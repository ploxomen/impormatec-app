<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE DE PRODUCTOS {{date('d/m/Y')}}</title>
</head>
<body>
    @include('helper.headerFooterPdfVertical')
    <h1 class="text-center" style="font-size: 20px;">REPORTE DE PRODUCTOS</h1>
    @include('productos.reportes.productosCompartido', ['productos' => $productos,'incluirMoneda' => true])
</body>
</html>