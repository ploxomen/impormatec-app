<div class="modal fade" id="agregarProgramacion" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Programación</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formAgregarProgramacion">
                <div class="form-group col-12">
                    <label for="cbResponsablesAgregar">Responsables</label>
                    <select name="id_usuario" id="cbResponsablesAgregar" required class="select2-simple">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{$usuario->id}}">{{$usuario->nombres . ' ' . $usuario->apellidos}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 d-flex justify-content-between">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Lista de actividades
                    </h5>
                    <button type="button" class="btn btn-sm btn-light" id="btnAgregarActividad">
                        <i class="fas fa-plus"></i> 
                    </button>
                </div>
                <div class="col-12 form-group">
                    <ol id="listaActividades" class="ml-3"></ol>
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
                <i class="far fa-times-circle"></i>
                <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>