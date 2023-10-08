<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$tituloPdf}}</title>
</head>
<body>
    @include('helper.headerFooterPdfVertical')
    <style>
        ol{
            list-style: none;
            padding-left: 0;
        }
        ol p{
            margin:0; 
        }
        ol li{
            margin:0 0 10px 0;
        }
        .estilos-subitulos{
            font-size: 17px;
            margin: 0 0 5px 0;
            color: #1F2B53;
        }
        .descripcion-img{
            font-size: 15px;
            color: #1F2B53;
        }
        .titulo-seccion{
            margin: 5px 0 20px 0;
            color: #1F2B53;
        }
        .titulo-general{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -90%);
            color: #1F2B53;
            font-weight: bold;
            width: 100%;
            text-transform: uppercase;
        }
        .contenido-cabecera{
            font-weight: bold;
            text-transform: uppercase;
        }
        .contenido-cabecera td{
            border: 6px solid rgb(137, 192, 54);
            padding: 5px;
        }
    </style>
    <div>
        <table border="1" class="contenido-cabecera">
            <tr>
                <td style="text-align: center; font-size: 25px; width: 400px;">
                    <span>informe técnico de {{$ordenServicioDetalle->cliente->nombreCliente}}</span>
                </td>
                <td style="width: 270px; font-size: 13px; line-height: 1.5;">
                    <p>
                        <span>fecha: {{$fechaNormal}}</span><br>
                        <span>cod. proy. inf.:{{$nroInforme}}</span><br>
                        <span>cod. os.: {{$nroOrdenServicio}}</span><br>
                        <span>{{$ordenServicioDetalle->cliente->usuario->direccion}}</span><br>
                        <span>tel.: {{$ordenServicioDetalle->cliente->usuario->telefono}}</span><br>
                        @if (!empty($ordenServicioDetalle->cliente->usuario->celular))
                            <span>cel.: {{$ordenServicioDetalle->cliente->usuario->celular}}</span>
                        @endif
                    </p>
                </td>
            </tr>
        </table>
    </div>
    <div class="titulo-general">
        <h1 class="text-center">INFORME TÉCNICO DE {{$ordenServicio->cotizacionServicio->servicios->servicio}}</h1>
    </div>
    <div class="saltopagina"></div>
    <p>
        El día {{$fechaTerminoLargo}} se culminó con los trabajos de {{$ordenServicio->cotizacionServicio->servicios->servicio}}
    </p>
    <p>
        Con la finalidad de ofrecerle los detalles durante la ejecución del servicio, se ha generado el informe técnico correspondiente. Líneas abajo se incluye fotografías que evidencian la objetividad del servicio generado.
    </p>
    <ol style="margin: 10px 0;">
        <li>
            <h2 class="estilos-subitulos">1. Objetivos</h2>
            {!!$ordenServicio->objetivos!!}
        </li>
        <li>
            <h2 class="estilos-subitulos">2. Actuaciones realizadas</h2>
            {!!$ordenServicio->acciones!!}
        </li>
        <li>
            <h2 class="estilos-subitulos">3. Descripción clara y precisa de la forma técnica e instrumentos utilizados</h2>
            {!!$ordenServicio->descripcion!!}
        </li>
        <li>
            <h2 class="estilos-subitulos">4. Álbum de imágenes</h2>
            @foreach ($ordenServicio->secciones as $ks => $seccion)
                <div>
                    <h3 class="text-center titulo-seccion">
                    {{$seccion->titulo}}
                    </h3>
                    @if ($seccion->imagenes->count() === 0)
                        <h4 class="text-center">NO SE ASIGNARON FOTOS A ESTA SECCION</h4>
                        @php
                            continue;
                        @endphp
                    @endif
                    @php
                        $columna = $seccion->columnas;
                        $ancho = 100;
                        switch ($columna) {
                            case 2:
                                $ancho = 350;
                            break;
                            case 3:
                                $ancho = 235;
                            break;
                            case 4:
                                $ancho = 175;
                            break;
                        }
                        $inicioContador = 1;
                    @endphp
                    <table>
                        @foreach ($seccion->imagenes as $imagen)
                            @php
                                $path = storage_path('app/informeImgSeccion/' . $imagen->url_imagen);
                                if (!\File::exists($path) || empty($imagen->url_imagen)) {
                                    $path = storage_path('app/productos/sin-imagen.png');
                                }
                            @endphp
                            @if ($inicioContador === 1)
                                <tr>
                            @endif
                            <td style="width:{{$ancho}}px;" class="text-center">
                                <div>
                                    <img src="{{$path}}" alt="{{$imagen->descripcion}}" width="{{$ancho - 30}}px">
                                </div>
                                <h4 class="descripcion-img">
                                    {{$imagen->descripcion}}
                                </h4>
                            </td>
                            @if ($inicioContador === $columna)
                                </tr>
                                @php
                                    $inicioContador = 1;
                                @endphp
                            @else
                                @php
                                    $inicioContador++;
                                @endphp
                            @endif
                        @endforeach
                    </table>
                </div>
            @endforeach
        </li>
        <li style="page-break-inside:avoid;">
            <h2 class="estilos-subitulos">5. Conclusiones y Recomendaciones</h2>
            {!!$ordenServicio->conclusiones_recomendaciones!!}
        </li>
    </ol>
    
</body>
</html>