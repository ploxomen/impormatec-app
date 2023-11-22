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
            margin: 30px 50PX;
        }
        .alineado {
            vertical-align: middle !important;
        }
        p {
            font-weight: 400;
        }
        table {
            border: 3px solid #1A284A !important;
        }
        tr, th {
            border: none !important;
        }
        td {
            border: 1px solid #b5b5b5 !important;
        }
    </style>
    <table class="table table-bordered">
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
                        <h5 style="margin-bottom: .1rem; color: #1A284A; padding: .2rem;"><b>RECIBO DE PAGO</b></h5>
                        <b style="margin-bottom: .3rem; display: block;">FECHA: {{$fechaFormato}}</b>
                        <h5 style="color: red;"><b>N° {{$numeroPago}}</b></h5>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>

                <td scope="row" colspan="2">

                    <h5 style="font-weight: 700; margin: 0; text-align: center;">Monto: <span
                            style="font-weight: 400;">{{$cuota->ordenServicio->tipoMoneda === 'PEN' ? 'S/.' : '$'}} {{number_format($cuota->monto_pagado,2)}}</span></h5>
                </td>
            </tr>
            <tr>
                <td scope="row" colspan="2">
                    <h5 style="font-weight: 700; margin: 0; text-align: center;">Recibí de: <span
                            style="font-weight: 400;">{{$cuota->ordenServicio->cliente->nombreCliente}}</span></h5>
                </td>
            </tr>
            <tr>
                <td scope="row" colspan="2">
                    <h5 style="font-weight: 700; margin: 0; text-align: center;">La cantidad de: <span
                            style="font-weight: 400;">{{$nombreMonto}}</span></h5>
                </td>
            </tr>
            <tr>
                <td scope="row" colspan="2">
                    <h5 style="font-weight: 700; margin: 0; text-align: center;">Por concepto de: <span
                            style="font-weight: 400;">{{$cuota->descripcion_pagada}}</span></h5>
                </td>
            </tr>
            <tr style="border: none;">
                <td class="text-center" colspan="2">
                    @if (!empty($cuota->id_firmante_pago))
                        <img src="{{$cuota->usuario->firma}}" alt="Firma del usuario" width="200px" height="150px">
                    @endif
                    <h5 class="text-center py-3"><i>{{$fechaTexto}}</i></h5>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
