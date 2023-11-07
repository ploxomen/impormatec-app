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
@if (count($reportePreCotizacionImagenes))
    <div class="saltopagina"></div>
    <p>
        <strong>
            <u>IMAGENES DETALLADAS:</u>
        </strong>
    </p>
    <table>
        @php
            $columna = $preCotizacion->columnas;
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
            @foreach ($reportePreCotizacionImagenes as $keyImagen => $imagen)
                @php
                    $pathReporte = storage_path('app/imgCotizacionPre/' . $imagen->url_imagen);
                    $pixelReporte = 160;
                    if (!\File::exists($pathReporte) || empty($imagen->url_imagen)) {
                        $pathReporte = storage_path('app/productos/sin-imagen.png');
                    }
                @endphp
                @if ($inicioContador === 1)
                    <tr>
                @endif
                <td style="width:{{$ancho}}px;vertical-align: top !important;" class="text-center">
                    <img src="{{$pathReporte}}" alt="Imagen del informe" width="{{$ancho - 30}}px" height="{{$ancho - 30}}px"/>
                    <p>
                        {{$imagen->descripcion}}
                    </p>
                </td>
                @if ($inicioContador === $columna || ($columna !== $inicioContador && ($keyImagen + 1) === count($reportePreCotizacionImagenes)))
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
@endif
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
