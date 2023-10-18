@extends('helper.index')
@section('head')
@include('helper.headDatatable')
<script src="/library/tinyeditor/tinyeditor.js"></script>
<script src="/library/tinyeditor/es.js"></script>
<script src="/cotizacion/compartido.js?v1.3"></script>
<script src="/administrador/publicidades.js"></script>
<title>Mis publicidades</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/publicidad-digital.png" alt="Imagen de publicidad por correo" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Publicidad</h4>
            </div>
        </div>
        <div class="bg-white p-3 border">
            <div class="form-group text-right">
                <button class="btn btn-outline-primary" data-toggle="modal" data-target="#agregarPublicidad">
                    <i class="fas fa-plus"></i>
                    <span>Nueva publicidad</span>
                </button>
            </div>
            <table class="table table-sm table-bordered" id="tablaPublicidad">
                <thead class="text-center">
                    <tr>
                        <th>N° Publicidad</th>
                        <th>Asunto</th>
                        <th>Fecha Hr. Creada</th>
                        <th>Último Envío</th>
                        <th>Responsable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
    @include('administracion.modales.egregarPublicidad')
@endsection