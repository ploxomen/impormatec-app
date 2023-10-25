<h2 class="text-center">
    <u>REPORTE PRE-COTIZACION</u>
</h2>
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
    <table class="tabla-precio">
        <tbody>
            @php
                $columna = 0;
            @endphp
            @foreach ($reportePreCotizacionImagenes as $keyImagen => $imagen)
                @php
                    $pathReporte = storage_path('app/imgCotizacionPre/' . $imagen->url_imagen);
                    $pixelReporte = 160;
                    if (!\File::exists($pathReporte) || empty($imagen->url_imagen)) {
                        $pathReporte = storage_path('app/productos/sin-imagen.png');
                    }
                    $columna++;
                @endphp
                @if ($columna === 1)
                    <tr>
                @endif
                    <td style="padding: 5px; text-align: center; vertical-align: top !important;">
                        <img src="{{$pathReporte}}" alt="Imagen del informe" width="{{$pixelReporte}}px" height="{{$pixelReporte}}px">
                        <p>
                            {{$imagen->descripcion}}
                        </p>
                    </td>
                @if ($columna === 4 || ($columna !== 4 && ($keyImagen + 1) === count($reportePreCotizacionImagenes)))
                    </tr>
                    @php
                        $columna = 0;
                    @endphp
                @endif
            @endforeach
        </tbody>
    </table>
@endif