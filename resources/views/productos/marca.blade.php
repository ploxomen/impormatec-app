@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/productos/adminMarca.js"></script>
    <title>Marcas</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/marca.png" alt="Imagen de marcas" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de marcas</h4>
            </div>
        </div>
        
       <div class="row">
            <div class="form-group col-12 col-md-4">
                <form class="bg-white p-3 border" id="formMarca">
                    <div class="form-group">
                        <label for="txtArea">Marcas</label>
                        <input type="text" name="nombreMarca" maxlength="255" class="form-control" placeholder="Ej: Gloria" id="txtMarca" required>
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
                    <table id="tablaMarca" class="table table-sm table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th>N°</th>
                                <th>Marca</th>
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