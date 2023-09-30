@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/ordenServicio/compartidoOs.js?v1.1"></script>
    <script src="/ordenServicio/agregar.js?v1.1"></script>
    <title>Nueva orden de servicio</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/orden.png" alt="Imagen de cotizacion" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Nueva Orden de Servicio</h4>
            </div>
        </div>
        <form id="frmCotizacion">
            <div class="form-group">
                <fieldset class="bg-white px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Orden Servicio</legend>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                            <label for="cbPreCotizacion" class="col-form-label col-form-label-sm">Clientes</label>
                            <select name="id_cliente" id="cbClientes" required class="form-control select2-simple" data-tags="true" data-placeholder="Seleccione un cliente">
                                <option value=""></option>
                                <option value="ninguno" selected>Ninguno</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4 col-xl-2">
                            <label for="idModalfechaEmitida">Fecha emisión</label>
                            <input type="date" name="fecha" value="{{date('Y-m-d')}}" id="idModalfechaEmitida" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4 col-xl-2">
                            <label for="idModaltipoMoneda">Tipo moneda</label>
                            <select name="tipoMoneda" id="idModaltipoMoneda" required class="select2-simple form-control-sm">
                                <option value=""></option>
                                <option value="PEN">Soles (S/)</option>
                                <option value="USD" selected>Dolar ($)</option>
                            </select>
                        </div>
                </fieldset>
            </div>
            <div class="form-group">
                <fieldset class="bg-white px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Cotizaciones</legend>
                    <small class="text-info">Nota: Solo las cotizaciones que han sido aprobadas apareceran para agregarse como ordenes de servicio</small>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>N° COTIZACION</th>
                                    <th style="min-width: 300px;">DESCRIPCION</th>
                                    <th style="width: 100px;">CANT.</th>
                                    <th>P. UNIT</th>
                                    <th>DESC.</th>
                                    <th>P.TOTAL</th>
                                    <th>ELIMINAR</th>
                                </tr>
                            </thead>
                            <tbody id="contenidoServicios">
                                <tr>
                                    <td colspan="100%" class="text-center">No se seleccionaron servicios y/o productos</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6">SUBTOTAL</th>
                                    <th colspan="2" id="txtSubTotal">$0.00</th>
                                </tr>
                                <tr>
                                    <th colspan="6">DESCUENTO</th>
                                    <th colspan="2" id="txtDescuento">- $0.00</th>
                                </tr>
                                <tr>    
                                    <th colspan="6">I.G.V</th>
                                    <th colspan="2" id="txtIGV">$0.00</th>
                                </tr>
                                <tr>
                                    <th colspan="6">COSTOS ADICIONALES</th>
                                    <th colspan="2" id="txtCostoAdicional">$0.00</th>
                                </tr>
                                <tr>
                                    <th colspan="6">TOTAL</th>
                                    <th colspan="2" id="txtTotal">$0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </fieldset>
            </div>
            <div class="form-group">
                <fieldset class="bg-white px-3 border">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Adicionales</legend>
                    <span class="text-primary">Costos adicionales</span>
                    <button type="button" class="btn btn-sm btn-primary" id="btnAgregarServiciosAdicionales">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">ITEM</th>
                                    <th>DESCRIPCION</th>
                                    <th style="width: 120px;">P. UNIT</th>
                                    <th style="width: 120px;">CANT.</th>
                                    <th style="width: 120px;">P. TOTAL</th>
                                    <th style="width: 120px;">ELIMINAR</th>
                                </tr>
                            </thead>
                            <tbody id="tablaServiciosAdicionales" data-tipo="vacio">
                                <tr>
                                    <td colspan="100%" class="text-center">No se agregaron servicios adicionales</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
            <div class="form-group">
                <fieldset class="bg-white px-3 border">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Observaciones</legend>
                    <textarea id="observacionesOrdenServicio">
                        <span>Sin observaciones</span>
                    </textarea>
                </fieldset>
            </div>
            <div class="col-12 form-group text-center">
                <button type="submit" class="btn btn-primary" id="btnAgregarCotizacion">
                    <i class="fas fa-plus"></i>
                    <span>Agregar</span>
                </button>
            </div>
        </form>
    </section>
@endsection