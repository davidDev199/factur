<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnexosExport implements FromView, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $query = Invoice::query()
            ->where('company_id', session('company')->id)
            ->where('production', session('company')->production);
        foreach ($this->filters as $filter => $value) {
            if ($filter === 'tipoDoc' && $value) {
                $query->where('tipoDoc', $value);
            } elseif ($filter === 'fecha' && $value) {
                $query->whereDate('fechaEmision', '>=', $value['minDate'])
                      ->whereDate('fechaEmision', '<=', $value['maxDate']);
            }
        }

        return view('exports.anexos', [
            'invoices' => $query->get()
        ]);
    }

    public function headings(): array
    {
        return [
            'Fecha Emisión',
            'Tipo Documento',
            'Serie',
            'Correlativo',
            'Cliente',
            'Monto',
            'PDF',
            'XML',
            'CDR',
            'Sunat Response'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text and green background
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '80DAEB']]],
        ];
    }
}
