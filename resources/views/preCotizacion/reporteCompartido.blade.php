<h2 class="text-center">
    <u>REPORTE PRE-COTIZACION</u>
</h2>
<div>
    {!! $reportePreCotizacionHtml !!}
</div>
@if (count($reportePreCotizacionImagenes))
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
            @foreach ($reportePreCotizacionImagenes as $keyImagen => $imagen)
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