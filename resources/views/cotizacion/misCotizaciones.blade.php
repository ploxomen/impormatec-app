@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <link rel="stylesheet" href="/tecnico/primeraVisitaPreCoti.css?v1.5">
    <script src="/cotizacion/compartido.js?v1.3"></script>
    <script src="/cotizacion/misCotizaciones.js?v1.1"></script>
    <title>Mis Cotizaciones</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/gestion-de-proyectos.png" alt="Imagen de cotizaciones" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Cotizaciones</h4>
            </div>
        </div>
        <div class="bg-white p-3 mb-3">
            <form class="row">
                <div class="form-group col-12 col-md-3 col-lg-2">
                    <label for="cbEstados">Estados</label>
                    <select name="cbEstados" id="cbEstados" class="select2-simple" required>
                        <option value="TODOS" selected>TODOS</option>
                        <option value="1">GENERADO</option>
                        <option value="2">APROBADO</option>
                        <option value="3">PENDIENTE OS</option>
                        <option value="4">CON OS</option>
                        <option value="0">ANULADO</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="bg-white p-3 border">
            <table class="table table-sm table-bordered" id="tablaCotizaciones">
                <thead class="text-center">
                    <tr>
                        <th>N° Cotización</th>
                        <th>N° Pre - Cotización</th>
                        <th>Fecha Emisión</th>
                        <th>Fecha Vencimiento</th>
                        <th>Cliente</th>
                        <th>Cotizador</th>
                        <th>Importe</th>
                        <th>Desc.</th>
                        <th>I.G.V</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
    @include('cotizacion.modales.editarCotizacion')
    @include('cotizacion.modales.cotizacionAlmacen')
@endsection