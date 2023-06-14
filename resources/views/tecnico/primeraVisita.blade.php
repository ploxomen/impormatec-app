@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/tecnico/primeraVisitaPreCoti.js?v1.1"></script>
    <link rel="stylesheet" href="/tecnico/primeraVisitaPreCoti.css?v1.2">
    <title>Visitas Pre - Cotización</title>
@endsection
@section('body')
    <section class="px-3 container">
        <div class="my-4">
            <h4 class="text-center text-primary my-2">Visitas Pre - Cotización</h4>
        </div>
        <section class="my-4">
            <div class="d-flex m-auto gap filtro-fecha">
                <button class="btn btn-primary fechaCambio" data-tipo="atras">
                    <i class="fas fa-caret-left"></i>
                </button>
                <input type="date" id="txtFecha" class="form-control" value="{{date('Y-m-d')}}">
                <button class="btn btn-primary fechaCambio" data-tipo="adelante">
                    <i class="fas fa-caret-right"></i>
                </button>
            </div>
        </section>
        <h5 class="text-primary">
            <i class="fas fa-caret-right"></i>
            Todas las visitas por fecha
        </h5>
        <div class="mb-3 d-flex flex-wrap" style="gap:10px;" id="contenidoFiltro"></div>
        <h5 class="text-primary">
            <i class="fas fa-caret-right"></i>
            Lista de visitas 
        </h5>
        <div class="my-2" id="contenidoNoVisitas" hidden>
            <span>No se encontraron visitas para la fecha seleccionada</span>
        </div>
        <div class="form-row my-2 lista-visitas" id="contenidoVisitas">
        </div>
    </section>
    @include('tecnico.modales.reportePrimeraVisita')
@endsection