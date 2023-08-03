@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/ordenServicio/compartidoOs.js"></script>
    <script src="/ordenServicio/misOs.js"></script>
    <title>Mis Cotizaciones</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/servicio-de-entrega.png" alt="Imagen de orden de servicio" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Ordenes de Servicio</h4>
            </div>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaCotizaciones">
            <thead class="text-center">
                <tr>
                    <th>N° OS</th>
                    <th>Fecha Emisión</th>
                    <th>Cliente</th>
                    <th>Importe</th>
                    <th>Desc.</th>
                    <th>I.G.V</th>
                    <th>Adicional</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
    @include('ordenesServicio.modales.editarOrdenes')
@endsection