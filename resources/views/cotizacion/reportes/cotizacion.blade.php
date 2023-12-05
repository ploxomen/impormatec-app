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
        .tabla-detalle{
            width: 100px;
            border: 1px solid black; 
            background: rgb(223, 223, 223);
            padding: 8px 5px;
            font-size: 13px;
        }
    </style>
    @include('helper.headerFooterPdfVertical')
    <table>
        <tr>
            <td style="width: 470px;">
                <strong>{{$nombreDia}}</strong>
            </td>
            <td class="tabla-detalle"> 
                <strong>Cotización</strong>
            </td>
            <td class="text-center" style="border: 1px solid black; width: 120px;font-size: 13px;">
                <strong>{{str_pad($cotizacion->id,5,'0',STR_PAD_LEFT)}}</strong>
            </td>
        </tr>
        <tr>
            <td rowspan="2">
                <strong>{{$cliente->nombreCliente}}</strong><br>
                <span>{{$cotizacion->direccionCliente}}</span><br>
                <strong>LIMA - PERU</strong>
            </td>
            <td class="tabla-detalle">
                <strong>Mes</strong>
            </td>
            <td class="text-center" style="border: 1px solid black;font-size: 13px;">
                <strong>{{mb_convert_case($nombreMes, MB_CASE_TITLE, "UTF-8")}}</strong>
            </td>
        </tr>
        <tr>
            <td class="tabla-detalle">
                <strong>Año</strong>
            </td>
            <td class="text-center" style="border: 1px solid black;font-size: 13px;">
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
        </strong><br>
        <strong>Garantía:</strong>
        <span>{{$cotizacion->mesesGarantia}} meses</span><br>
        <strong>Tipo moneda:</strong>
        <span>{{$cotizacion->tipoMoneda == 'PEN' ? 'Soles' : 'Dólares'}}</span>
    </p>
    <p class="text-justify">
        Estimados señores,<br>
        Por medio de la presente reciban nuestro cordial saludo, en atención a la
        solicitud de la referencia les hacemos llegar nuestra cotización de acuerdo con
        lo solicitado.
    </p>
    @php
        $conbinarCeldaDescuento = !empty($cotizacion->descuentoTotal) && $cotizacion->descuentoTotal > 0 ? 5 : 4;
    @endphp
    <table class="tabla-precio tabla-moderna">
        <caption class="text-center">
            <p style="margin: 0 0 10px 0 !important;">
                <span style="font-weight: bold; text-decoration: underline;">PRECIO</span>
            </p>
        </caption>
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
                <td colspan="{{$conbinarCeldaDescuento}}" class="text-right" style="border: none !important;">
                    <b>SUBTOTAL</b>
                </td>
                <td>{{$moneda . ''. number_format($cotizacion->importeTotal,2)}}</td>
            </tr>
            <tr>
                @if (!empty($cotizacion->descuentoTotal) && $cotizacion->descuentoTotal > 0)
                    <td colspan="{{$conbinarCeldaDescuento}}" class="text-right" style="border: none !important;">
                        <b>DESCUENTO</b>
                    </td>
                    <td>- {{$moneda . ''. number_format($cotizacion->descuentoTotal,2)}}</td>
                @endif
            </tr>
            @if (intval($cotizacion->incluirIGV) === 1)
                <tr>
                    <td colspan="{{$conbinarCeldaDescuento}}" class="text-right" style="border: none !important;">
                        <b>I.G.V</b>
                    </td>
                    <td>{{$moneda . ''. number_format($cotizacion->igvTotal,2)}}</td>
                </tr>
            @endif
            <tr>
                <td colspan="{{$conbinarCeldaDescuento}}" class="text-right" style="border: none !important;">
                    <b>TOTAL</b>
                </td>
                <td>{{$moneda . ''. number_format($cotizacion->total,2)}}</td>
            </tr>
        </tfoot>
    </table>
    @if ($cotizacion->reporteDetallado === 1 && $productosServicios->where('tipo','servicio')->count() > 0)
    @if ($productosServicios->count() > 5)
    <div class="saltopagina"></div>
    @endif
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
            <table border="1" class="tabla-precio">
                <caption style="text-align: left; margin-bottom: 10px;">{{$index. '. ' . $servicio->servicios->servicio}}</caption>
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
        {!! str_replace(['../../imagenesEditorCotizacion/'],'imagenesEditorCotizacion/',$cotizacion->textoNota) !!}
    @endif
    {!! str_replace(['../../imagenesEditorConfiguracion/'],'imagenesEditorConfiguracion/',$configuracion->where('descripcion','texto_datos_bancarios')->first()->valor) !!}
    @php
        $firma = auth()->user()->firma;
    @endphp
    @if (!empty($firma))
        <img src="{{$firma}}" style="position: absolute; right: 5px; bottom: 20px;" alt="Firma del usuario" width="150px" height="120px">
    @endif
    @if ($cotizacion->reportePreCotizacion === 1)
        <div class="saltopagina"></div>
        @include('preCotizacion.reporteCompartido',['preCotizacion' => $preCotizacion,'reportePreCotizacionHtml' => $reportePreCotizacion['html'], 'reportePreCotizacionImagenes' => $reportePreCotizacion['imagenes']])
    @endif
</body>
</html>