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
            position: fixed;
            top: -130px;
            left: 0;
            font-size: 13px;
            border-bottom: 2px solid #1F2B53;
            padding-bottom: 5px;
            font-family: Arial, Helvetica, sans-serif;
            color: #1F2B53;
        }
        footer{
            position: fixed;
            bottom: -30px;
            left: 0;
            font-size: 13px;
            border-top: 2px solid #1F2B53;
            padding-top: 15px;
            font-family: Arial, Helvetica, sans-serif;
            color: #1F2B53;
        }
        .saltopagina{page-break-after:always;}
        .tabla-precio{
            font-size: 13px;
            margin-bottom: 20px;
        }
        .tabla-precio td,
        .tabla-precio th{
            padding: 8px 5px;
        }
        .tabla-precio th{
            background: rgb(223, 223, 223);
        }
        footer a{
            color: #ffff;
            padding: 3px;
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
    <footer style="width: 700px">
        <table style="width: 100%;">
            <tr>
                <td>
                    <img src="{{public_path("img/logo.png")}}" alt="logo de impormatec" width="100px">
                </td>
                <td style="width: 340px;">
                </td>
                <td class="text-right">
                    @php
                        $facebook = $configuracion->where('descripcion','red_social_facebook')->first();
                        $instagram = $configuracion->where('descripcion','red_social_instagram')->first();
                        $tiktok = $configuracion->where('descripcion','red_social_tiktok')->first();
                        $twiter = $configuracion->where('descripcion','red_social_twitter')->first();
                    @endphp
                    @empty(!$facebook->valor)
                        <a href="{{$facebook->valor}}">
                            <img src="{{public_path('img/logos/facebook.png')}}" width="20px" alt="logo de Facebook">
                        </a>
                    @endempty
                    @empty(!$instagram->valor)
                        <a href="{{$instagram->valor}}">
                            <img src="{{public_path('img/logos/instagram.png')}}" width="20px" alt="logo de Instagram">
                        </a>
                    @endempty
                    @empty(!$tiktok->valor)
                        <a href="{{$tiktok->valor}}">
                            <img src="{{public_path('img/logos/tik-tok.png')}}" width="20px" alt="logo de TikTok">
                        </a>
                    @endempty
                    @empty(!$twiter->valor)
                        <a href="{{$twiter->valor}}">
                            <img src="{{public_path('img/logos/twiter.png')}}" width="20px" alt="logo de Twiter">
                        </a>
                    @endempty
                </td>
            </tr>
        </table>
    </footer>
    <table>
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
            @foreach ($productosServicios as $keyServicioProducto => $servicioProducto)
                <tr>
                    <td class="text-center">{{$keyServicioProducto + 1}}</td>
                    <td>{{$servicioProducto->tipo == 'producto' ? $servicioProducto->productos->nombreProducto : $servicioProducto->servicios->servicio}}</td>
                    <td class="text-center">{{$servicioProducto->cantidad}}</td>
                    <td>{{$moneda . ''. number_format($servicioProducto->precio,2)}}</td>
                    @if (!empty($cotizacion->descuentoTotal) && $cotizacion->descuentoTotal > 0)
                        <td>- {{$moneda . ''. number_format($servicioProducto->descuento,2)}}</td>
                    @endif
                    <td>{{$moneda . ''. number_format($servicioProducto->total,2)}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="{{$conbinarCeldaDescuento}}" class="text-right">
                    <b>SUBTOTAL</b>
                </td>
                <td>{{$moneda . ''. number_format($cotizacion->importeTotal,2)}}</td>
            </tr>
            <tr>
                @if (!empty($cotizacion->descuentoTotal) && $cotizacion->descuentoTotal > 0)
                    <td colspan="{{$conbinarCeldaDescuento}}" class="text-right">
                        <b>DESCUENTO</b>
                    </td>
                    <td>{{$moneda . ''. number_format($cotizacion->descuentoTotal,2)}}</td>
                @endif
            </tr>
            <tr>
                <td colspan="{{$conbinarCeldaDescuento}}" class="text-right">
                    <b>I.G.V</b>
                </td>
                <td>{{$moneda . ''. number_format($cotizacion->igvTotal,2)}}</td>
            </tr>
            <tr>
                <td colspan="{{$conbinarCeldaDescuento}}" class="text-right">
                    <b>TOTAL</b>
                </td>
                <td>{{$moneda . ''. number_format($cotizacion->total,2)}}</td>
            </tr>
        </tfoot>
    </table>
    @if ($cotizacion->reporteDetallado === 1 && $productosServicios->where('tipo','servicio')->count() > 0)
    <p>
        <strong><u>DESCRIPCIÓN DEL SERVICIO</u></strong>
    </p>
        @php
            $index = 0;
        @endphp 
        @foreach ($productosServicios->where('tipo','servicio') as $servicio)
            @php
                $index++;
            @endphp    
            <p>
                <span>{{$index. '. ' . $servicio->servicios->servicio}}</span>
            </p>
            <table border="1" class="tabla-precio">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th style="width: {{$servicio->descuento > 0 ? '200px' : '310px'}};">DESCRIPCION</th>
                        <th>IMAGEN</th>
                        <th>CANT.</th>
                        <th style="width: 100px;">P. UNIT</th>
                        @if ($servicio->descuento > 0)
                            <th style="width: 100px;">DESC.</th>
                        @endif
                        <th style="width: 100px;">P. TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicio->productosCotizacion as $keyProducto => $producto)
                        @php
                            $path = storage_path('app/productos/' . $producto->producto->urlImagen);
                            if (!\File::exists($path) || empty($producto->producto->urlImagen)) {
                                $path = storage_path('app/productos/sin-imagen.png');
                            }
                        @endphp
                        <tr>
                            <td>{{$keyProducto + 1}}</td>
                            <td>{{$producto->producto->nombreProducto .' - ' . $producto->producto->descripcion}}</td>
                            <td><img src="{{$path}}" alt="" width="50px"></td>
                            <td>{{$producto->cantidad}}</td>
                            <td>{{$moneda . ''. number_format($producto->costo,2)}}</td>
                            @if ($servicio->descuento > 0)
                                <td>- {{$moneda . ''. number_format($producto->descuento,2)}}</td>
                            @endif
                            <td>{{$moneda . ''. number_format($producto->total,2)}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
    @if (!empty($cotizacion->textoNota))
        {!! $cotizacion->textoNota !!}
    @endif
    {!! $configuracion->where('descripcion','texto_datos_bancarios')->first()->valor !!}
    @if ($cotizacion->reportePreCotizacion === 1)
        <div class="saltopagina"></div>
        <h4 class="text-center"><u>REPORTE PRE-COTIZACION</u></h4>
        <div>
            {!! $reportePreCotizacion['html'] !!}
        </div>
        @if (count($reportePreCotizacion['imagenes']))
            <p>
                <strong>
                    <u>IMAGENES DETALLADAS:</u>
                </strong>
            </p>
            <table border="1" class="tabla-precio">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th style="width: 220px;">IMAGEN</th>
                        <th style="width: 420px;">DESCRIPCION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportePreCotizacion['imagenes'] as $keyImagen => $imagen)
                        @php
                            $pathReporte = storage_path('app/imgCotizacionPre/' . $imagen->url_imagen);
                            $pixelReporte = 200;
                            if (!\File::exists($pathReporte) || empty($imagen->url_imagen)) {
                                $pathReporte = storage_path('app/productos/sin-imagen.png');
                                $pixelReporte = 50;
                            }
                        @endphp    
                    <tr>
                            <td>{{$keyImagen + 1}}</td>
                            <td style="padding: 5px; text-align: center;"><img src="{{$pathReporte}}" alt="Imagen del informe" width="{{$pixelReporte}}px"></td>
                            <td>{{$imagen->descripcion}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
</body>
</html>