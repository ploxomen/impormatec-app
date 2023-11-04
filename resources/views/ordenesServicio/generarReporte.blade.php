@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/ordenServicio/compartidoOs.js?v1.1"></script>
    <script src="/ordenServicio/generarInforme.js?v1.5"></script>
    <title>Generar nuevo informe</title>
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
                <h4 class="text-center text-primary my-2">Generar nuevo informe</h4>
            </div>
        </div>
        <form class="form-group" method="GET" action="{{route('informe.generar')}}">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Filtros</legend>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                        <label for="cbPreCotizacion" class="col-form-label col-form-label-sm">Clientes</label>
                        <select name="cliente" id="cbClientes" required class="form-control select2-simple" data-placeholder="Seleccione un cliente">
                            <option value=""></option>
                            @foreach ($clientes as $cliente)
                                <option {{$cliente->id == $idCliente ? 'selected' : ''}} value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                        <label for="cbOrdenServicio" class="col-form-label col-form-label-sm">Ordenes de servicio</label>
                        <select name="ordenServicio" id="cbOrdenServicio" required class="form-control select2-simple" data-placeholder="Seleccione una orden de servicio">
                            <option value=""></option>
                            @foreach ($listaOs as $os)
                                <option value="{{$os->id}}" {{$os->id == $idOs ? 'selected' : ''}}>{{$os->nroOs}}</option>
                            @endforeach                         
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
            </fieldset>
        </form>
        @include('ordenesServicio.compartidoInforme',['botonGenerar' => true])
    </section>
    @if (!is_null($ordenServicio))
        @include('ordenesServicio.modales.agregarSeccion')
    @endif
@endsection