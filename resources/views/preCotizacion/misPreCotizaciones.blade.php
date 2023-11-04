@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <link rel="stylesheet" href="/tecnico/primeraVisitaPreCoti.css?v1.5">
    <script src="/preCotizacion/misPreCotizaciones.js"></script>
    <title>Mis Pre - Cotizaciones</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 450px;">
                <img src="/img/modulo/atencion-al-cliente.png" alt="Imagen de servicios" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Pre - Cotizaciones</h4>
            </div>
        </div>
       <div class="bg-white p-3 border">
        <div class="form-group text-right">
            <a href="{{route('descargarArchivo',['FORMATO_UNICO_DE_VISITAS.pdf'])}}" download="FORMATO_UNICO_DE_VISITAS" class="btn btn-sm btn-primary">
                <i class="fas fa-download"></i>
                <span>Descargar formato de visitas</span>
            </a>
        </div>
        <table class="table table-sm table-bordered" id="tablaPreCotizaciones">
            <thead class="text-center">
                <tr>
                    <th>N° Pre - Cotización</th>
                    <th>Cliente</th>
                    <th>Técnico Responsable</th>
                    <th>Fecha y Hr. programada</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
    @include('tecnico.modales.reportePrimeraVisita')
    @include('preCotizacion.modales.editarPreCotizacion')
@endsection