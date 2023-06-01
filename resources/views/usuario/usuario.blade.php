@extends('general.index')
@section('head')
    <title>Agregar usuarios</title>
    <script src="/general.js"></script>
    <script src="/usuario/users.js"></script>
@endsection
@section('body')
    <section class="bg-white p-2">
        <form action="">
            <fieldset class="px-3 border mb-2 row">
                <legend class="d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos personales</legend>
                <div class="form-group col-12 col-md-6">
                    <label for="type_document">Tipo de documento:</label>
                    <select name="type_document" id="type_document" class="form-control">
                        <option value="">DNI</option>
                        <option value="">Carnet de extranjeria</option>
                        <option value="">Pasaporte</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="n_document">N° de documento:</label>
                    <input type="tel" name="n_document" id="n_document" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="name">Nombres:</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="last_name">Apellidos:</label>
                    <input type="text" name="last_name" id="last_name" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="email">Correo:</label> 
                    <input type="email" class="form-control" name="email" id="email">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="phone">Celular:</label> 
                    <input type="tel" class="form-control" name="phone" id="phone">
                </div>
                <div class="form-group col-12">
                    <label for="direction">Direccion:</label>
                    <textarea name="direction" class="form-control" id="direction" cols="30" rows="2"></textarea>
                </div>
            </fieldset>
            <fieldset class="px-3 border mb-2">
                <legend class="d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos del sistema</legend>
                <div class="form-group">
                    <label for="areas">Área:</label>
                    <select name="id_area" class="form-control" id="areas">
                        <option></option>
                        @foreach ($areas as $area)
                            <option value="{{$area->id}}">{{$area->area}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="roles">Roles:</label>
                    <select name="roles" class="form-control" multiple id="roles"></select>
                </div>
                <div class="form-group">
                    <label for="">Contraseña temporal:</label>
                    <input type="text" readonly disabled class="form-control">
                </div>
            </fieldset>
            <div class="form-group">
                <button class="btn btn-primary">
                    <div class="d-flex">
                        <span class="material-icons">add</span>
                        <span>Agregar</span>
                    </div>
                </button>
                <button class="btn btn-danger" type="reset">
                    <div class="d-flex">
                        <span class="material-icons">clear_all</span>
                        <span>Limpiar</span>
                    </div>
                </button>
            </div>
        </form>
    </section>
    
@endsection