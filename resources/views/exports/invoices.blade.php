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
        @foreach($invoices as $invoice)
            <tr>
                <td>1</td>
                <td>{{ $invoice->fechaEmision->format('d/m/Y') }}</td>
                <td>{{ $invoice->fechaEmision->format('d/m/Y') }}</td>
                {{-- <td>{{ $invoice->fecVencimiento->format('d/m/Y') }}</td> --}}
                <td></td>
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
                <td>
                    020103</td>
                    <td>{{ $invoice->mtoOperGravadas ?? 'S/D' }}</td>

                <td>{{ $invoice->mtoIGV ?? 'S/D' }}</td>

                <td>{{ $invoice->mtoOperInafectas ?? 'S/D' }}</td>
                <td>{{ $invoice->mtoImpVenta ?? 'S/D' }}</td>
                    <td></td>
                <td></td>
               <td></td>
                <td>{{$invoice->numDocfectado}}</td>
                <td>{{$invoice->numDocfectado}}</td>


            </tr>
        @endforeach
    </tbody>
</table>
