<div class="modal fade" id="editarProgramacion" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Programación</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formEditarProgramacion">
                @method('PUT')
                <div class="form-group col-12">
                    <label for="editarModalid_usuario">Responsables</label>
                    <select name="id_usuario" id="editarModalid_usuario" required class="select2-simple">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{$usuario->id}}">{{$usuario->nombres . ' ' . $usuario->apellidos}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="editarModalfecha_hr_inicio">Fecha Hr. Inicio</label>
                    <input type="datetime-local" class="form-control form-control-sm" required id="editarModalfecha_hr_inicio" name="fecha_hr_inicio"/>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="editarModalfecha_hr_fin">Fecha Hr. Fin</label>
                    <input type="datetime-local" class="form-control form-control-sm" required id="editarModalfecha_hr_fin" name="fecha_hr_fin"/>
                </div>
                <div class="form-group col-12">
                    <label for="editarModaltarea">Descripción de la tarea</label>
                    <textarea class="form-control form-control-sm" row="3" required id="editarModaltarea" name="tarea"></textarea>
                </div>
                <input type="submit" hidden id="btnFrmEnviarEditar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrmEditar">
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