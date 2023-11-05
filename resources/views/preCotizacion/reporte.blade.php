<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$titulo}}</title>
</head>
<body>
    @include('helper.headerFooterPdfVertical')
    @include('preCotizacion.reporteCompartido',['preCotizacion'=>$preCotizacion,'reportePreCotizacionHtml' => $preCotizacion->html_primera_visita, 'reportePreCotizacionImagenes' => $preCotizacion->img])
</body>
</html>