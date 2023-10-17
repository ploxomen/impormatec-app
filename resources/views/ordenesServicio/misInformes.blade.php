@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/ordenServicio/misInformes.js?v1.5"></script>
    <title>Mis Informes</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/informe-seo.png" alt="Imagen de un informe" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Informes</h4>
            </div>
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
                    <th>Cliente</th>
                    <th>Responsable</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
@endsection