@extends('general.index')
@section('head')
    <script src="{{asset('asset/general.js')}}"></script>
    @include('headDatatable')
    <script src="{{asset('asset/ventas/adminComprobantes.js')}}"></script>
    <title>Comprobantes</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 500px;">
                <img src="{{asset('asset/img/modulo/comprobante.png')}}" alt="Imagen de comprobantes" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Comprobantes</h4>
            </div>
        </div>
        <div class="form-group text-right">
            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#agregarComprobante">
                <i class="fas fa-plus"></i>
                <span>Agregar</span>
            </button>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaComprobantes">
            <thead class="text-center">
                <tr>
                    <th>N°</th>
                    <th>Comprobante</th>
                    <th>Serie</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Disponibles</th>
                    <th>Utilizado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
    @include('intranet.ventas.modales.agregarComprobante')
@endsection