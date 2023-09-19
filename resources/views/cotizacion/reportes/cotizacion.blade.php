<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cotización</title>
</head>
<body>
    <style>
        table{
            border-collapse: collapse;
        }
        .text-right{
            text-align: right;
        }
        .text-center{
            text-align: center;
        }
        .text-justify{
            text-align: justify;
        }
        @page{
            font-family: 'Courier New', Courier, monospace;
            margin-top: 130px;
            /* font-size: 20px; */
        }
        .nota{
            font-size: 16px;
        }
        header{
            position: absolute;
            top: -130px;
            left: 0;
            font-size: 13px;
            border-bottom: 2px solid #1F2B53;
            padding-bottom: 5px;
            font-family: Arial, Helvetica, sans-serif;
            color: #1F2B53;
        }
        .tabla-precio{
            font-size: 14px;
            margin-bottom: 20px;
        }
        .tabla-precio td,
        .tabla-precio th{
            padding: 8px 5px;
        }
        .tabla-precio th{
            background: rgb(223, 223, 223);
        }
    </style>
    <header>
        <table>
            <tr>
                <td>
                    <p class="text-center">
                        <small>{{$configuracion->where('descripcion','direccion')->first()->valor}}</small>
                        <br>
                        <small>{{$configuracion->where('descripcion','telefono')->first()->valor}}</small>
                        <br>
                        <small>{{Auth::user()->celular}}</small>
                        <br>
                        <small>{{Auth::user()->correo}}</small>
                    </p>
                </td>
                <td style="width: 150px;">

                </td>
                <td>
                    <img src="{{public_path("img/logo.png")}}" alt="logo de impormatec" width="300px">
                </td>
            </tr>
        </table>
    </header>

    <table border="1">
        <tr>
            <td style="width: 490px;">
                <strong>{{$nombreDia}}</strong>
            </td>
            <td style="width: 120px;"> 
                <strong>Cotización</strong>
            </td>
            <td class="text-center">
                <strong>{{str_pad($cotizacion->id,5,'0',STR_PAD_LEFT)}}</strong>
            </td>
        </tr>
        <tr>
            <td>
                <strong>{{$cliente->nombreCliente}}</strong>
            </td>
            <td>
                <strong>Mes</strong>
            </td>
            <td class="text-center">
                <strong>{{mb_convert_case($nombreMes, MB_CASE_TITLE, "UTF-8")}}</strong>
            </td>
        </tr>
        <tr>
            <td>
                <strong>LIMA - PERU</strong>
            </td>
            <td>
                <strong>Año</strong>
            </td>
            <td class="text-center">
                <strong>{{date('Y',strtotime($cotizacion->fechaCotizacion))}}</strong>
            </td>
        </tr>
    </table>
    <p>
        <strong>
            <u>Presente</u>
        </strong>
        <span>.-</span>
        <br>
        <strong>
            Atención: {{$representante->nombreContacto}}<br>
            CEL: {{$representante->numeroContacto}}
        </strong>
    </p>
    <p>
        <strong>
            Referencia: {{$cotizacion->referencia}}
        </strong>
    </p>
    <p class="text-justify">
        Estimados señores,<br>
        Por medio de la presente reciban nuestro cordial saludo, en atención a la
        solicitud de la referencia les hacemos llegar nuestra cotización de acuerdo con
        lo solicitado.
    </p>
    <p class="text-center">
        <strong>
            <u>PRECIO</u>
        </strong>
    </p>
    @php
        $conbinarCeldaDescuento = !empty($cotizacion->descuentoTotal) && $cotizacion->descuentoTotal > 0 ? 5 : 4;
    @endphp
    <table border="1" class="tabla-precio">
        <thead>
            <tr>
                <th>ITEM</th>
                <th style="width: {{!empty($cotizacion->descuentoTotal) && $cotizacion->descuentoTotal > 0 ? '265px' : '375px'}};">DESCRIPCIÓN</th>
                <th>CANT.</th>
                <th style="width: 100px;">P. UNIT</th>
                @if (!empty($cotizacion->descuentoTotal) && $cotizacion->descuentoTotal > 0)
                    <th style="width: 100px;">DESC.</th>
                @endif
                <th style="width: 100px;">P. TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($servicios as $keyServicio => $servicio)
                <tr>
                    <td class="text-center">{{$keyServicio + 1}}</td>
                    <td>{{$servicio->servicios->servicio}}</td>
                    <td class="text-center">{{$servicio->cantidad}}</td>
                    <td>{{$moneda . ' '. number_format($servicio->precio,2)}}</td>
                    @if (!empty($cotizacion->descuentoTotal) && $cotizacion->descuentoTotal > 0)
                        <td>- {{$moneda . ' '. number_format($servicio->descuento,2)}}</td>
                    @endif
                    <td>{{$moneda . ' '. number_format($servicio->total,2)}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="{{$conbinarCeldaDescuento}}" class="text-right">
                    <b>SUBTOTAL</b>
                </td>
                <td>{{$moneda . ' '. number_format($cotizacion->importeTotal,2)}}</td>
            </tr>
            <tr>
                @if (!empty($cotizacion->descuentoTotal) && $cotizacion->descuentoTotal > 0)
                    <td colspan="{{$conbinarCeldaDescuento}}" class="text-right">
                        <b>DESCUENTO</b>
                    </td>
                    <td>{{$moneda . ' '. number_format($cotizacion->descuentoTotal,2)}}</td>
                @endif
            </tr>
            <tr>
                <td colspan="{{$conbinarCeldaDescuento}}" class="text-right">
                    <b>I.G.V</b>
                </td>
                <td>{{$moneda . ' '. number_format($cotizacion->igvTotal,2)}}</td>
            </tr>
            <tr>
                <td colspan="{{$conbinarCeldaDescuento}}" class="text-right">
                    <b>TOTAL</b>
                </td>
                <td>{{$moneda . ' '. number_format($cotizacion->total,2)}}</td>
            </tr>
        </tfoot>
    </table>
    @if ($cotizacion->reporteDetallado === 1)
        <strong><u>DESCRIPCIÓN DEL SERVICIO</u></strong>
        <ol>
            @foreach ($servicios as $servicio)
                <li>{{$servicio->servicios->servicio}}</li>
                <table border="1" style="font-size: 16px; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th>ITEM</th>
                            <th style="width: 280px;">DESCRIPCION</th>
                            <th>CANT.</th>
                            <th>P. UNIT</th>
                            <th>DESC.</th>
                            <th>P. TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servicio->productos as $keyProducto => $producto)
                            <tr>
                                <td>{{$keyProducto + 1}}</td>
                                <td>{{$producto->producto->nombreProducto}}</td>
                                <td>{{$producto->cantidad}}</td>
                                <td>S/ {{$producto->costo}}</td>
                                <td>- S/ {{$producto->descuento}}</td>
                                <td>S/ {{$producto->total}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </ol>
    @endif
    @if (!empty($cotizacion->textoNota))
        {!! $cotizacion->textoNota !!}
    @endif
    @if ($cotizacion->reportePreCotizacion === 1)
        <h4 class="text-center"><u>REPORTE PRE COTIZACION</u></h4>
        <div>
            {!! $reportePreCotizacion['html'] !!}
        </div>
        @if (count($reportePreCotizacion['imagenes']))
            <p>
                <strong>
                    <u>IMAGENES DETALLADAS:</u>
                </strong>
            </p>
            <table border="1">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>IMAGEN</th>
                        <th>DESCRIPCION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportePreCotizacion['imagenes'] as $keyImagen => $imagen)
                        <tr>
                            <td>{{$keyImagen + 1}}</td>
                            <td style="padding: 5px;"><img src="{{storage_path('app/imgCotizacionPre/' . $imagen->url_imagen)}}" alt="Imagen del informe" width="200px"></td>
                            <td style="width: 400px;">{{$imagen->descripcion}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        
    @endif
</body>
</html>