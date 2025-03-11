<table>
    <thead>
        <tr style="background-color: blue; color: white;">
            <th>TIPO ANEXO</th>
            <th>CODIGO ANEXO</th>
            <th>RUC DEL ANEXO</th>
            <th>DENOMINACION</th>
            <th>DIRECCION</th>
            <th>TIPO DOCUMENTO</th>
            <th>NRO DOCUMENTO IDENTIDAD</th>
            <th>TIPO PERSONA</th>
            <th>APELLIDO PATERNO</th>
            <th>APELLIDO MATERNO</th>
            <th>PRIMER NOMBRE</th>
            <th>SEGUNDO NOMBRE</th>
            <th>NACIONALIDAD</th>
            <th>SEXO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <td>02</td>
                <td>{{$invoice->client['numDoc']}}</td>
                <td>
                    @if($invoice->client['tipoDoc'] == 6)
                        {{$invoice->client['numDoc']}}
                    @else
                        {{''}}
                    @endif
                </td>
                <td>
                    @if($invoice->client['tipoDoc'] == 6)
                        {{$invoice->client['rznSocial']}}
                    @else
                        {{''}}
                    @endif
                </td>
                <td></td>

                <td>{{$invoice->client['tipoDoc']}}</td>
                <td>{{$invoice->client['numDoc']}}</td>

                <td>
                @if($invoice->client['tipoDoc'] == 1)
                    PERSONA NATURAL
                @elseif($invoice->client['tipoDoc'] == 6)
                    PERSONA JURIDICA
                @else
                    NO DOMICIALIADO
                @endif
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>1</td>
            </tr>
        @endforeach
    </tbody>
</table>
