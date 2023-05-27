@extends('general.index')
@section('head')
    <script src="{{asset('asset/general.js')}}"></script>
    @include('headDatatable')
    <link rel="stylesheet" href="{{asset('asset/productos/pestilos.css')}}">
    <script src="{{asset('asset/ventas/misVentas.js')}}"></script>
    <title>Mis Ventas</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="{{asset('asset/img/modulo/mis-ventas.png')}}" alt="Imagen de ventas" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de ventas</h4>
            </div>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaVentas">
            <thead class="text-center">
                <tr>
                    <th>N° Venta</th>
                    <th>Tipo Comprobante</th>
                    <th>N° Comprobante</th>
                    <th>Fecha Emitida</th>
                    <th>Cliente</th>
                    <th>Método de pago</th>
                    <th>Método de envio</th>
                    <th>Subtotal</th>
                    <th>I.G.V</th>
                    <th>Descuento</th>
                    <th>Envío</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
    @include('intranet.ventas.modales.editarVenta')
@endsection