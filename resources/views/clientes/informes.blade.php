@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/cliente/informe.js?v1.1"></script>
    <title>Mis Informes</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/informe-seo.png" alt="Imagen de un informe" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Mis Informes</h4>
            </div>
        </div>
        <div class="bg-white p-3 mb-3">
            <form class="form-row">
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="txtFechaInicio">Fecha Emisión Inicio</label>
                    <input type="date" value="{{$fechaInicio}}" class="form-control" required name="fecha_inicio" id="txtFechaInicio">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="txtFechaFin">Fecha Emisión Fin</label>
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
            <table class="table table-sm table-bordered" id="tablaMisInformes">
                <thead class="text-center">
                    <tr>
                        <th>N° INF.</th>
                        <th>N° OS</th>
                        <th>Fecha Emisión</th>
                        <th>Fecha Termino</th>
                        <th>Fecha Fin Garantia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
@endsection