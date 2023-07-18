@extends('helper.index')
@section('head')
    <script src="/ordenServicio/agregar.js"></script>
    <title>Nueva orden de servicio</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/cotizacion.png" alt="Imagen de cotizacion" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Nueva Orden de Servicio</h4>
            </div>
        </div>
        <form id="frmCotizacion" class="">
            <div class="form-group">
                <fieldset class="bg-white px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Orden Servicio</legend>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                            <label for="cbPreCotizacion" class="col-form-label col-form-label-sm">Clientes</label>
                            <select name="id_pre_cotizacion" id="cbClientes" required class="form-control select2-simple" data-tags="true" data-placeholder="Seleccione un cliente">
                                <option value=""></option>
                                <option value="ninguno" selected>Ninguno</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4 col-xl-2">
                            <label for="idModalfechaEmitida">Fecha emisión</label>
                            <input type="date" name="fechaCotizacion" value="{{date('Y-m-d')}}" class="form-control form-control-sm" required>
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
                                    <th style="min-width: 300px;">DESCRIPCIÓN</th>
                                    <th style="width: 100px;">CANT.</th>
                                    <th>P. UNIT</th>
                                    <th>DESC.</th>
                                    <th>P.TOTAL</th>
                                    <th>ELIMINAR</th>
                                </tr>
                            </thead>
                            <tbody id="contenidoServicios">
                                <tr>
                                    <td colspan="100%" class="text-center">No se seleccionaron servicios</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6">SUBTOTAL</th>
                                    <th colspan="2" id="txtSubTotal">S/ 0.00</th>
                                </tr>
                                <tr>
                                    <th colspan="6">DESCUENTO</th>
                                    <th colspan="2" id="txtDescuento">- S/ 0.00</th>
                                </tr>
                                <tr>    
                                    <th colspan="6">I.G.V</th>
                                    <th colspan="2" id="txtIGV">S/ 0.00</th>
                                </tr>
                                <tr>
                                    <th colspan="6">COSTOS ADICIONALES</th>
                                    <th colspan="2" id="txtCostoAdicional">S/ 0.00</th>
                                </tr>
                                <tr>
                                    <th colspan="6">TOTAL</th>
                                    <th colspan="2" id="txtTotal">S/ 0.00</th>
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
                                    <th>ITEM</th>
                                    <th>DESCRIPCION</th>
                                    <th>P. UNIT</th>
                                    <th>CANT.</th>
                                    <th>P. TOTAL</th>
                                    <th>ELIMINAR</th>
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
            <div class="col-12 form-group text-center">
                <button type="submit" class="btn btn-primary" id="btnAgregarCotizacion">
                    <i class="fas fa-plus"></i>
                    <span>Agregar</span>
                </button>
            </div>
        </form>
    </section>
@endsection