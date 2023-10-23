@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/almacen/cajaChicaAdministrador.js"></script>
    <title>Mi caja chica</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/control-de-calidad.png" alt="Imagen control de calidad" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Mi caja chica</h4>
            </div>
        </div>
        <div class="form-group text-right">
            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#agregarCaja">
                <i class="fas fa-plus"></i>
                <span>Agregar</span>
            </button>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaCajaChica">
            <thead class="text-center">
                <tr>
                    <th>N° Caja</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Monto abonado</th>
                    <th>Monto gastado</th>
                    <th>Monto restante</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
    @include('almacen.modales.agregarCajaChica')
@endsection