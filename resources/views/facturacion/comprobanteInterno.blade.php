<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="library/bootstrap/bootstrap.min.css">
    <title>{{ $titulo }}</title>
</head>
<body>
    <style>
        @page{
            margin: 30px 40px;
        }
        .alineado {
            vertical-align: middle !important;
        }
        .tabla-cabecera p {
            font-weight: 400;
        }
        .tabla-cabecera-border {
            border: 3px solid #1A284A !important;
        }
        .tabla-cabecera tr, .tabla-cabecera th, .tabla-cabecera td {
            border: none !important;
        }
        .tabla-cabecera-border td {
            border: 1px solid #b5b5b5 !important;
        }
        .tabla-pequena{
            font-size: 14px !important;
        }
        .anulado{
            position: fixed;
            top: 50%;
            z-index: -1;
            left: 50%;
            font-size: 80px;
            font-weight: bold;
            transform: translate(-50%,-50%) rotate(-45deg);
            font-family: Arial, Helvetica, sans-serif;
            color: rgb(255, 194, 194);
        }
    </style>
    @if ($comprobanteInterno->estado === 0)
        <div class="anulado">
            <span>ANULADO</span>
        </div>
    @endif
    <table class="table table-bordered tabla-cabecera tabla-cabecera-border">
        <thead>
            <tr>
                <th scope="col" class="alineado">
                    <img src="img/logo.png" alt width="45%">
                    <p style="font-size: 18px; font-weight: 600; margin-top: 1rem; margin-bottom: 0;">{{$configuracion->where('descripcion','razon_social_largo')->first()->valor}}</p>
                    <p style="font-weight: 400; margin: 0;">{{$configuracion->where('descripcion','direccion')->first()->valor}}</p>
                </th>
                <th scope="col" colspan="1" style="vertical-align: middle;">
                    <div class="numeracion"
                        style="text-align: center; border: 2px solid #000; padding: .5rem 1rem;">
                        <h5 style="margin-bottom: .1rem; color: #1A284A; padding: .2rem;"><b>COMPROBANTE INTERNO</b></h5>
                        <b style="margin-bottom: .3rem; display: block;">FECHA: {{date('d/m/Y',$strFechaPago)}}</b>
                        <h5 style="color: red;"><b>CI001 - {{$numeroPago}}</b></h5>
                    </div>
                </th>
            </tr>
        </thead>
    </table>
    <table class="table table-sm tabla-cabecera tabla-pequena">
        <tr>
            <th style="width: 200px;">Cliente:</th>
            <td>{{$comprobanteInterno->cliente}}</td>
        </tr>
        <tr>
            <th>Tipo Documento:</th>
            <td>{{$comprobanteInterno->tipo_documento}}</td>
        </tr>
        <tr>
            <th>Número Documento:</th>
            <td>{{$comprobanteInterno->numero_documento}}</td>
        </tr>
        <tr>
            <th>Tipo de moneda:</th>
            <td>{{$comprobanteInterno->tipo_moneda === 'PEN' ? 'SOLES' : 'DOLARES'}}</td>
        </tr>
        <tr>
            <th>Dirección:</th>
            <td>{{$comprobanteInterno->direccion}}</td>
        </tr>
    </table>
    <table class="table table-sm table-bordered tabla-pequena">
        <thead>
            <tr>
                <th class="text-center">CANT.</th>
                <th class="text-center">DESCRIPCION</th>
                <th class="text-center">P.U.</th>
                @if ($comprobanteInterno->descuento > 0)
                    <th class="text-center">DESC.</th>
                @endif
                <th class="text-center">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comprobanteInterno->detalleComprobantes as $detalle)
                <tr>
                    <td class="text-center">{{$detalle->cantidad}}</td>
                    <td>{{$detalle->descripcion}}</td>
                    <td class="text-right">{{$detalle->precio}}</td>
                    @if ($comprobanteInterno->descuento > 0)
                    <td class="text-right">{{$detalle->descuento}}</td>
                    @endif
                    <td class="text-right">{{$detalle->total}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @empty (!$comprobanteInterno->observaciones)
        <p>
            <strong>Observaciones: </strong> {{$comprobanteInterno->observaciones}}
        </p>
    @endempty
    <table class="table table-sm tabla-cabecera tabla-pequena">
        <tr>
            <td><strong>SON: {{$comprobanteInterno->monto_letras}}</strong></td>
            <td class="text-right"><strong>SUBTOTAL</strong></td>
            <td class="text-right"><strong>{{$comprobanteInterno->subtotal}}</strong></td>
        </tr>
        @if ($comprobanteInterno->descuento > 0)
        <tr>
            <td colspan="2" class="text-right"><strong>DESCUENTO</strong></td>
            <td class="text-right"><strong>{{$comprobanteInterno->descuento}}</strong></td>
        </tr>
        @endif
        <tr>
            <td colspan="2" class="text-right"><strong>I.G.V</strong></td>
            <td class="text-right"><strong>{{$comprobanteInterno->igv_total}}</strong></td>
        </tr>
        <tr>
            <td colspan="2" class="text-right"><strong>TOTAL</strong></td>
            <td class="text-right">
                <strong>{{$comprobanteInterno->tipo_moneda === 'PEN' ? 'S/ ' : '$ '}}{{$comprobanteInterno->total}}</strong>
            </td>
        </tr>
    </table>
</body>
</html>
