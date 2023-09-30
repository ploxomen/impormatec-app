<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orden de servicio</title>
</head>
<body>
    @include('helper.headerFooterPdfVertical')
    <h1 class="text-center">ORDEN DE SERVICIO</h1>
    <p>
        <b>Fecha:</b>
        <span>{{$nombreDia}}</span>
        <br>
        <b>No. Orden de Servicio:</b>
        <span>{{$codigoOrdenServicio}}</span>
    </p>
    <table border="1" class="tabla-precio" style="width: 100%;">
        <thead>
            <tr>
                <th colspan="2">CLIENTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 25%;">
                    <b>Razón Social: </b>
                </td>
                <td>
                    <span>{{$cliente->nombreCliente}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <b>RUC/DNI: </b>
                </td>
                <td>
                    <span>{{$cliente->usuario->nroDocumento}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Dirección: </b>
                </td>
                <td>
                    <span>{{$cliente->usuario->direccion}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Teléfono: </b>
                </td>
                <td>
                    <span>{{$cliente->usuario->telefono}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Correo electrónico: </b>
                </td>
                <td>
                    <span>{{$cliente->usuario->correo}}</span>
                </td>
            </tr>
        </tbody>
    </table>
    <table border="1" class="tabla-precio">
        <thead>
            <tr>
                <th>CANTIDAD</th>
                <th>DESCRIPCION</th>
                <th>PRECIO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($ordenServicioDetalle as $detalle)
                
                @php
                    if($detalle->tipoServicioProducto === "producto"){
                        $cantidad = $detalle->cotizacionOsProductos->cantidad;
                        $descripcion = $detalle->cotizacionOsProductos->productos->nombreProducto;
                        $subtotal = $detalle->cotizacionOsProductos->total;
                    }else{
                        $cantidad = $detalle->cotizacionOsServicios->cantidad;
                        $descripcion = $detalle->cotizacionOsServicios->servicios->servicio;
                        $subtotal = $detalle->cotizacionOsServicios->total;
                    }
                    $total += $subtotal;
                @endphp
                <tr>
                    <td>{{$cantidad}}</td>
                    <td>{{$descripcion}}</td>
                    <td>{{$moneda . '' .number_format($subtotal,2)}}</td>
                </tr>
            @endforeach
            @foreach ($ordenServicio->costosAdicionales as $costoAdicional)
            @php
                $total += $costoAdicional->total;
            @endphp
            <tr>
                <td>{{$costoAdicional->cantidad}}</td>
                <td style="width: 500px;">{{$costoAdicional->descripcion}}</td>
                <td style="width: 105px;">{{$moneda . '' .number_format($costoAdicional->total,2)}}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                $igv = $total * 0.18;
                $subtotal = $total - $igv;
            @endphp
            <tr>
                <th colspan="2" class="text-right">SUBTOTAL</th>
                <th>{{$moneda . '' . number_format($subtotal,2) }}</th>
            </tr>
            <tr>
                <th colspan="2" class="text-right">IGV 18%</th>
                <th>{{$moneda . '' . number_format($igv,2) }}</th>
            </tr>
            <tr>
                <th colspan="2" class="text-right">TOTAL</th>
                <th>{{$moneda . '' . number_format($total,2) }}</th>
            </tr>
        </tfoot>
    </table>
    <strong>OBSERVACIONES:</strong>
    <div>
        {!! $ordenServicio->observaciones !!}
    </div>
</body>
</html>