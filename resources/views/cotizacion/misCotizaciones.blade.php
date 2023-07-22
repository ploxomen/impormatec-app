@extends('helper.index')
@section('head')
    @include('helper.headDatatable')

    <link rel="stylesheet" href="/tecnico/primeraVisitaPreCoti.css?v1.3">
    <script src="/cotizacion/compartido.js"></script>
    <script src="/cotizacion/misCotizaciones.js"></script>
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