<table class="tabla-precio" style="width: 100%;" border="1">
    <thead>
        <tr>
            <th>CODIGO</th>
            <th>NOMBRE PRODUCTO</th>
            <th>DESCRIPCION</th>
            <th>TIPO</th>
            <th>P.VENTA</th>
            <th>P.COMPRA</th>
            <th>UTILIDAD<br>%</th>
            <th>ESTADO</th>
            <th>ALMACEN</th>
            <th>STOCK</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($productos as $producto)
            @php
                $moneda = $incluirMoneda ? ($producto->tipoMoneda === 'USD' ? '$ ' : 'S/ ') : '';
                $totalAlmacen = $producto->almacenes->count();
                $filas = $totalAlmacen > 0 ? $totalAlmacen : 1;
            @endphp
            <tr>
                <td rowspan="{{$filas}}">{{str_pad($producto->id,5,'0',STR_PAD_LEFT)}}</td>
                <td rowspan="{{$filas}}">{{$producto->nombreProducto}}</td>
                <td rowspan="{{$filas}}">{{$producto->descripcion}}</td>
                <td rowspan="{{$filas}}">{{$producto->esIntangible === 0 ? 'Tangible' : 'Intangible'}}</td>
                <td rowspan="{{$filas}}">{{$moneda.$producto->precioVenta}}</td>
                <td rowspan="{{$filas}}">{{$moneda.$producto->precioCompra}}</td>
                <td rowspan="{{$filas}}">{{$producto->utilidad}}</td>
                <td rowspan="{{$filas}}">{{$producto->estado === 1 ? 'Vigente' : 'Descontinuado'}}</td>
                @if ($producto->esIntangible === 0 && $totalAlmacen > 0)
                    @foreach ($producto->almacenes as $key => $almacen)
                        @if($key > 0)
                            <tr>
                        @endif
                        <td>{{$almacen->nombre}}</td>
                        <td>{{$almacen->pivot->stock}}</td>
                        @if(($key + 1) !== $filas)
                            </tr>
                        @endif
                    @endforeach
                @else
                    <td> - </td>
                    <td> - </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>