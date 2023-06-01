@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/usuario/rol.js"></script>
    <title>Roles</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 300px;">
                <img src="/img/modulo/roles.png" alt="Imagen de 4 roles" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de roles</h4>
            </div>
        </div>
        
       <div class="row">
            <div class="form-group col-12 col-md-4">
                <form class="bg-white p-3 border" id="formRol">
                    <div class="form-group">
                        <label for="txtRol">Rol</label>
                        <input type="text" name="rol" maxlength="255" class="form-control" placeholder="Ej: Administrador" id="txtRol" required>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-outline-primary form-group" id="btnGuardarForm">
                            <i class="fas fa-hand-point-right"></i>
                            <span>Siguiente</span>
                        </button>
                        <button type="reset" class="btn btn-outline-danger form-group">
                            <i class="fas fa-eraser"></i>
                            <span>Cancelar</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="form-group col-12 col-md-8">
                <div class="bg-white p-3 border">
                    <table id="tablaRol" class="table table-sm table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th>N°</th>
                                <th>Rol</th>
                                {{-- <th>Icono</th> --}}
                                {{-- <th>Fecha Creada</th>
                                <th>Fecha Actualizada</th> --}}
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
       </div>
    </section>
    @include('usuario.modales.modulos')
@endsection