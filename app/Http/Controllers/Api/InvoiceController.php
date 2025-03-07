<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Company;
use App\Models\Invoice;
use App\Services\Sunat\DocumentService;
use App\Services\Sunat\InvoiceService;
use App\Services\Sunat\Util;
use App\Services\Sunat\UtilService;
use App\Traits\Sunat\DataTrait;
use Greenter\Report\XmlUtils;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;

class InvoiceController extends Controller
{
    use DataTrait;

    public $company;

    public function __construct()
    {
        $company = auth('sanctum')->user();
        $this->company = $company;
    }

    public function send(InvoiceRequest $request)
    {

        if (!$request->input('correlativo')) {
            // Obtener los datos de la tabla branch_company_document
            $branch = DB::table('branch_company_document')
                ->where('branch_id', 1)
                ->where('company_id', 1)
                ->first();

            // Obtener la última factura o boleta registrada
            $lastInvoice = Invoice::where('company_id', 1)
                ->where('serie',$request->serie)
                ->orderByDesc('correlativo') // Ordenar de mayor a menor
                ->first();

            // Calcular el nuevo correlativo
            $newCorrelativo = $lastInvoice
                ? $lastInvoice->correlativo + 1 // Si existe, incrementar
                : $branch->correlativo;        // Si no existe, usar el base de branch

            // Asignar el nuevo correlativo al request
            $request->merge([
                'correlativo' => $newCorrelativo
            ]);

        }
        $data = $request->all();
        $data['company_id'] = $this->company->id;
        $data['production'] = $this->company->production;

        $data = $this->getData($data);

        $invoice = Invoice::create($data);

        $util = new UtilService($this->company);
        $document = new DocumentService();

        $invoiceGreenter = $document->getInvoice($invoice);

        //Directorio
        $directory = $this->company->production ? 'sunat/' : 'sunat/beta/';

        //Generar XML
        $xml = $util->getXmlSigned($invoiceGreenter);
        $invoice->hash = $util->getHashSign($xml);

        //Guardar XML
        $invoice->xml_path = $directory . 'xml/' . $invoiceGreenter->getName() . '.xml';
        Storage::put($invoice->xml_path, $xml);

        //PDF
        $pdf = $util->getReportPdf($invoiceGreenter, $invoice->hash);
        $invoice->pdf_path = $directory . 'cpe/' . $invoiceGreenter->getName() . '.pdf';
        Storage::put($invoice->pdf_path, $pdf);
        $invoice->save();

        //Enviar a SUNAT
        try {

            $see = $util->getSee();
            $result = $see->sendXmlFile($xml);

            if (!$result->isSuccess()) {

                $invoice->sunatResponse = $util->getErrorResponse($result);
                $invoice->save();

                return response()->json([
                    'sunatResponse' => $invoice->sunatResponse,
                ]);
            }

            // Guardamos el CDR
            $invoice->cdr_path = $directory . 'cdr/R-' . $invoiceGreenter->getName() . '.zip';
            Storage::put($invoice->cdr_path, $result->getCdrZip());

            //Leer el CDR
            $cdr = $result->getCdrResponse();
            $invoice->sunatResponse = $util->readCdr($cdr);
            $invoice->save();

            return response()->json([
                'sunatResponse' => $invoice->sunatResponse,
                'pdf_url' => Storage::url($invoice->pdf_path),
                'xml_url' => Storage::url($invoice->xml_path),
                'cdr_url' => Storage::url($invoice->cdr_path),
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage(),
            ], 500);

        }

    }

    public function xml(InvoiceRequest $request)
    {
        $data = $this->getData($request->all());
        $data['company'] = [
            'ruc' => $this->company->ruc,
            'razonSocial' => $this->company->razonSocial,
            'nombreComercial' => $this->company->nombreComercial,
            'address' => [
                'direccion' => $this->company->direccion,
                'distrito' => $this->company->district->name,
                'provincia' => $this->company->district->province->name,
                'departamento' => $this->company->district->province->department->name,
                'ubigueo' => $this->company->ubigeo
            ]
        ];

        $util = new UtilService($this->company);
        $document = new DocumentService();

        $invoiceGreenter = $document->getInvoice($data);

        //Generar XML
        $xml = $util->getXmlSigned($invoiceGreenter);
        $hash = $util->getHashSign($xml);

        return response()->json([
            'xml' => $xml,
            'hash' => $hash,
        ]);
    }

    public function pdf(InvoiceRequest $request)
    {
        $data = $this->getData($request->all());
        $data['company'] = [
            'ruc' => $this->company->ruc,
            'razonSocial' => $this->company->razonSocial,
            'nombreComercial' => $this->company->nombreComercial,
            'address' => [
                'direccion' => $this->company->direccion,
                'distrito' => $this->company->district->name,
                'provincia' => $this->company->district->province->name,
                'departamento' => $this->company->district->province->department->name,
                'ubigueo' => $this->company->ubigeo
            ]
        ];

        $util = new UtilService($this->company);
        $document = new DocumentService();

        $invoiceGreenter = $document->getInvoice($data);

        //Generar XML
        $xml = $util->getXmlSigned($invoiceGreenter);
        $hash = $util->getHashSign($xml);

        return response()->json([
            'html' => $util->getReportHtml($invoiceGreenter, $hash),
        ]);
    }
}
