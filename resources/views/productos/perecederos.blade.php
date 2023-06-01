@extends('general.index')
@section('head')
    <script src="/general.js')}}"></script>
    @include('headDatatable')
    <script src="/productos/perecederos.js')}}"></script>
    <title>Perecederos</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/fechaVencimiento.png')}}" alt="Imagen de perecederos" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de perecederos</h4>
            </div>
        </div>
        <div class="form-group text-right">
            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#agregarPerecedero">
                <i class="fas fa-plus"></i>
                <span>Agregar</span>
            </button>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaPerecedero">
            <thead class="text-center">
                <tr>
                    <th>N°</th>
                    <th>Código/Barra</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Categoría</th>
                    <th>Presentación</th>
                    <th>Vence</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
    @include('intranet.productos.modales.agregarPerecedero')
@endsection