@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/cotizacion/seguimiento.js?v1.5"></script>
    <title>Seguimiento de cotizaciones</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/lista-de-verificacion.png" alt="Imagen de una lista verificando una lista" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Seguimiento de Cotizaciones</h4>
            </div>
        </div>
        <form class="bg-white p-3 border mb-3" id="filtrosSeguimiento">
            <div class="form-row">
                <div class="form-group col-12 text-center">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" data-filtros-ocultar="filtro-garantia" data-filtros-mostrar="filtro-aprobar" checked id="radioCotizacionesPendientes" name="tipo_consulta" class="custom-control-input">
                        <label class="custom-control-label" for="radioCotizacionesPendientes">Cotizaciones por aprobar</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="radioGarantia" data-filtros-ocultar="filtro-aprobar" data-filtros-mostrar="filtro-garantia" name="tipo_consulta" class="custom-control-input">
                        <label class="custom-control-label" for="radioGarantia">Servicios y/o productos por vencer garantias</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3 filtro-aprobar">
                    <label for="txtFechaInicio">Fecha Inicio</label>
                    <input type="date" value="{{$fechaInicio}}" class="form-control" required name="fecha_inicio" id="txtFechaInicio">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3 filtro-aprobar">
                    <label for="txtFechaFin">Fecha Fin</label>
                    <input type="date" value="{{$fechaFin}}" class="form-control" required name="fecha_fin" id="txtFechaFin">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-2 filtro-aprobar">
                    <label for="cbPorcentaje">Porcentaje</label>
                    <select name="porcentaje" required class="select2-simple" id="cbPorcentaje">
                        <option value="todos" selected>Todos</option>
                       <option value="95">95%</option>
                       <option value="75">75%</option>
                       <option value="50">50%</option>
                       <option value="30">30%</option>
                       <option value="10">10%</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3 filtro-aprobar">
                    <label for="cbCotizador">Responsable</label>
                    <select name="cotizador" required class="select2-simple" id="cbCotizador">
                        <option value="todos" selected>Todos</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{$usuario->id}}">{{$usuario->nombres . ' ' . $usuario->apellidos}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3 filtro-garantia" hidden>
                    <label for="cbYearFinGarantia">Año</label>
                    <select name="year_fin_garantia" required class="select2-simple" id="cbYearFinGarantia">
                        @for ($i = 2023; $i <= date('Y') + 25; $i++)
                            <option value="{{$i}}" {{$i == date('Y') ? 'selected' : ''}}>{{$i}}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3 filtro-garantia" hidden>
                    <label for="cbMesFinGarantia">Mes</label>
                    <select name="mes_fin_garantia" required class="select2-simple" id="cbMesFinGarantia">
                        <option value="0">Todos</option>
                        @foreach ($meses as $keyMes => $mes)
                            <option value="{{$keyMes + 1}}" {{($keyMes + 1 ) == date('n') ? 'selected' : ''}}>{{ucfirst($mes)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-3 filtro-garantia" hidden>
                    <label for="cbClientes">Clientes</label>
                    <select name="id_cliente" id="cbClientes" class="form-control select2-simple" required data-placeholder="Seleccione un cliente">
                        <option value="0">Todos</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4 col-xl-2 filtro-garantia" hidden>
                    <label for="cbEstadoGarantia">Estado</label>
                    <select name="vigencia" required class="select2-simple" id="cbEstadoGarantia">
                        <option value="todos" selected>Todos</option>
                       <option value="vigentes">Vigentes</option>
                       <option value="vencidas">Vencidas</option>
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-sm btn-primary" type="button" data-toggle="tooltip" data-placement="top" title="Aplicar filtros" id="btnAplicarFiltros">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
       <div class="bg-white p-3 border">
            <table class="table table-sm table-bordered" id="tablaSeguimiento">
                <thead class="text-center">
                    <tr>
                        <th>N° Cotización</th>
                        <th>Fecha Emisión</th>
                        <th>Fecha Vencimiento</th>
                        <th>Porcentaje</th>
                        <th>Cliente</th>
                        <th>Responsable</th>
                        <th>Importe</th>
                        <th>Desc.</th>
                        <th>I.G.V</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
            <table class="table table-sm table-bordered" id="tablaFinGarantia">
                <thead class="text-center">
                    <tr>
                        <th>N° Cotización</th>
                        <th>N° OS</th>
                        <th>Fecha Fin Garantia</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Descripcion</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
       </div>
    </section>
    @include('cotizacion.modales.agregarSeguimiento')
    @include('cotizacion.modales.editarSeguimiento')
@endsection