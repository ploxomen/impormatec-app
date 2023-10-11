<div class="modal fade" id="editarSeguimiento" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Acciones seguimiento</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="frmEditarSeguimiento" class="form-row">
            @method('PUT')
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <span>Pulse el boton <b>Actualizar</b> para guardar los cambios</span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="col-12">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Historial de seguimientos
                </h5>
            </div>
            <div class="form-group col-12" id="contenidoHistorialSeguimientoEditar"></div>
            <input type="submit" id="btnEditarSeguimiento" hidden>
          </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btnEditarFrm">
                <i class="far fa-save"></i>
                <span>Actualizar</span>
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="far fa-times-circle"></i>
                <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>