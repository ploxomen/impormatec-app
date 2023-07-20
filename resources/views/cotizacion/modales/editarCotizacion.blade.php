<div class="modal fade" id="editarCotizacion" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" >Editar cotización</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="frmCotizacion" class="form-row">
            <div class="col-12">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Datos de la cotización
                </h5>
            </div>
            <div class="col-12 col-md-6 col-lg-4 form-group">
                <label for="idModalid_pre_cotizacion" class="col-form-label col-form-label-sm">Pre - Cotizacion</label>
                <select name="id_pre_cotizacion" id="idModalid_pre_cotizacion" required class="form-control select2-simple" data-tags="true" data-placeholder="Seleccione una pre - cotización">
                    <option value=""></option>
                    <option value="ninguno" selected>Ninguno</option>
                    @foreach ($preCotizaciones as $preCotizacion)
                        <option value="{{$preCotizacion->id}}">{{str_pad($preCotizacion->id,5,'0',STR_PAD_LEFT)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12 col-md-6 col-lg-4">
                <label for="idModalfechaCotizacion">Fecha emisión</label>
                <input type="date" name="fechaCotizacion" id="idModalfechaCotizacion" class="form-control form-control-sm" required>
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
            <div class="col-12">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Datos del cliente
                </h5>
            </div>
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
                <label for="idModalrepresentanteCliente" class="col-form-label col-form-label-sm">Representante</label>
                <select name="representanteCliente" id="idModalrepresentanteCliente" class="form-control select2-simple" data-tags="true" required data-placeholder="Seleccione un representante">
                </select>
            </div>
            <div class="col-12 form-group">
                <label for="idModaldireccionCliente" class="col-form-label col-form-label-sm">Dirección</label>
                <input type="text" name="direccionCliente" id="idModaldireccionCliente" required class="form-control limpiar-frm form-control-sm">
            </div>          
            <div class="col-12">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Datos del servicio
                </h5>
            </div>
            <div class="form-group col-12">
                <label for="cbServicios">Lista de servicios</label>
                <select id="cbServicios" class="form-control select2-simple" data-placeholder="Seleccione un servicio">
                    <option value=""></option>
                    @foreach ($servicios as $servicio)
                        <option value="{{$servicio->id}}">{{$servicio->servicio}}</option>
                    @endforeach
                </select>
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
            <div class="col-12">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Datos del producto
                </h5>
            </div>
            <div class="col-12 form-group text-center" id="listaServiciosProductos">
                <span>Sin productos para mostrar</span>
            </div>
            <div class="col-12">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Datos adicionales
                </h5>
            </div>
            <div class="col-12 d-flex form-group" style="gap:15px;">
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
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="actualizarCotizacion">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>