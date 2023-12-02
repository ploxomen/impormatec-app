<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE DE GARANTIAS {{date('d/m/Y')}}</title>
</head>
<body>
    @include('helper.headerFooterPdfVertical')
    <h1 class="text-center" style="font-size: 20px;">SEGUIMIENTO DE GARANTIAS DE PRODUCTOS Y/O SERVICIOS {{$fechaInicioReporte}} - {{$fechaFinReporte}}</h1>
    @include('cotizacion.reportes.garantiaCompartido', ['garantias' => $garantias])
</body>
</html>