@extends('general.index')
@section('head')
    <title>Mis productos</title>
    <script src="/general.js"></script>
    <script src="/productos/productos.js"></script>
    <script src="/productos/pListar.js"></script>
@endsection
@section('body')
    <style>
        .contenido-buscador{
            width: 100%;
            max-width: 150px;
        }
        .contenido-buscador-search{
            width: 100%;
            max-width: 350px;
        }
    </style>
    <section class="bg-white p-4 mb-3">
        <article>
            <form id="filtros" class="row">
                <div class="col-md-6 form-group">
                    <div class="contenido-buscador">
                        <label for="idnregistros">N° registros:</label>
                        <select class="form-control" name="n_registros" id="idnregistros">
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                            <option value="{{$totalProductos}}">Mostrar todo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 form-group">
                    <div class="contenido-buscador-search ml-md-auto">
                        <label for="idbuscar">Buscar:</label>
                        <input type="search" class="form-control" name="namaproduct" id="idbuscar">
                    </div>
                </div>
            </form>

        </article>
        <article>
            <div class="form-group text-right">
                <a href="{{route('addProduct" class="btn btn-success btn-sm mb-2"><span class="material-icons">add</span></a>
                <a href="" class="btn btn-danger btn-sm mb-2"><span class="material-icons">download</span></a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Producto</th>
                            <th>Detales</th>
                            <th>Precio<br>Compra</th>
                            <th>Precio<br>Venta</th>
                            <th>Tipo<br>Unidad</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">No se encontraron productos para mostrar</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>
    </section>
        
       
@endsection