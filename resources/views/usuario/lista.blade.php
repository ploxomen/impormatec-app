@extends('helper.index')
@section('head')
    <script src="/general.js"></script>
    @include('helper.headDatatable')
    <script src="/usuario/users.js"></script>
    <title>Usuarios</title>
@endsection
@section('body')
<section class="p-3">
    <div class="mb-4">
        <div class="m-auto" style="max-width: 400px;">
            <img src="/img/modulo/usuario.png" alt="Imagen de una persona con roles" width="120px" class="img-fluid d-block m-auto">
            <h4 class="text-center text-primary my-2">Administración de usuarios</h4>
        </div>
    </div>
    <div class="form-group text-right">
        <button class="btn btn-outline-primary" data-toggle="modal" data-target="#usurioModal">
            <i class="fas fa-plus"></i>
            <span>Agregar</span>
        </button>
    </div>
    <div class="bg-white p-3 mb-3">
        <form class="row">
            <div class="form-group col-12 col-md-6 col-xl-3">
                <label for="cbRol">Rol:</label>
                <select name="rol" id="cbRol" class="form-control select2">
                    <option value="todos">Todos</option>
                    @foreach ($roles as $rol)
                        <option value="{{$rol->id}}">{{$rol->nombreRol}}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
    <div class="bg-white p-3">
        <div class="table-responsive">
            <table class="table table-striped table-bordered tabla-sm" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Nombre y Apellido</th>
                        <th>Celular</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>
@include('usuario.modales.usuario')
@include('usuario.modales.restaurarPass')
@endsection