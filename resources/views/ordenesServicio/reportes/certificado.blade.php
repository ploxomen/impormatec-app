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
        body{
            font-family: Arial, Helvetica, sans-serif;
            margin-right: 40px;
            margin-left: 40px;
        }
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1000;
        }
        .watermark img {
            filter: brightness(10%); /* Cambia el valor para ajustar el brillo */
        }
        .center {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
    <div class="watermark">
        <div class="center">
            <img src="img/icono-30.png" style="width: 600px"/>
        </div>
    </div>
    <h1 class="text-center" style="font-size: 20px;">CERTIFICADO DE OPERATIVIDAD</h1>
    <p class="text-right">
        {{$certificado->lugar . ', ' . $certificado->fechaLarga}}
    </p>
    <p>
        <span>Señores:</span><br>
        <strong>{{$cliente->nombreCliente}}</strong><br>
        <span>{{$cliente->usuario->direccion}}</span><br>
        <span>{{$cliente->departamento . ' - ' . $cliente->pais->pais_espanish}}</span>
    </p>
    <p>
        <span>Asunto:</span><br>
        <strong>{{$certificado->asunto}}</strong>
    </p>
    <p class="text-justify">
        <strong>{{$configuracion->where('descripcion','razon_social_largo')->first()->valor}},</strong> con Nro. de <strong>R.U.C: {{$configuracion->where('descripcion','ruc')->first()->valor}}</strong> extiende el presente certificado de operatividad al cliente {{$cliente->nombreCliente}} ubicado en {{$direccionCliente}}.
    </p>
    <p>
        Con este documento se deja constancia lo siguiente:
    </p>
    {!! $certificado->descripcion !!}
    <p class="text-justify">
        El <strong>{{$certificado->ordenServicioCotizacion->usuario->nombres . ' ' . $certificado->ordenServicioCotizacion->usuario->apellidos}} con Nro. CIP {{$certificado->ordenServicioCotizacion->usuario->cip}}, </strong> quien se encuentra
        habilitado para ejercer la ingeniería en nuestro país, garantiza que las inspecciones y pruebas fueron realizadas bajo los estándares correspondientes.
    </p>
    <p class="text-justify">
        El certificado de operatividad tendrá una vigencia de <b>{{$certificado->ordenServicioCotizacion->cotizacionServicio->cotizacion->mesesGarantia}} meses</b>, siempre que no se realicen cambios y/o manipulaciones en el sistema evaluado.
    </p>
    @if (!empty($certificado->ordenServicioCotizacion->id_firma_profesional))
        <img src="{{$certificado->ordenServicioCotizacion->usuario->firma}}" style="position: absolute; right: 5px; bottom: 20px;" alt="Firma del usuario" width="200px" height="150px">
    @endif
</body>
</html>