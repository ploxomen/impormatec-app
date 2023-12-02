@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/library/signature.js"></script>
    <script src="/ordenServicio/compartidoOs.js?v1.5"></script>
    <link rel="stylesheet" href="/ordenServicio/ordenServicio.css">
    <script src="/ordenServicio/misOs.js?v1.5"></script>
    <title>Mis Ordenes de Servicio</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 450px;">
                <img src="/img/modulo/servicio-de-entrega.png" alt="Imagen de orden de servicio" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administración de Ordenes de Servicio</h4>
            </div>
        </div>
        <div class="bg-white p-3 mb-3">
            <form class="form-row" target="_blank" method="GET" action="{{route('ordenes.servicios.reportes')}}">
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
                        <option value="2">Informado</option>
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
                        <span>Exportar OS EXCEL</span>
                    </button>
                    <button type="submit" name="exportarPdf" class="btn btn-sm btn-danger">
                        <i class="far fa-file-pdf"></i>
                        <span>Exportar OS PDF</span>
                    </button>
                    <button type="submit" name="exportarExcelPagos" class="btn btn-sm btn-success">
                        <i class="far fa-file-excel"></i>
                        <span>Exportar Pagos EXCEL</span>
                    </button>
                    <button type="submit" name="exportarPdfPagos" class="btn btn-sm btn-danger">
                        <i class="far fa-file-pdf"></i>
                        <span>Exportar Pagos PDF</span>
                    </button>
                </div>
            </form>
        </div>
       <div class="bg-white p-3 border">
        <table class="table table-sm table-bordered" id="tablaCotizaciones">
            <thead class="text-center">
                <tr>
                    <th>N° OS</th>
                    <th>Fecha Emisión</th>
                    <th>Cliente</th>
                    <th>Subtotal</th>
                    <th>Desc.</th>
                    <th>I.G.V</th>
                    <th>Gastos Adicionales</th>
                    <th>Gastos Caja</th>
                    <th>Total</th>
                    <th>Costo Total</th>
                    <th>Utilidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
       </div>
    </section>
    @include('ordenesServicio.modales.editarOrdenes')
    @include('ordenesServicio.modales.actaEntrega')
    @include('ordenesServicio.modales.pagos')
    @include('ordenesServicio.modales.editarCuota')
    @include('ordenesServicio.modales.nuevaFactura')
    @include('ordenesServicio.modales.nuevaGuiaRemitente')
    @include('ordenesServicio.modales.misComprobantes')
@endsection