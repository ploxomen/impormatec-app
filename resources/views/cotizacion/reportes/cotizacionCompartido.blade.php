<table class="tabla-precio" style="width: 100%;" border="1">
    <thead>
        <tr>
            <th>N° COTIZACION</th>
            <th>N° PRE COTI.</th>
            <th>F. EMISION</th>
            <th>F. VENCIMIENTO</th>
            <th>CLIENTE</th>
            <th>RESPONSABLE</th>
            <th>ESTADO</th>
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
                $nombresEstados = ['Anulado' => 0,'Generado' => 1,'Aprobado' => 2,'Pendiente OS' => 3,'Con OS' => 4];
                $keyEstado = array_search($cotizacion->estado,$nombresEstados);
            @endphp
            <tr>
                <td>{{$cotizacion->nroCotizacion}}</td>
                <td>{{$cotizacion->nroPreCotizacion}}</td>
                <td>{{$cotizacion->fechaCotizada}}</td>
                <td>{{$cotizacion->fechaFinCotizada}}</td>
                <td>{{$cotizacion->nombreCliente}}</td>
                <td>{{$cotizacion->atendidoPor}}</td>
                <td>{{$keyEstado === false ? 'Sin estado' : $keyEstado}}</td>
                <td>{{$moneda.$cotizacion->importeTotal}}</td>
                <td>{{$moneda.$cotizacion->descuentoTotal}}</td>
                <td>{{$moneda.$cotizacion->igvTotal}}</td>
                <td>{{$moneda.$cotizacion->total}}</td>
            </tr>
        @endforeach
    </tbody>
</table>