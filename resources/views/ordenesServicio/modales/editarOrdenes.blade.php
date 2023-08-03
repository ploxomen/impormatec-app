<div class="modal fade" id="editarOrdenServicio" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloServicio">Editar Orden de Servicio</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="frmOrdenServicio" class="form-row">
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Orden de Servicio
                    </h5>
                </div>
                <div class="col-12 col-md-6 col-lg-8 form-group">
                    <label for="cbPreCotizacion" class="col-form-label col-form-label-sm">Clientes</label>
                    <input type="text" class="form-control form-control-sm" disabled id="idModalcliente">                    
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="idModalfechaEmitida">Fecha emisión</label>
                    <input type="date" name="fecha" id="idModalfechaEmitida" class="form-control form-control-sm" required>
                </div>
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Cotización
                    </h5>
                    <small class="text-info">Nota: Solo las cotizaciones que han sido aprobadas apareceran para agregarse como ordenes de servicio</small>
                </div>
                <div class="form-group col-12">
                    <label for="idCotizacionServicio">Cotizaciones</label>
                    <select class="select2-simple" id="idCotizacionServicio"></select>
                </div>
                <div class="form-group col-12">
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
                </div>
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Adicionales
                    </h5>
                </div>
                <div class="col-12 form-group">
                    <span class="text-primary">Costos adicionales</span>
                    <button type="button" class="btn btn-sm btn-primary" id="btnAgregarServiciosAdicionales">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="col-12 form-group">
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
                </div>
                <input type="submit" hidden id="btnEnviar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrm">
                <i class="fas fa-save"></i>
                <span>Guardar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                <i class="fas fa-eraser"></i>
                <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>