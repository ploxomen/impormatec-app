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
    <h1 class="text-center">{{$titulo}}</h1>
    <table class="tabla-precio" border="1">
        <thead>
            <tr>
                <th>N°</th>
                <th>RESPONSABLE</th>
                <th>TIPO</th>
                <th>FECHA HR. INICIO</th>
                <th>FECHA HR. FIN</th>
                <th>ACTIVIDAD</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($actividades as $keyActividad=>$actividad)
                <tr>
                    <td>{{$keyActividad + 1}}</td>
                    <td>{{$actividad->nombres . ' ' . $actividad->apellidos}}</td>
                    <td class="text-center">{{$actividad->tipo}}</td>
                    <td class="text-center">{{$actividad->fecha_hr_inicio}}</td>
                    <td class="text-center">{{$actividad->fecha_hr_fin}}</td>
                    <td>{{$actividad->tarea}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
</body>
</html>