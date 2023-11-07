@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/ordenServicio/compartidoOs.js?v1.1"></script>
    <link rel="stylesheet" href="/ordenServicio/informe.css?v1.1">
    <script src="/ordenServicio/generarInforme.js?v1.5"></script>
    <title>Modificar Informe</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/notas.png" alt="Imagen de un libro de notas" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Modificar Informe</h4>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                <strong>Cliente: </strong>
                <span>{{$ordenServicio->cliente->nombreCliente}}</span>
            </div>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                <strong>Orden de Servicio: </strong>
                <span>{{str_pad($ordenServicio->id,5,'0',STR_PAD_LEFT)}}</span>
            </div>
            <div class="col-12 col-md-6 col-lg-4 col-xl-4 form-group">
                <strong>Responsable: </strong>
                <span>{{empty($ordenServicio->usuario_informe) ? 'No definido' :  $ordenServicio->usuario->nombres . ' ' . $ordenServicio->usuario->apellidos}}</span>
            </div>
        </div>
        @include('ordenesServicio.compartidoInforme',['botonGenerar' => false,'firmas' => $firmasUsuarios])
    </section>
    @if (!is_null($ordenServicio))
        @include('ordenesServicio.modales.agregarSeccion')
    @endif
@endsection