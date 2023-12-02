<table class="tabla-precio" style="width: 100%;" border="1">
    <thead>
        <tr>
            <th>N° COTIZACION</th>
            <th>N°. OS</th>
            <th>F. FIN GARANTIA</th>
            <th>CLIENTE</th>
            <th>TIPO</th>
            <th>DESCRIPCION</th>
            <th>CANTIDAD</th>
            <th>ESTADO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($garantias as $garantia)
            <tr>
                <td>{{$garantia->nroCotizacion}}</td>
                <td>{{$garantia->nroOs}}</td>
                <td>{{$garantia->fechaFinGarantia}}</td>
                <td>{{$garantia->nombreCliente}}</td>
                <td>{{$garantia->tipo}}</td>
                <td>{{$garantia->servicio}}</td>
                <td>{{$garantia->cantidad}}</td>
                <td>{{$garantia->vencimientoEstado}}</td>
            </tr>
        @endforeach
    </tbody>
</table>