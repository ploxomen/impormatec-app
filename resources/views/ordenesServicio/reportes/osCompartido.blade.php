<table class="tabla-precio" style="width: 100%;" border="1">
    <thead>
        <tr>
            <th>N° OS</th>
            <th>F. EMISION</th>
            <th>CLIENTE</th>
            <th>SUBTOTAL</th>
            <th>DESC.</th>
            <th>I.G.V</th>
            <th>GASTOS ADICIONALES</th>
            <th>GASTOS CAJA</th>
            <th>TOTAL VENTA</th>
            <th>COSTO TOTAL</th>
            <th>UTILIDAD</th>
            <th>ESTADO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ordenesServicios as $ordenServicio)
            @php
                $moneda = $incluirMoneda ? ($ordenServicio->tipoMoneda === 'USD' ? '$ ' : 'S/ ') : '';
                $nombresEstados = ['Anulado' => 0,'Generado' => 1,'Informado' => 2,'Pendiente OS' => 3,'Con OS' => 4];
                $keyEstado = array_search($ordenServicio->estado,$nombresEstados);
            @endphp
            <tr>
                <td>{{$ordenServicio->nroOs}}</td>
                <td>{{$ordenServicio->fechaOs}}</td>
                <td>{{$ordenServicio->nombreCliente}}</td>
                <td>{{$moneda.$ordenServicio->importe}}</td>
                <td>{{$moneda.$ordenServicio->descuento}}</td>
                <td>{{$moneda.$ordenServicio->igv}}</td>
                <td>{{$moneda.$ordenServicio->adicional}}</td>
                <td>{{$moneda.$ordenServicio->gasto_caja}}</td>
                <td>{{$moneda.$ordenServicio->total}}</td>
                <td>{{$moneda.$ordenServicio->costo_total}}</td>
                <td>{{$moneda.$ordenServicio->utilidad}}</td>
                <td>{{$keyEstado === false ? 'Sin estado' : $keyEstado}}</td>
            </tr>
        @endforeach
    </tbody>
</table>