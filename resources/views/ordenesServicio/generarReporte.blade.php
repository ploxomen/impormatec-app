@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/ordenServicio/compartidoOs.js?v1.1"></script>
    <link rel="stylesheet" href="/ordenServicio/informe.css?v1.1">
    <script src="/ordenServicio/generarInforme.js?v1.5"></script>
    <title>Generar nuevo informe</title>
@endsection
@section('body')
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
        @include('ordenesServicio.compartidoInforme',['botonGenerar' => true,'firmas' => $firmasUsuarios])
    </section>
    @if (!is_null($ordenServicio))
        @include('ordenesServicio.modales.agregarSeccion')
    @endif
@endsection