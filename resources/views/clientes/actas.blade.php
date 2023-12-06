@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/cliente/acta.js?v1.1"></script>
    <title>Mis Actas</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 450px;">
                <img src="/img/modulo/documento.png" alt="Imagen de un documento entregado" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Mis Actas</h4>
            </div>
        </div>
        <div class="bg-white p-3 mb-3">
            <form class="form-row">
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="txtFechaInicio">Fecha Entrega Inicio</label>
                    <input type="date" value="{{$fechaInicio}}" class="form-control" required name="fecha_inicio" id="txtFechaInicio">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="txtFechaFin">Fecha Entrega Fin</label>
                    <input type="date" value="{{$fechaFin}}" class="form-control" required name="fecha_fin" id="txtFechaFin">
                </div>
                <div class="form-group col-12 col-lg-1 col-xl-3">
                    <button class="btn btn-sm btn-primary" type="button" data-toggle="tooltip" data-placement="top" title="Aplicar filtros" id="btnAplicarFiltros">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="bg-white p-3 border">
            <table class="table table-sm table-bordered" id="tablaActas">
                <thead class="text-center">
                    <tr>
                        <th>N° Acta</th>
                        <th>N° OS</th>
                        <th>Fecha Entrega</th>
                        <th>Responsable</th>
                        <th>D.N.I Representante</th>
                        <th>Representante</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
@endsection