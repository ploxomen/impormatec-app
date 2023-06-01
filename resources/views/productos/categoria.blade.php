@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/productos/adminCategoria.js"></script>
    <title>Categoria</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/categoria.png" alt="Imagen de categorias" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de categorías</h4>
            </div>
        </div>
        
       <div class="row">
            <div class="form-group col-12 col-md-4">
                <form class="bg-white p-3 border" id="formCategoria">
                    <div class="form-group">
                        <label for="txtCategoria">Categoría</label>
                        <input type="text" name="nombreCategoria" maxlength="255" class="form-control" placeholder="Ej: Bebidas" id="txtCategoria" required>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="activo" class="custom-control-input" id="customSwitch1" checked>
                            <label class="custom-control-label" for="customSwitch1">Vigente</label>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-outline-primary form-group" id="btnGuardarForm">
                            <i class="fas fa-save"></i>
                            <span>Guardar</span>
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
                    <table id="tablaCategoria" class="table table-sm table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th>N°</th>
                                <th>Categoría</th>
                                <th>Estado</th>
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
@endsection