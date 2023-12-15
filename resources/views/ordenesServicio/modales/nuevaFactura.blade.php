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
                        <input type="radio" id="idModalcomprobanteBoleta" name="modoFacturaChec" required value="Boleta" class="custom-control-input tipo-factura">
                        <label class="custom-control-label" for="idModalcomprobanteBoleta">Boleta</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="idModalcomprobanteFactura" value="Factura" required name="modoFacturaChec" class="custom-control-input tipo-factura">
                        <label class="custom-control-label" for="idModalcomprobanteFactura">Factura</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="idModalcomprobanteInterno" value="Comprobante" required name="modoFacturaChec" class="custom-control-input tipo-factura">
                        <label class="custom-control-label" for="idModalcomprobanteInterno">Comprobante interno</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="modalFechaEmision">Fecha Emisión</label>
                    <input type="date" name="fechaEmision" value="{{$diaActual}}" min="{{$dosDiasAntes}}" max="{{$diaActual}}" required id="modalFechaEmision" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="idModaltipoDocumentoCliente">Tipo Documento</label>
                    <select name="tipoDocumentoCliente" required id="idModaltipoDocumentoCliente" class="form-control select2-simple" data-placeholder="Seleccionar un tipo de documento">
                        <option value="">Ninguno</option>
                        @foreach ($tiposDocumentos as $tipoDocumento)
                            <option value="{{$tipoDocumento->valor}}">{{$tipoDocumento->documento}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="idModalnumeroDocumentoCliente">Número Documento</label>
                    <input type="text" name="numeroDocumentoCliente" required id="idModalnumeroDocumentoCliente" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12">
                    <label for="idModalnombreCliente">Nombre</label>
                    <input type="text" required name="nombreCliente" id="idModalnombreCliente" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12">
                    <label for="idModaldireccionCliente">Dirección</label>
                    <input type="text" name="direccionCliente" id="idModaldireccionCliente" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12 col-md-6" hidden>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="tipoAlContado" disabled required name="tipoFactura" value="Contado" class="custom-control-input cambio-tipo-factura">
                        <label class="custom-control-label" for="tipoAlContado">Al contado</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6" hidden>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="tipoACredito" disabled value="Credito" required name="tipoFactura" class="custom-control-input cambio-tipo-factura">
                        <label class="custom-control-label" for="tipoACredito">A crédito</label>
                    </div>
                </div>
                <div class="form-group col-12">
                    <label for="modalObservaciones">Observaciones</label>
                    <textarea name="observaciones" id="modalObservaciones" class="form-control form-control-sm" rows="3"></textarea>
                </div>
                <div class="form-group col-12" id="bloqueCredito" hidden>
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
                                    <th>Descuento</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductos"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">OP. GRAVADA</th>
                                    <th id="idModaloperacionGravada"></th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-right">I.G.V</th>
                                    <th id="idModaligvTotal"></th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-right">IMPORTE TOTAL</th>
                                    <th id="idModalimporteTotal"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="form-group col-12">
                    <b>Total en letras: </b>
                    <span id="idModalletraImporteTotal"></span>
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
                <i class="far fa-times-circle"></i>
                <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>