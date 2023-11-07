<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$tituloPdf}}</title>
</head>
<body>
    @include('helper.headerFooterPdfVertical')
    <style>
        .lista-principal{
            list-style: none;
            padding-left: 0;
        }
        p{
            margin-bottom:3px; 
        }
        .lista-principal > li{
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
    @foreach ($ordenServicio as $keyServicio=>$servicio)
        @php
            $fechaTime = 0;
            $fechaTerminoLargo = 'No se establecio la fecha de termino';
            $fechaNormal = "";
            if(!empty($servicio->fecha_termino)){
                $fechaTime = strtotime($servicio->fecha_termino);
                $fechaTerminoLargo = $utilitarios->obtenerFechaLarga($fechaTime);
                $fechaNormal = date('d/m/Y',$fechaTime);
            }
            $nroOrdenServicio = str_pad($servicio->id_orden_servicio,5,'0',STR_PAD_LEFT);
            $nroInforme = str_pad($servicio->id,5,'0',STR_PAD_LEFT);
        @endphp 
        <table border="1" class="contenido-cabecera">
            <tr>
                <td style="text-align: center; font-size: 25px; width: 400px;">
                    <span>informe técnico de {{$ordenServicioDetalle->cliente->nombreCliente}}</span>
                </td>
                <td style="width: 270px; font-size: 13px; line-height: 1.5;">
                    <p style="margin: 0;">
                        <span>fecha: {{empty($fechaNormal) ? date('d/m/y') : $fechaNormal}}</span><br>
                        <span>cod. proy. inf.:{{$nroInforme}}</span><br>
                        <span>cod. os.: {{$nroOrdenServicio}}</span><br>
                        <span>{{$ordenServicioDetalle->cliente->usuario->direccion}}</span><br>
                        <span>tel. cli.: {{$ordenServicioDetalle->cliente->usuario->telefono}}</span><br>
                        @if (!empty($ordenServicioDetalle->cliente->usuario->celular))
                            <span>cel. cli.: {{$ordenServicioDetalle->cliente->usuario->celular}}</span><br>
                        @endif
                        <span>nom. contac.: {{$servicio->cotizacionServicio->cotizacion->representantes->nombreContacto}}</span><br>
                        <span>num. contac.: {{$servicio->cotizacionServicio->cotizacion->representantes->numeroContacto}}</span><br>
                    </p>
                </td>
            </tr>
        </table>
        <div class="titulo-general">
            <h1 class="text-center">INFORME TÉCNICO DE {{$servicio->cotizacionServicio->servicios->servicio}}</h1>
        </div>
        <div class="saltopagina"></div>
        <p>
            El día {{$fechaTerminoLargo}} se culminó con los trabajos de {{$servicio->cotizacionServicio->servicios->servicio}}
        </p>
        <p class="parrafo">
            Con la finalidad de ofrecerle los detalles durante la ejecución del servicio, se ha generado el informe técnico correspondiente. Líneas abajo se incluye fotografías que evidencian la objetividad del servicio generado.
        </p>
        <div>
            <div>
                {!!str_replace(['../../../../imagenesEditor/','../../../imagenesEditor/'],'imagenesEditor/',$servicio->objetivos)!!}
            </div>
            <div>
                {!!str_replace(['../../../../imagenesEditor/','../../../imagenesEditor/'],'imagenesEditor/',$servicio->acciones)!!}
            </div>
            <div>
                {!!str_replace(['../../../../imagenesEditor/','../../../imagenesEditor/'],'imagenesEditor/',$servicio->descripcion)!!}
            </div>
            <div class="saltopagina"></div>
            <h2 style="font-size:14px;">Álbum de imágenes</h2>
            @foreach ($servicio->secciones as $ks => $seccion)
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
                    @foreach ($seccion->imagenes as $keyImagen => $imagen)
                        @php
                            $path = storage_path('app/informeImgSeccion/' . $imagen->url_imagen);
                            if (!\File::exists($path) || empty($imagen->url_imagen)) {
                                $path = storage_path('app/productos/sin-imagen.png');
                            }
                        @endphp
                        @if ($inicioContador === 1)
                            <tr>
                        @endif
                        <td style="width:{{$ancho}}px;vertical-align: top !important;" class="text-center">
                            <img src="{{$path}}" alt="{{$imagen->descripcion}}" width="{{$ancho - 30}}px" height="{{$ancho - 30}}px"/>
                            <h4 class="descripcion-img">
                                {{$imagen->descripcion}}
                            </h4>
                        </td>
                        @if ($inicioContador === $columna || ($columna !== $inicioContador && ($keyImagen + 1) === count($seccion->imagenes)))
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
                @if (($ks + 1) !== count($servicio->secciones))
                    <div class="saltopagina"></div>
                @endif
            @endforeach
            <div style="page-break-inside:avoid;">
                {!! str_replace(['../../../../imagenesEditor/','../../../imagenesEditor/'],'imagenesEditor/',$servicio->conclusiones_recomendaciones)  !!}
            </div>
        </div>
        @if (!empty($servicio->id_firma_profesional))
            <img src="{{$servicio->usuario->firma}}" style="position: absolute; right: 5px; bottom: 20px;" alt="Firma del usuario" width="200px" height="150px">
        @endif
        @if (($keyServicio + 1) !== $ordenServicio->count())
        <div class="saltopagina"></div>
        @endif
    @endforeach
</body>
</html>