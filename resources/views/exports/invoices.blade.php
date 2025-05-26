<table>
    <thead>
        <tr style="background-color: green; color: white;">
            <th></th>
            <th>TIPO DOC</th>
            <th>FECHA REGISTRO</th>
            <th>FECHA DOCUMTO.</th>
            <th>FECHA VENCMTO.</th>
            <th>TD</th>
            <th>SERIE</th>
            <th>CORRELATIVO</th>
            <th>CODIGO CLIENTE</th>
            <th>RAZON SOCIAL DEL CLIENTE</th>
            <th>ESTADO</th>
            <th>MONEDA</th>
            <th>TIPO DE CAMBIO</th>
            <th>CENTRO DE COSTO</th>
            <th>BASE IMPONIBLE</th>
            <th>IGV</th>
            <th>NO GRAVADOS</th>
            <th>IMPORTE TOTAL</th>
            <th>TIPO CAMBIO</th>
            <th>FECHA DOC. REF.</th>
            <th>TD REF</th>
            <th>SERIE REF.</th>
            <th>NUMERO REF.</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoices as $invoice)
            <tr>
                <td>1</td>
                <td>{{ $invoice->tipoDoc }}</td>
                <td>{{ $invoice->fechaEmision->format('d/m/Y') }}</td>
                <td>{{ $invoice->fechaEmision->format('d/m/Y') }}</td>
                {{-- <td>{{ $invoice->fecVencimiento->format('d/m/Y') }}</td> --}}
                <td></td>
                <td>
                    @if ($invoice->tipoDoc == '03')
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
                <td>
                    @if ($invoice->voided)
                        Anulado
                    @elseif (($invoice->sunatResponse['cdrResponse']['code'] ?? -1) == 0)
                        Aprobado
                    @else
                        Rechazado
                    @endif
                </td>
                <td>{{ $invoice->tipoMoneda }}</td>
                <td>{{ $invoice->tipo_cambio }}</td>
                <td>
                    020103</td>
                <td>{{ $invoice->mtoOperGravadas ?? 'S/D' }}</td>

                <td>{{ isset($invoice->mtoIGV) ? number_format($invoice->mtoIGV, 2) : 'S/D' }}</td>
                <td>{{ isset($invoice->mtoOperInafectas) ? number_format($invoice->mtoOperInafectas, 2) : 'S/D' }}</td>
                <td>{{ isset($invoice->mtoImpVenta) ? number_format($invoice->mtoImpVenta, 2) : 'S/D' }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ explode('-', $invoice->numDocfectado)[0] }}</td>
                <td>{{ explode('-', $invoice->numDocfectado)[1] ?? '' }}</td>


            </tr>
        @endforeach
    </tbody>
</table>
