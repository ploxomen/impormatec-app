@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <link rel="stylesheet" href="/tecnico/primeraVisitaPreCoti.css?v1.5">
    <script src="/cotizacion/compartido.js?v1.3"></script>
    <script src="/cotizacion/misCotizaciones.js?v1.1"></script>
    <title>Mis Cotizaciones</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/gestion-de-proyectos.png" alt="Imagen de cotizaciones" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Cotizaciones</h4>
            </div>
        </div>
        <div class="bg-white p-3 mb-3">
            <form class="form-row" target="_blank" method="GET" action="{{route('cotizacion.reportes')}}">
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="txtFechaInicio">Fecha Inicio</label>
                    <input type="date" value="{{$fechaInicio}}" class="form-control" required name="fecha_inicio" id="txtFechaInicio">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                    <label for="txtFechaFin">Fecha Fin</label>
                    <input type="date" value="{{$fechaFin}}" class="form-control" required name="fecha_fin" id="txtFechaFin">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3 col-xl-3">
                    <label for="cbClientes">Clientes</label>
                    <select name="cliente" id="cbClientes" class="form-control select2-simple" required data-placeholder="Seleccione un cliente">
                        <option value="TODOS">Todos</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-2 col-xl-2">
                    <label for="cbEstados">Estados</label>
                    <select name="estado" id="cbEstados" class="select2-simple" required>
                        <option value="TODOS" selected>Todos</option>
                        <option value="1">Generado</option>
                        <option value="2">Aprobado</option>
                        <option value="3">Pendiente OS</option>
                        <option value="4">Con OS</option>
                        <option value="0">Anulado</option>
                    </select>
                </div>
                <div class="form-group col-12 col-lg-1 col-xl-3">
                    <button class="btn btn-sm btn-primary" type="button" data-toggle="tooltip" data-placement="top" title="Aplicar filtros" id="btnAplicarFiltros">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="form-group col-12">
                    <button type="submit" name="exportarExcel" class="btn btn-sm btn-success">
                        <i class="far fa-file-excel"></i>
                        <span>Exportar EXCEL</span>
                    </button>
                    <button type="submit" name="exportarPdf" class="btn btn-sm btn-danger">
                        <i class="far fa-file-pdf"></i>
                        <span>Exportar PDF</span>
                    </button>
                </div>
            </form>
        </div>
        <div class="bg-white p-3 border">
            <table class="table table-sm table-bordered" id="tablaCotizaciones">
                <thead class="text-center">
                    <tr>
                        <th>N° Cotización</th>
                        <th>N° Pre - Cotización</th>
                        <th>Fecha Emisión</th>
                        <th>Fecha Vencimiento</th>
                        <th>Cliente</th>
                        <th>Responsable</th>
                        <th>Subtotal</th>
                        <th>Desc.</th>
                        <th>I.G.V</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
    @include('cotizacion.modales.editarCotizacion')
    @include('cotizacion.modales.cotizacionAlmacen')
@endsection