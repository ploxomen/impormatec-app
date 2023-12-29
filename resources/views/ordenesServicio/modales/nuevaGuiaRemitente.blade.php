<div class="modal fade" id="generarGuiaRemision" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Guía de Remisión Remitente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row formulario-remision" id="formGuiaRemitente">
                <div class="form-group mb-1 col-6">
                    <label for="modalFechaEmision">Fecha Emisión</label>
                    <input type="date" value="{{$diaActual}}" min="{{$dosDiasAntes}}" max="{{$diaActual}}" name="fechaEmision" required id="modalFechaEmision" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-6">
                    <label for="modalFechaEmision">Fecha Traslado</label>
                    <input type="date" value="{{$diaActual}}" max="{{$diaActual}}" name="fechaTraslado" required id="modalFechaEmision" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalPuntoPartida">Dirección Partida</label>
                    <input type="text" name="puntoPartida" required id="modalPuntoPartida" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalUbigeoPartida">Ubigeo partida</label>
                    <input type="text" name="ubigeoPartida" value="LIMA - LIMA - SAN MARTIN DE PORRES" required id="modalUbigeoPartida" class="form-control form-control-sm">
                    <small class="form-text text-muted">El ubigeo se debe escribir de la siguiente manera: DEPARTAMENTO - PROVINCIA - DISTRITO</small>
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalpuntoLlegada">Dirección Llegada</label>
                    <input type="text" name="puntoLlegada" required id="modalPuntoLlegada" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalUbigeoLlegada">Ubigeo Llegada</label>
                    <input type="text" name="ubigeoLlegada" required id="modalUbigeoLlegada" class="form-control form-control-sm">
                    <small class="form-text text-muted">El ubigeo se debe escribir de la siguiente manera: DEPARTAMENTO - PROVINCIA - DISTRITO</small>
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalMotivoTraslado">Motivo Traslado</label>
                    <select id="modalMotivoTraslado" required name="motivoTraslado" class="form-control form-control-sm">
                        <option value="01" selected>VENTA</option>
                        <option value="02">COMPRA</option>
                        <option value="04">TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA</option>
                        <option value="13">OTROS</option>
                        <option value="14">VENTA SUJETA A CONFIRMACION DEL COMPRADOR</option>
                        <option value="18">TRASLADO EMISOR ITINERANTE CP</option>
                    </select>
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del destinatario
                    </h6>
                </div>
                <div class="form-group mb-1 col-12 col-lg-4">
                    <label for="modalagenteNumeroDocumento">Número RUC</label>
                    <input type="text" name="numeroDocumentoDestinatario" required id="modalagenteNumeroDocumento" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12 col-lg-8">
                    <label for="modalagente">Nombre</label>
                    <input type="text" required name="nombreDestinatario" id="modalagente" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalDireccionDestinatario">Dirección</label>
                    <input type="text" required name="direccionDestinatario" id="modalDireccionDestinatario" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos Transportista
                    </h6>
                </div>
                <div class="form-group mb-1 col-12 col-lg-4">
                    <label for="modalnumeroDocumentoTransportista">Número RUC</label>
                    <input type="text" name="numeroDocumentoTransportista" required id="modalnumeroDocumentoTransportista" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12 col-lg-8">
                    <label for="modalnombreTransportista">Nombre</label>
                    <input type="text" required name="nombreTransportista" id="modalnombreTransportista" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos de los Vehículos - Principal
                    </h6>
                </div>
                <div class="form-group mb-1 col-6">
                    <label for="modalnumeroPlacaPrincipal">Número de Placa</label>
                    <input type="text" required name="numeroPlacaPrincipal" id="modalnumeroPlacaPrincipal" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-6">
                    <label for="modalnumeroTuceOChvPrincipal">TUCE o CHV</label>
                    <input type="text" name="numeroTuceOChvPrincipal" id="modalnumeroTuceOChvPrincipal" class="form-control form-control-sm">
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
                    <select name="tipoDocumentoConductorPrincipal" required id="modaltipoDocumentoConductorPrincipal" class="form-control form-control-sm">
                        <option value="">Ninguno</option>
                        <option value="6">REGISTRO ÚNICO DE CONTRIBUYENTES</option>
                        <option value="1" selected>DNI</option>
                        <option value="7">PASPORTE</option>
                        <option value="A">CED. DIPLOMÁTICA DE IDENTIDAD</option>
                    </select>
                </div>
                <div class="form-group mb-1 col-4">
                    <label for="modalnumeroDocumentoConductorPrincipal">Número Documento</label>
                    <input type="text" name="numeroDocumentoConductorPrincipal" id="modalnumeroDocumentoConductorPrincipal" required class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-4">
                    <label for="modalnumeroLicenciaConductorPrincipal">Número Licencia</label>
                    <input type="text" name="numeroLicenciaConductorPrincipal" id="modalnumeroLicenciaConductorPrincipal" required class="form-control form-control-sm">
                </div>
                <div class="form-group mb-1 col-12">
                    <label for="modalnombreCompletoConductorPrincipal">Apellidos y Nombres</label>
                    <input type="text" name="nombreCompletoConductorPrincipal" id="modalnombreCompletoConductorPrincipal" required class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del Conductor - Secundario
                    </h6>
                </div>
                <div class="form-group mb-1 col-4">
                    <label for="modaltipoDocumentoConductorSecundario">Tipo Documento</label>
                    <select name="tipoDocumentoConductorSecundario" id="modaltipoDocumentoConductorSecundario" class="form-control form-control-sm">
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
                <div class="form-group mb-1 col-12 d-flex justify-content-between">
                    <h6 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Detalle de productos
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" title="Agregar detalle" id="agregarDetalleGuiaRemision">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="form-group mb-1 col-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th style="min-width: 400px; width: 400px;">Descripcion</th>
                                    <th style="min-width: 200px; width: 200px; max-width: 200px;">Unidad</th>
                                    <th>Cantidad</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody id="tablaDetalleGuiaRemision">
                                <tr>
                                    <td>1</td>
                                    <td><textarea name="descripciones[]" required class="form-control form-control-sm" rows="2"></textarea></td>
                                    <td>
                                        <select name="unidades[]" required class="select2-simple" data-placeholder="Seleccione una medida">
                                            <option value=""></option>
                                            @foreach ($unidadesMedidas as $unidadMedida)
                                                <option value="{{$unidadMedida->codigo}}" {{$unidadMedida->codigo === 'NIU' ? 'selected' : ''}}>{{$unidadMedida->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" required name="cantidades[]" class="form-control form-control-sm" step="0.01">
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger eliminar-detalle" type="button" title="Eliminar detalle">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Bulto Total</th>
                                    <th id="modalCantidadTotal">0</th>
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
                <i class="far fa-times-circle"></i>
                <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>