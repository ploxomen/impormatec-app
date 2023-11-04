<div class="modal fade" id="agregarSeccion" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloSeccion">Agregar una sección</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form action=""></form>
            <form class="form-row" id="frmSeccionNueva">
                <div class="form-group col-12">
                    <label for="idModalSecciontitulo">Titulo de la sección</label>
                    <textarea required class="form-control form-control-sm" name="titulo" id="idModalSecciontitulo" rows="4"></textarea>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalSeccioncolumnas">Columnas Reporte</label>
                    <select name="columnas" required id="idModalSeccioncolumnas" class="form-control form-control-sm">
                        <option value="2">2 columnas</option>
                        <option value="3" selected>3 columnas</option>
                        <option value="4">4 columnas</option>
                    </select>
                </div>
                <input type="submit" id="btnSeccionAgregar" hidden>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrmSeccion">
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