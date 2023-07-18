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
            font-size: 20px;
        }
        .nota{
            font-size: 16px;
        }
    </style>
    <table>
        <tr>
            <td style="width: 400px;"></td>
            <td>
                <img src="{{public_path("img/logo.png")}}" alt="logo de impormatec" width="300px">
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="width: 400px;">
                <strong>{{$nombreDia}}</strong>
            </td>
            <td style="width: 200px;" class="text-right"> 
                <strong>Cotización</strong>
            </td>
            <td style="width: 100px;" class="text-center">
                <strong>{{str_pad($cotizacion->id,5,'0',STR_PAD_LEFT)}}</strong>
            </td>
        </tr>
        <tr>
            <td>
                <strong>{{$cliente->nombreCliente}}</strong>
            </td>
            <td class="text-right">
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
            <td class="text-right">
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
    </p>
    <p>
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
    <table border="1" style="font-size: 16px; margin-bottom: 20px;">
        <thead>
            <tr>
                <th>ITEM</th>
                <th style="width: 310px;">DESCRIPCIÓN</th>
                <th>CANT.</th>
                <th>P. UNIT</th>
                <th>DESC.</th>
                <th>P. TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($servicios as $keyServicio => $servicio)
                <tr>
                    <td>{{$keyServicio + 1}}</td>
                    <td>{{$servicio->servicios->servicio}}</td>
                    <td>{{$servicio->cantidad}}</td>
                    <td>S/ {{$servicio->costo}}</td>
                    <td>- S/ {{$servicio->descuento}}</td>
                    <td>S/ {{$servicio->total}}</td>
                </tr>
            @endforeach
        </tbody>
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
    <p>
        <strong>
            <u>NOTA:</u>
        </strong>
    </p>
    <ul class="nota">
        <li><strong>Forma pago:</strong> 50% con la confirmación, resto contra entrega.</li>
        <li><strong>Garantía:</strong> 6 meses.</li>
        <li><strong>Validez Oferta:</strong> 15 día(s).</li>
        <li><strong>Moneda:</strong> Sóles.</li>
        <li><strong>Nota 1:</strong> Los costos incluyen IGV -Todos nuestros servicios se formalizan con boleta de venta o factura
            de venta.</li>
        <li><strong>Nota 2:</strong> Todo trabajo adicional que se pudiera encontrar se comunicará anticipadamente.</li>
        <li><strong>Nota 3:</strong> Nuestro personal cuenta con EPP ́S (casco, uniforme completo, guantes, lentes, zapatos de
            ✓ seguridad, etc). Así mismo, cuenta con SCTR (salud y pensión).</li>
        <li><strong>Nota 4:</strong> Nuestro personal cumple con el protocolo COVID19</li>
    </ul>
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