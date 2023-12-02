@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/preCotizacion/compartido.js"></script>
    <link rel="stylesheet" href="/ordenServicio/informe.css?v1.1">
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
        <div class="bg-white mb-3 border p-3">
            <form class="form-row">
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="txtFechaInicio">Fecha Inicio</label>
                    <input type="date" value="{{$fechaInicio}}" class="form-control" required name="fecha_inicio" id="txtFechaInicio">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="txtFechaFin">Fecha Fin</label>
                    <input type="date" value="{{$fechaFin}}" class="form-control" required name="fecha_fin" id="txtFechaFin">
                </div>
                <div class="form-group col-12 col-lg-1 col-xl-3">
                    <button class="btn btn-sm btn-primary" type="button" data-toggle="tooltip" data-placement="top" title="Aplicar filtros" id="btnAplicarFiltros">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            <div class="form-group text-right">
                @include('preCotizacion.botonDescargaVisita')
            </div>
        </div>
       <div class="bg-white p-3 border mb-3">
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
    @include('ordenesServicio.modales.agregarSeccion')
@endsection