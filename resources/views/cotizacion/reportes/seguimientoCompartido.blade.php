<table class="tabla-precio" style="width: 100%;" border="1">
    <thead>
        <tr>
            <th>N° COTIZACION</th>
            <th>F. EMISION</th>
            <th>F. VENCIMIENTO</th>
            <th>PORCENTAJE</th>
            <th>CLIENTE</th>
            <th>RESPONSABLE</th>
            <th>SUBTOTAL</th>
            <th>DESC.</th>
            <th>I.G.V</th>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cotizaciones as $cotizacion)
            @php
                $moneda = $incluirMoneda ? ($cotizacion->tipoMoneda === 'USD' ? '$ ' : 'S/ ') : '';
            @endphp
            <tr>
                <td>{{str_pad($cotizacion->id,5,'0',STR_PAD_LEFT)}}</td>
                <td>{{$cotizacion->fechaCotizada}}</td>
                <td>{{$cotizacion->fechaFinCotizada}}</td>
                <td>{{$cotizacion->porcentaje_actual}}</td>
                <td>{{$cotizacion->nombreCliente}}</td>
                <td>{{$cotizacion->atendidoPor}}</td>
                <td>{{$moneda.$cotizacion->importeTotal}}</td>
                <td>{{$moneda.$cotizacion->descuentoTotal}}</td>
                <td>{{$moneda.$cotizacion->igvTotal}}</td>
                <td>{{$moneda.$cotizacion->total}}</td>
            </tr>
        @endforeach
    </tbody>
</table>