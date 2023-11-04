<div class="modal fade" id="egregarSeguimiento" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo seguimiento</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="frmAgregarSeguimiento" class="form-row">
            <div class="col-12">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Datos del seguimiento
                </h5>
            </div>
            <div class="form-group col-12">
                <label for="txtAgregarPorcentaje">Porcentaje</label>
                <div class="input-group mb-2">
                    <input type="number" name="porcentaje" min="1" required max="100" step="0.01" class="form-control" id="txtAgregarPorcentaje">
                    <div class="input-group-prepend">
                        <div class="input-group-text">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group col-12">
                <label for="txtAgregarDescripcion">Descripción</label>
                <textarea class="form-control" name="descripcion" required name="descripcion" maxlength="500" id="txtAgregarDescripcion" rows="4"></textarea>
            </div>
            <div class="col-12">
                <h5 class="text-primary">
                    <i class="fas fa-caret-right"></i>
                    Historial de seguimiento
                </h5>
            </div>
            <div class="form-group col-12" id="contenidoHistorialSeguimiento"></div>
            <input type="submit" id="btnAgregarSeguimiento" hidden>
          </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btnGuardarFrm">
                <i class="far fa-save"></i>
                <span>Agregar</span>
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="far fa-times-circle"></i>
                <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>