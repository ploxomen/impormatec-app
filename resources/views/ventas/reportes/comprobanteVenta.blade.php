<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Comprobante de venta</title>
</head>
<body>
    @include('intranet.ventas.reportes.estilos')
    @include('intranet.ventas.reportes.header')
    <table>
        <tr>
            <td><b>Tipo Comprobante:</b> {{$ventas->comprobantes->comprobante}}</td>
        </tr>
        <tr>
            <td><b>N° Comprobante:</b> {{$ventas->serieComprobante . " - " . $ventas->numeroComprobante}}</td>
        </tr>
        <tr>
            <td><b>Fecha de Compra:</b> {{date("d/m/Y",strtotime($ventas->fechaVenta))}}</td>
        </tr>
        <tr>
            <td><b>Cliente:</b> {{$ventas->clientes->nombreCliente}} </td>
        </tr>
    </table>
    <div class="separador"></div>
    <table style="width: 350px;"> 
        <tr>
            <td style="width: 30px;">C.</td>
            <td style="width: 200px;">Descripción</td>
            <td style="width: 50px;">Precio</td>
            <td style="width: 50px;">Total</td>
        </tr>
    </table>
    <div class="separador"></div>
    <table style="width: 350px; font-size: 13px;">
        @foreach ($ventas->detalleVentas as $dv)
            <tr>
                <td style="width: 30px;">{{$dv->cantidad}}</td>
                <td style="width: 200px;">{{$dv->nombreProducto}}</td>
                <td style="width: 50px;">S/ {{$dv->costo}}</td>
                <td style="width: 50px;">S/ {{$dv->importe}}</td>
            </tr>
        @endforeach
    </table>
    <div class="separador"></div>
    <table>
        <tr>
            <td><b>SUBTOTAL:</b></td>
            <td>S/ {{$ventas->subTotal - $ventas->igvTotal}}</td>
        </tr>
        <tr>
            <td><b>I.G.V:</b></td>
            <td>S/ {{$ventas->igvTotal}}</td>
        </tr>
        <tr>
            <td><b>DESCUENTO:</b></td>
            <td>- S/ {{$ventas->descuentoTotal}}</td>
        </tr>
        <tr>
            <td><b>ENVÍO:</b></td>
            <td>S/ {{$ventas->envio}}</td>
        </tr>
        <tr>
            <td><b>TOTAL A PAGAR:</b></td>
            <td>S/ {{$ventas->total}}</td>
        </tr>
    </table>
    <div class="separador"></div>
    <table>
        <tr>
            <td><b>N° PRODUCTOS:</b></td>
            <td>{{$ventas->detalleVentas->count()}}</td>
        </tr>
        <tr>
            <td><b>EFECTIVO:</b></td>
            <td>S/ {{$ventas->montoPagado}}</td>
        </tr>
        <tr>
            <td><b>VELTO:</b></td>
            <td>S/ {{$ventas->vuelto}}</td>
        </tr>
    </table>
    <div class="separador"></div>
    <h3 class="text-center">!GRACIAS POR SU COMPRA!</h3>
</body>
</html>