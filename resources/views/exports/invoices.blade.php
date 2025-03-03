<table>
    <thead>
        <tr style="background-color: green; color: white;">
            <th></th>
            <th>FECHA REGISTRO</th>
            <th>FECHA DOCUMTO.</th>
            <th>FECHA VENCMTO.</th>
            <th>TD</th>
            <th>SERIE</th>
            <th>CORRELATIVO</th>
            <th>CODIGO CLIENTE</th>
            <th>RAZON SOCIAL DEL CLIENTE</th>
            <th>CENTRO DE COSTO</th>
            <th>BASE IMPONIBLE</th>
            <th>Monto</th>
            <th>PDF</th>
            <th>XML</th>
            <th>CDR</th>
            <th>Sunat Response</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <td>1</td>
                <td>{{ $invoice->fechaEmision->format('d/m/Y') }}</td>
                <td>{{ $invoice->fechaEmision->format('d/m/Y') }}</td>
                <td>{{ $invoice->fecVencimiento->format('d/m/Y') }}</td>
                <td>
                    @if($invoice->tipoDoc == '03')
                        BV
                    @elseif($invoice->tipoDoc == '01')
                        FT
                    @else
                        CC
                    @endif
                </td>
                <td>{{ $invoice->serie }}</td>
                <td>{{ $invoice->correlativo }}</td>
                <td>{{ $invoice->client['numDoc'] ?? 'S/D' }}</td>
                <td>{{ $invoice->client['rznSocial'] ?? 'S/D' }}</td>
                <td>{{ $invoice->client['rznSocial'] ?? 'S/D' }}</td>
                <td>
                    020109</td>
                <td>{{ $invoice->tipoMoneda . ' ' . number_format($invoice->mtoImpVenta, 2) }}</td>
                <td>{{ $invoice->pdf_path }}</td>
                <td>{{ $invoice->xml_path }}</td>
                <td>{{ $invoice->cdr_path }}</td>
                <td>{{ $invoice->sunatResponse['success'] ? 'Aceptado' : 'Rechazado' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
