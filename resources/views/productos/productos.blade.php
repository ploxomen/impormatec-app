@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/productos/adminProductos.js?v1.5"></script>
    <title>Productos</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/clasificacion.png" alt="Imagen de productos" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de productos</h4>
            </div>
        </div>
        <div class="form-group text-right">
            <input type="file" hidden id="inputFileExcelUtilidades" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
            <button class="btn btn-success" id="subirExcelUtilidades">
                <i class="fas fa-file-upload"></i>
                <span>Importar utilidades</span>
            </button>
            <button class="btn btn-primary" data-toggle="modal" data-target="#agregarProducto">
                <i class="fas fa-plus"></i>
                <span>Agregar</span>
            </button>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaProductos">
            <thead class="text-center">
                <tr>
                    <th>N°</th>
                    <th>Producto</th>
                    <th>Descripcion</th>
                    <th>Almacenes</th>
                    <th>P. Venta</th>
                    <th>P. Compra</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
    @include('productos.modales.agregarProducto')
    @include('productos.modales.importarUtilidades')
@endsection