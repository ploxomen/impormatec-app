<div class="modal fade" id="generarFactura" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Generar comprobante</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formFacturar">
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="idModalFacturaBoleta" name="modoFacturaChec" required checked value="Boleta" class="custom-control-input tipo-factura">
                        <label class="custom-control-label" for="idModalFacturaBoleta">Boleta</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="idModalFacturaFactura" value="Factura" required name="modoFacturaChec" class="custom-control-input tipo-factura">
                        <label class="custom-control-label" for="idModalFacturaFactura">Factura</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="idModalComprobante" value="Comprobante" required name="modoFacturaChec" class="custom-control-input tipo-factura">
                        <label class="custom-control-label" for="idModalComprobante">Comprobante interno</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="modalFechaEmision">Fecha Emisión</label>
                    <input type="date" name="fechaEmision" required id="modalFechaEmision" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="modalTipoDocumentoSUNAT">Tipo Documento</label>
                    <select name="agenteTipoDocumento" required id="modalTipoDocumentoSUNAT" class="form-control select2-simple">
                        <option value="">Ninguno</option>
                        <option value="0" selected>DOC.TRIB.NO.DOM.SIN.RUC</option>
                        <option value="6">REGISTRO ÚNICO DE CONTRIBUYENTES</option>
                        <option value="7">PASPORTE</option>
                        <option value="A">CED. DIPLOMÁTICA DE IDENTIDAD</option>
                        <option value="B">DOCUMENTO INDENTIDAD PAÍS RESIDENCIA-NO.D</option>
                        <option value="C">TAX IDENTIFICACIÓN NUMBER - TIN - DOC TRIB PP.NN</option>
                        <option value="D">IDENTIFICATION NUMBER - IN - DOC TRIB PP.JJ</option>
                        <option value="E">TAM- TARJETA ANDINA DE MIGRACIÓN</option>
                        <option value="F">PERMISO TEMPORAL DE PERMANENCIA PTP</option>
                        <option value="G">SALVOCONDUCTO</option>
                        <option value="00">OTROS</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="modalagenteNumeroDocumento">Número Documento</label>
                    <input type="text" name="numeroDocumento" required id="modalagenteNumeroDocumento" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-8">
                    <label for="modalagente">Nombre</label>
                    <input type="text" required name="nombreAgente" id="modalagente" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="modalguia_remision_sunat">Guia de Remisión Remitente</label>
                    <input type="text" name="guiaRemision" id="modalguia_remision_sunat" class="form-control form-control-sm" pattern="^[A-Za-z]+[0-9]+-[0-9]+$" placeholder="Ejm: GR001-1234">
                </div>
                <div class="form-group col-12">
                    <label for="modalDireccion">Dirección</label>
                    <input type="text" name="direccionAgente" id="modalDireccion" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12 col-md-6">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="tipoAlContado" required name="tipoFactura" value="Contado" class="custom-control-input cambio-tipo-factura">
                        <label class="custom-control-label" for="tipoAlContado">Al contado</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="tipoACredito" checked value="Credito" required name="tipoFactura" class="custom-control-input cambio-tipo-factura">
                        <label class="custom-control-label" for="tipoACredito">A crédito</label>
                    </div>
                </div>
                <div class="form-group col-12">
                    <label for="modalObservaciones">Observaciones</label>
                    <textarea name="observaciones" id="modalObservaciones" class="form-control form-control-sm" rows="3"></textarea>
                </div>
                <div class="form-group col-12" id="bloqueCredito">
                    <div class="d-flex justify-content-between form-group" style="gap: 10px;">
                        <h5 class="text-primary">
                            <i class="fas fa-caret-right"></i>
                            Detalle Crédito
                        </h5>
                        <button type="button" class="btn btn-sm btn-primary" id="btnAgregarCuotaFactura" title="Agregar cuotas">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha Límite</th>
                                    <th>Monto $</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody id="tablaCreditos">
                                <tr>
                                    <td colspan="100%" class="text-center">No se asignaron cuotas</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Detalle de productos
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Descripcion</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductos"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Monto Total</th>
                                    <th id="modalimporte"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="form-group col-12">
                    <b>Total en letras: </b>
                    <span id="modaltotalLetras"></span>
                </div>
                <input type="submit" hidden id="inputFacturar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnFacturar">
                <i class="fas fa-save"></i>
                <span>Generar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" id="btnAtrasFrmContacto">
                <i class="far fa-hand-point-left"></i>                
                <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>