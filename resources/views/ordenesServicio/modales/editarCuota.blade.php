<div class="modal fade" id="modificarCuota" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloCuota">Modificar cuota</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-row" id="frmCuotaPago">
            <div class="col-12 form-group">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-caret-right"></i>
                    Datos de la cuota
                </h5>
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="idCuotafechaVencimiento">Fecha vencimiento</label>
                <input required name="fecha_vencimiento" type="date" class="form-control form-control-sm" id="idCuotafechaVencimiento">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="idCuotamontoPagar">Monto</label>
                <input required name="monto_pagar" type="number" step="0.01" class="form-control form-control-sm" id="idCuotamontoPagar">
            </div>
            <div class="form-group col-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="cuota_pagada" id="radioCambioPagos">
                    <label class="custom-control-label" for="radioCambioPagos">Cuota pagada</label>
                </div>
            </div>
            <div class="col-12 form-group">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-caret-right"></i>
                    Datos del pago
                </h5>
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="idCuotafechaPagada">Fecha</label>
                <input required disabled name="fecha_pagada" type="date" class="form-control form-control-sm pago-texto" id="idCuotafechaPagada">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="idCuotamontoPagado">Monto</label>
                <input required disabled name="monto_pagado" type="number" step="0.01" class="form-control form-control-sm pago-texto" id="idCuotamontoPagado">
            </div>
            <div class="col-12 col-md-6 form-group">
              <label for="idCuotafirmatePagado">Responsable</label>
              <select name="id_firmante_pago" required disabled id="idCuotafirmatePagado"  class="select2-simple pago-texto" data-placeholder="Seleccione una firma">
                <option value=""></option>
                @foreach ($firmasUsuarios as $firmaUsuario)
                    <option value="{{$firmaUsuario->id}}">{{$firmaUsuario->nombres . ' ' . $firmaUsuario->apellidos}}</option>
                @endforeach
            </select>
            </div>
            <div class="col-12 form-group">
                <label for="idCuotadescripcionPagada">Descripción</label>
                <textarea required disabled id="idCuotadescripcionPagada" name="descripcion_pagada" class="form-control form-control-sm pago-texto" rows="5"></textarea>
            </div>
            <div class="col-12 form-group d-flex flex-wrap align-items-center justify-content-between" style="gap: 5px;">
              <input type="file" accept=".pdf" name="comprobante_sunat" hidden id="documentoComprobante">
              <button class="btn btn-sm btn-primary" type="button" hidden id="botonDocumentoComprobante" title="Subir el comprobante otorgado por SUNAT">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Comprobante SUNAT</span>
              </button>
              <div class="contenido rounded-pill bg-light p-2" hidden>
                <a href="javascript:void(0)" id="enlaceDocumentoComprobante">
                </a>
                <button type="button" class="btn btn-sm p-1" id="eliminarDocumentoSunat"><i class="fas fa-trash-alt"></i></button>
              </div>
            </div>
            <div class="form-group col-12 pagos-ocultar" hidden>
              <input type="file" accept="image/*" multiple hidden id="documentoImagenPagos">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="text-primary mb-0">
                  <i class="fas fa-caret-right"></i>
                  Imagenes de pagos
                </h5>
                <button type="button" class="btn btn-sm btn-success" id="btnAgregarImagenPagos" data-toggle="tooltip" data-placement="top" title="Agregar imagenes de pagos">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
            <div class="form-group col-12 pagos-ocultar" hidden>
              <div class="row" id="contenidoImagenPagos"></div>
            </div>
            <input type="submit" hidden id="enviarCuota">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnGuardarCuota">Guardar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>