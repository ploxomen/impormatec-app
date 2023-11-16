<div class="modal fade" id="generarGuiaRemision" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Guía de Remisión</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row formulario-remision" id="formGuiaRemitente">
                <div class="form-group mb-1 col-6 col-md-4">
                    <label for="modalFechaEmision">Fecha Emisión</label>
                    <input type="date" name="fechaEmision" required id="modalFechaEmision" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-6 col-md-4">
                    <label for="modalFechaEmision">Fecha Traslado</label>
                    <input type="date" name="fechaTraslado" required id="modalFechaEmision" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12 col-md-4">
                    <label for="modalfacturaSunat">Factura</label>
                    <input type="text" name="facturaSunat" pattern="^[A-Za-z]+[0-9]+-[0-9]+$" placeholder="Ejm: F001-1234" id="modalfacturaSunat" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalPuntoPartida">Punto de Partida</label>
                    <input type="text" value="JR. AMERICA 626 URB. EL PORVENIR INT. 302 LIMA - LIMA - LA VICTORIA" name="puntoPartida" required id="modalPuntoPartida" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalpuntoLlegada">Punto de Llegada</label>
                    <input type="text" value="AV. ELMER FAUCETT NRO. S/N (ARPTO INT JORGE CHAVEZ - RAMPA SUR) - CALLAO - PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO" name="puntoLlegada" required id="modalpuntoLlegada" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del destinatario
                    </h6>
                </div>
                <div class="form-group mb-1 col-12 col-lg-4">
                    <label for="modalagenteNumeroDocumento">Número RUC</label>
                    <input type="text" name="numeroDocumentoDestinatario" value="20550083613" required id="modalagenteNumeroDocumento" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12 col-lg-8">
                    <label for="modalagente">Nombre</label>
                    <input type="text" required name="nombreDestinatario" value="SERVICIOS AEROPORTUARIOS ANDINOS S.A" id="modalagente" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalDireccionDestinatario">Dirección</label>
                    <input type="text" required name="direccionDestinatario" value="Av. Mariscal Jose de la Mar Nro. 1263 Int. 604" id="modalDireccionDestinatario" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos Bultos
                    </h6>
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="pesoBultoTotal">Peso Bulto Total</label>
                    <input type="number" step="0.01" min="0" required name="pesoBultoTotal" id="modalpesoBultoTotal" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos Transportista
                    </h6>
                </div>
                <div class="form-group mb-1 col-12 col-lg-4">
                    <label for="modalnumeroDocumentoTransportista">Número RUC</label>
                    <input type="text" name="numeroDocumentoTransportista" value="20606223162" required id="modalnumeroDocumentoTransportista" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12 col-lg-8">
                    <label for="modalnombreTransportista">Nombre</label>
                    <input type="text" required name="nombreTransportista" value="WYNS SERVICIOS GENERALES S.A.C." id="modalnombreTransportista" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos de los Vehículos - Principal
                    </h6>
                </div>
                <div class="form-group mb-1 col-6">
                    <label for="modalnumeroPlacaPrincipal">Número de Placa</label>
                    <input type="text" value="BBO900" required name="numeroPlacaPrincipal" id="modalnumeroPlacaPrincipal" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-6">
                    <label for="modalnumeroTuceOChvPrincipal">TUCE o CHV</label>
                    <input type="text" value="151933753" required name="numeroTuceOChvPrincipal" id="modalnumeroTuceOChvPrincipal" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos de los Vehículos - Secundario
                    </h6>
                </div>
                <div class="form-group mb-1 col-6">
                    <label for="modalnumeroPlacaSecundario">Número de Placa</label>
                    <input type="text" name="numeroPlacaSecundario" id="modalnumeroPlacaSecundario" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-6">
                    <label for="modalnumeroTuceOChvSecundario">TUCE o CHV</label>
                    <input type="text" name="numeroTuceOChvSecundario" id="modalnumeroTuceOChvSecundario" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del Conductor - Principal
                    </h6>
                </div>
                <div class="form-group mb-1 col-4">
                    <label for="modaltipoDocumentoConductorPrincipal">Tipo Documento</label>
                    <select name="tipoDocumentoConductorPrincipal" required id="modaltipoDocumentoConductorPrincipal" class="form-control">
                        <option value="">Ninguno</option>
                        <option value="6">REGISTRO ÚNICO DE CONTRIBUYENTES</option>
                        <option value="1" selected>DNI</option>
                        <option value="7">PASPORTE</option>
                        <option value="A">CED. DIPLOMÁTICA DE IDENTIDAD</option>
                    </select>
                </div>
                <div class="form-group mb-1 col-4">
                    <label for="modalnumeroDocumentoConductorPrincipal">Número Documento</label>
                    <input type="text" value="40584190" name="numeroDocumentoConductorPrincipal" id="modalnumeroDocumentoConductorPrincipal" required class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-4">
                    <label for="modalnumeroLicenciaConductorPrincipal">Número Licencia</label>
                    <input type="text" value="Q40584190" name="numeroLicenciaConductorPrincipal" id="modalnumeroLicenciaConductorPrincipal" required class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalnombreCompletoConductorPrincipal">Apellidos y Nombres</label>
                    <input type="text" value="FARIAS GONZALES WILLMER HUGO" name="nombreCompletoConductorPrincipal" id="modalnombreCompletoConductorPrincipal" required class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del Conductor - Secundario
                    </h6>
                </div>
                <div class="form-group mb-1 col-4">
                    <label for="modaltipoDocumentoConductorSecundario">Tipo Documento</label>
                    <select name="tipoDocumentoConductorSecundario" id="modaltipoDocumentoConductorSecundario" class="form-control">
                        <option value="">Ninguno</option>
                        <option value="6">REGISTRO ÚNICO DE CONTRIBUYENTES</option>
                        <option value="1">DNI</option>
                        <option value="7">PASPORTE</option>
                        <option value="A">CED. DIPLOMÁTICA DE IDENTIDAD</option>
                    </select>
                </div>
                <div class="form-group mb-1 col-4">
                    <label for="modalnumeroDocumentoConductorSecundario">Número Documento</label>
                    <input type="text" name="numeroDocumentoConductorSecundario" id="modalnumeroDocumentoConductorSecundario" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-4">
                    <label for="modalnumeroLicenciaConductorSecundario">Número Licencia</label>
                    <input type="text" name="numeroLicenciaConductorSecundario" id="modalnumeroLicenciaConductorSecundario" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalnombreCompletoConductorSecundario">Apellidos y Nombres</label>
                    <input type="text" name="nombreCompletoConductorSecundario" id="modalnombreCompletoConductorSecundario" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalObservaciones">Observaciones</label>
                    <textarea name="observaciones" id="modalobservaciones" class="form-control form-control-sm" rows="3"></textarea>
                </div>
                <div class="form-group mb-1 col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Detalle de productos
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Descripcion</th>
                                    <th>Unidad</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductos"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Cantidad Total</th>
                                    <th id="modalCantidadTotal"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <input type="submit" id="inputFacturar" hidden>
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