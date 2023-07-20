@extends('helper.index')
@section('head')
    <script src="/cotizacion/compartido.js"></script>
    <script src="/cotizacion/nuevaCotizacion.js"></script>
    <title>Nueva cotización</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/cotizacion.png" alt="Imagen de cotizacion" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Nueva Cotización</h4>
            </div>
        </div>
        <form id="frmCotizacion" class="form-row">
            <div class="form-group col-12 col-lg-6">
                <fieldset class="bg-white col-12 px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Cotización</legend>
                    <div class="col-12 col-md-6 col-lg-4 form-group">
                        <label for="cbPreCotizacion" class="col-form-label col-form-label-sm">Pre - Cotizacion</label>
                        <select name="id_pre_cotizacion" id="cbPreCotizacion" required class="form-control select2-simple" data-tags="true" data-placeholder="Seleccione una pre - cotización">
                            <option value=""></option>
                            <option value="ninguno" selected>Ninguno</option>
                            @foreach ($preCotizaciones as $preCotizacion)
                                <option value="{{$preCotizacion->id}}">{{str_pad($preCotizacion->id,5,'0',STR_PAD_LEFT)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="idModalfechaEmitida">Fecha emisión</label>
                        <input type="date" name="fechaCotizacion" value="{{date('Y-m-d')}}" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-4">
                        <label for="idModalmoneda">Tipo moneda</label>
                        <select name="tipoMoneda" id="idModalmoneda" required class="select2-simple form-control-sm">
                            <option value=""></option>
                            <option value="Soles" selected>Soles (S/)</option>
                            <option value="Dolar">Dolar ($)</option>
                        </select>
                    </div>
                    <div class="col-12 form-group">
                        <label for="cbCliente" class="col-form-label col-form-label-sm">Referencia</label>
                        <input type="text" class="form-control form-control-sm" required id="idModalreferencia" name="referencia">
                    </div>
                    <select hidden id="cbProductos">
                        <option value=""></option>
                        @foreach ($productos as $producto)
                            <option value="{{$producto->id}}">{{$producto->nombreProducto}}</option>
                        @endforeach
                    </select>
                </fieldset>
            </div>
            <div class="form-group col-12 col-lg-6">
                <fieldset class="bg-white col-12 px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Cliente</legend>
                        <div class="col-12 col-lg-6 form-group">
                            <label for="idModalid_cliente" class="col-form-label col-form-label-sm">Cliente</label>
                            <select name="id_cliente" id="idModalid_cliente" class="form-control select2-simple" required data-placeholder="Seleccione un cliente">
                                <option value=""></option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-6 form-group">
                            <label for="cbContactosCliente" class="col-form-label col-form-label-sm">Representante</label>
                            <select name="representanteCliente" id="cbContactosCliente" class="form-control select2-simple" data-tags="true" required data-placeholder="Seleccione un representante">
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <label for="idModaldireccion" class="col-form-label col-form-label-sm">Dirección</label>
                            <input type="text" name="direccionCliente" id="idModaldireccion" required class="form-control limpiar-frm form-control-sm">
                        </div>          
                </fieldset>
            </div>
            <div class="form-group col-12">
                <fieldset class="bg-white col-12 px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Servicios</legend>
                        <div class="form-group col-12">
                            <label for="cbServicios">Mis Servicios</label>
                            <select id="cbServicios" class="form-control select2-simple" data-placeholder="Seleccione un servicio">
                                <option value=""></option>
                                @foreach ($servicios as $servicio)
                                    <option value="{{$servicio->id}}">{{$servicio->servicio}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <h5 class="text-primary">
                                <i class="fas fa-caret-right"></i>
                                Servicios seleccionados
                            </h5>
                        </div>
                        <div class="col-12 form-group">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ITEM</th>
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
                                            <th colspan="5">SUBTOTAL</th>
                                            <th colspan="2" id="idModalimporteTotal">S/ 0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="5">DESCUENTO</th>
                                            <th colspan="2" id="idModaldescuentoTotal">- S/ 0.00</th>
                                        </tr>
                                        <tr>    
                                            <th colspan="5">I.G.V</th>
                                            <th colspan="2" id="idModaligvTotal">S/ 0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="5">TOTAL</th>
                                            <th colspan="2" id="idModaltotal">S/ 0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                </fieldset>
            </div>
            <div class="form-group col-12">
                <fieldset class="bg-white col-12 px-3 border">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Servicios y productos</legend>
                    <div class="form-row" id="listaServiciosProductos">
                        <h5 class="col-12 text-primary text-center">
                            Sin productos para mostrar  
                        </h5>
                    </div>
                </fieldset>
            </div>
            <div class="form-group col-12">
                <fieldset class="bg-white col-12 px-3 border">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Adicionales</legend>
                    <div class="d-flex form-group" style="gap:15px;">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="reportePreCotizacion" value="1" class="custom-control-input" disabled id="incluirPreCotizacion">
                            <label class="custom-control-label" for="incluirPreCotizacion">Incluir Pre-cotización</label>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" value="1" name="reporteDetallado" id="reporteDetallado">
                            <label class="custom-control-label" for="reporteDetallado">Cotización detallada</label>
                        </div>
                        <div>
                            <input type="file" multiple accept=".pdf" id="fileOtrosDocumentos" hidden>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="btnOtrosDocumentos">
                                <i class="far fa-file-pdf"></i>
                                <span>Adjuntar documentos</span>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex" id="contenedorArchivoPdf" style="gap:10px; font-size: 0.8rem;"></div>
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