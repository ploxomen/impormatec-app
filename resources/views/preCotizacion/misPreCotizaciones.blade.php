@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <title>Mis Pre - Cotizaciones</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/atencion-al-cliente.png" alt="Imagen de servicios" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Pre - Cotizaciones</h4>
            </div>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaServicios">
            <thead class="text-center">
                <tr>
                    <th>N° Pre - Cotización</th>
                    <th>Cliente</th>
                    <th>Contacto</th>
                    <th>Servicio</th>
                    <th>Técnico</th>
                    <th>Fecha y Hr. programada</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
@endsection