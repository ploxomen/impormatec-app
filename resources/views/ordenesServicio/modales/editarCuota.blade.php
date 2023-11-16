<div class="modal fade" id="modificarCuota" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modificar cuota</h5>
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
                <label for="idModalCuotafecha_vencimiento">Fecha vencimiento</label>
                <input required name="fecha_vencimiento" type="date" class="form-control form-control-sm" id="idModalCuotafecha_vencimiento">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="idModalCuotamonto_pagar">Monto</label>
                <input required name="monto_pagar" type="number" step="0.01" class="form-control form-control-sm" id="idModalCuotamonto_pagar">
            </div>
            <div class="form-group col-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="radioCambioPagos">
                    <label class="custom-control-label" for="radioCambioPagos">Pagado</label>
                </div>
            </div>
            <div class="col-12 form-group">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-caret-right"></i>
                    Datos del pago
                </h5>
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="idModalCuotafecha_vencimiento">Fecha</label>
                <input required disabled name="fecha_vencimiento" type="date" class="form-control form-control-sm pago-texto" id="idModalCuotafecha_vencimiento">
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="idModalCuotamonto_pagar">Monto</label>
                <input required disabled name="monto_pagar" type="number" step="0.01" class="form-control form-control-sm pago-texto" id="idModalCuotamonto_pagar">
            </div>
            <div class="col-12 form-group">
                <label for="idModalCuotafecha_vencimiento">Descripción</label>
                <textarea required disabled name="descripcion_pago" class="form-control form-control-sm pago-texto" rows="5"></textarea>
            </div>
            <div class="col-12 form-group">
              <input type="file" hidden>
              <button class="btn btn-sm btn-primary">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Subir comprobante</span>
              </button>
              <a href="#" class="contenido rounded-pill bg-light p-2">
                <input type="hidden" value="19" name="servicios[]">
                <span>Factura.pdf</span>
                <button type="button" class="btn btn-sm p-1" data-valor="19"><i class="fas fa-trash-alt"></i></button>
              </a>
            </div>
            <input type="submit" hidden id="enviarActa">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnGuardarCambiosActa">Guardar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>