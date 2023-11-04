@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/ordenServicio/compartidoOs.js?v1.1"></script>
    <script src="/ordenServicio/generarInforme.js?v1.5"></script>
    <title>Modificar Informe</title>
@endsection
@section('body')
    <style>
        .posicion-visible{
            position: sticky;
            top: -20px;
            background: var(--color-principal);
            z-index: 100;
            color: #ffffff;
        }
        .img-guias {
            display: block;
            width: 200px;
            min-width: 200px;
            object-fit: contain;
            height: 100px;
            margin: auto;
        }
        .contenido-img{
            position: relative;
        }
        .contenido-img button{
            position: absolute;
            top: 0;
            right: 15px;
        }
        .pagination .page-link{
            border-color: var(--activo-li-navegacion) !important;
            color: var(--activo-li-navegacion) !important;
        }
        .pagination .page-link:focus{
            box-shadow: 0 0 0 0.2rem rgba(29, 87, 2, 0.25);
        }
        .pagination .active .page-link{
            background-color: var(--activo-li-navegacion) !important;
            color: #ffffff !important;

        }
    </style>
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
        @include('ordenesServicio.compartidoInforme',['botonGenerar' => false])
    </section>
    @if (!is_null($ordenServicio))
        @include('ordenesServicio.modales.agregarSeccion')
    @endif
@endsection