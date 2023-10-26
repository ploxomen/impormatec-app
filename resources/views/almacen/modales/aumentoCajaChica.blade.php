<div class="modal fade" id="agregarCajaAumento" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar aumentos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="formCajaChicaAumento">
                <div class="form-group text-right">
                    <button type="button" class="btn btn-sm btn-success" id="btnAgregarAumento">
                        <i class="fas fa-plus"></i>
                        <span>Agregar aumento</span>
                    </button>
                </div>
                <div id="contenidoCajaChicaAumento"></div>
                <input type="submit" hidden id="btnFrmEnviarAumento">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrmAumento">
                <i class="fas fa-save"></i>
                <span>Guardar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                <i class="far fa-times-circle"></i>
                <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>