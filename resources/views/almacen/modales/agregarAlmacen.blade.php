<div class="modal fade" id="agregarAlmacen" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloAlmacen">Agregar Almacen</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formAlmacen">
                <div class="form-group col-12">
                    <label for="idModalnombre">Nombre</label>
                    <input type="text" id="idModalnombre" class="form-control" required name="nombre">
                </div>
                <div class="col-12 form-group">
                    <label for="idModaldescripcion" class="col-form-label col-form-label-sm">Descripción</label>
                    <textarea name="descripcion" id="idModaldescripcion" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group col-12">
                    <label for="idModaldireccion">Dirección</label>
                    <input type="text" id="idModaldireccion" class="form-control" required name="direccion">
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="estado" class="custom-control-input change-switch" data-selected="VIGENTE" data-noselected="DESCONTINUADO" disabled checked id="idModalestado">
                        <label class="custom-control-label" for="idModalestado">VIGENTE</label>
                    </div>
                </div>
                <input type="submit" hidden id="btnFrmEnviar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrm">
                <i class="fas fa-save"></i>
                <span>Guardar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                <i class="fas fa-eraser"></i>
                <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>