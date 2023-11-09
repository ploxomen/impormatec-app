@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/preCotizacion/compartido.js"></script>
    <script src="/tecnico/primeraVisitaPreCoti.js?v1.5"></script>
    <link rel="stylesheet" href="/tecnico/primeraVisitaPreCoti.css?v1.5">
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
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap: 5px;">
            <h5 class="text-primary">
                <i class="fas fa-caret-right"></i>
                <span>Todas las visitas por fechas<span>
            </h5>
            <a href="{{route('descargarArchivo',['FORMATO_UNICO_DE_VISITAS.pdf'])}}" download="FORMATO_UNICO_DE_VISITAS" class="btn btn-sm btn-primary">
                <i class="fas fa-download"></i>
                <span>Descargar formato de visitas</span>
            </a>
        </div>
        
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
    @include('ordenesServicio.modales.agregarSeccion')
@endsection