@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/administrador/programacion.js?v1.5"></script>
    <title>Programación de actividades</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/activo-intangible.png" alt="Imagen de actividades creativas" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Programación de actividades</h4>
            </div>
        </div>
        <form class="bg-white p-3 border mb-3" target="_blank" action="{{route('reporte.programacion')}}" method="GET">
            @csrf
            <div class="form-row">
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-2">
                    <label for="txtFechaInicio">Fecha Inicio</label>
                    <input type="date" value="{{$fechaInicio}}" class="form-control" required name="fecha_inicio" id="txtFechaInicio">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-2">
                    <label for="txtFechaFin">Fecha Fin</label>
                    <input type="date" value="{{$fechaFin}}" class="form-control" required name="fecha_fin" id="txtFechaFin">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3">
                    <label for="cbResponsables">Responsables</label>
                    <select name="responsables" required class="select2-simple" id="cbResponsables">
                        <option value="todos" selected>Todos</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{$usuario->id}}">{{$usuario->nombres . ' ' . $usuario->apellidos}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-sm btn-primary" type="button" data-toggle="tooltip" data-placement="top" title="Aplicar filtros" id="btnAplicarFiltros">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" type="submit" data-toggle="tooltip" data-placement="top" title="Visualizar reporte">
                        <i class="far fa-file-pdf"></i>
                    </button>
                </div>
            </div>
        </form>
        <div class="bg-white p-3 border">
            <div class="form-group text-right">
                <button class="btn btn-outline-primary" data-toggle="modal" data-target="#agregarProgramacion">
                    <i class="fas fa-plus"></i>
                    <span>Agregar</span>
                </button>
            </div>
            <table class="table table-sm table-bordered" id="tablaProgramacion">
                <thead class="text-center">
                    <tr>
                        <th>N°</th>
                        <th>Responsable</th>
                        <th>Fecha Hr. Inicio</th>
                        <th>Fecha Hr. Fin</th>
                        <th>Tipo</th>
                        <th>Actividad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
    @include('administracion.modales.agregarProgramacion')
    @include('administracion.modales.editarProgramacion')
@endsection