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
                <label for="cbAgregarSeguimientoPorcentaje">Porcentaje</label>
                <select name="porcentaje" data-placeholder="Seleccione un porcentaje" required class="select2-simple" id="cbAgregarSeguimientoPorcentaje">
                  <option value=""></option>
                  <option value="10" selected>10% cotizado</option>
                  <option value="30">30% en consultas</option>
                  <option value="50">50% en evaluación</option>
                  <option value="75">75% oferta elegida</option>
                  <option value="95">95% pre aprobado</option>
                </select>
            </div>
            <div class="col-12 form-group">
              <div class="custom-control custom-switch">
                <input type="checkbox" name="anular" class="custom-control-input" id="opcionAnular">
                <label class="custom-control-label" for="opcionAnular">Anular cotización</label>
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