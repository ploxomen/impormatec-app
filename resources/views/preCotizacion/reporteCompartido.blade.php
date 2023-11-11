<h2 class="text-center">
    <u>REPORTE DE VISITA</u>
</h2>
<p style="margin-bottom:0px; ">
    <b>Cliente:</b>
    <span>{{$preCotizacion->cliente->nombreCliente}}</span><br>
    <b>Dirección:</b>
    <span>{{$preCotizacion->cliente->usuario->direccion}}</span><br>
    @php
        $datosContacto = $preCotizacion->contactos()->first();
    @endphp
    @if (!empty($datosContacto))
        <b>Nombre contacto:</b>
        <span>{{$preCotizacion->contactos()->first()->contacto->nombreContacto}}</span><br>
        <b>Número de contacto:</b>
        <span>{{$preCotizacion->contactos()->first()->contacto->numeroContacto}}</span>
    @endif
</p>
<div>
    {!! $reportePreCotizacionHtml !!}
</div>
<div class="saltopagina"></div>
@foreach ($preCotizacion->secciones as $ks => $seccion)
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
                $path = storage_path('app/preCotizacionImgSeccion/' . $imagen->url_imagen);
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
    @if (($ks + 1) !== count($preCotizacion->secciones))
        <div class="saltopagina"></div>
    @endif
@endforeach
@php
    $firmaTecnico = $preCotizacion->tecnicoResponsable;
    $firma = null;
    if(!$firmaTecnico->isEmpty()){
        $firma = $firmaTecnico->first()->usuario->firma;
    }
@endphp
@if (!empty($firma))
    <img src="{{$firma}}" style="position: absolute; right: 5px; bottom: 20px;" alt="Firma del técnico" width="150px" height="120px">
@endif
