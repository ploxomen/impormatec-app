@extends('helper.index')
@section('head')
    <script src="/general.js"></script>
    @include('helper.headDatatable')
    <script src="/usuario/modulo.js"></script>
    <title>Modulos</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 300px;">
                <img src="/img/modulo/modular.png" alt="Imagen de modulo" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de módulos</h4>
            </div>
        </div>
        <div class="p-3 bg-white">
            <table id="tablaModulo" class="table table-sm table-bordered text-secondary">
                <thead class="text-center">
                    <tr>
                        <th>N°</th>
                        <th>
                            <span>Grupo</span>
                            <b class="text-info mx-1"><i class="fas fa-chevron-right"></i></b>
                            <span>Módulo</span>
                        </th>
                        <th>Descipción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
    @include('usuario.modales.roles')
@endsection