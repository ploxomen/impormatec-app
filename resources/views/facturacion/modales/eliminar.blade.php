<div class="modal fade" id="eliminarFactura" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Anular factura</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="eliminarFacturaForm" class="row">
                <div class="form-group col-6">
                    <b class="d-block">Serie</b>
                    <span id="txtserie"></span>
                </div>
                <div class="form-group col-6">
                    <b class="d-block">Correlativo</b>
                    <span id="txtcorrelativo"></span>
                </div>
                <div class="form-group col-6">
                    <b class="d-block">Tipo Documento</b>
                    <span id="txttipoDocumento"></span>
                </div>
                <div class="form-group col-6">
                    <b class="d-block">Número Documento</b>
                    <span id="txtnumeroDocumento"></span>
                </div>
                <div class="form-group col-12">
                    <b class="d-block">Cliente</b>
                    <span id="txtcliente"></span>
                </div>
                <div class="form-group col-12">
                    <label for="txtMotivo">Motivo</label>
                    <textarea required name="motivo" class="form-control" id="txtMotivo" rows="5">Error al registrar los datos</textarea>
                </div>
                <input type="submit" hidden id="btnEnviar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btEliminar">
                <i class="fas fa-trash-alt"></i>
                <span>Anular</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                <i class="far fa-hand-point-left"></i>                
                <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>