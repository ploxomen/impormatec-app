<div class="modal fade" id="usuarioRestaurar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Restaurar contraseña</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="formRestaurar">
              <div class="form-group">
                <label for="id_password_temp">Contraseña temporal</label>
                <input type="text" class="form-control" name="password_temp" required value="sistema{{date('Y')}}" data-value="sistema{{date('Y')}}" id="id_password_temp">
              </div>
                
                <input type="submit" id="btnSubmitRest" hidden>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrmRest">
                <i class="fas fa-save"></i>
                <span>Restaurar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-eraser"></i>
                    <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>