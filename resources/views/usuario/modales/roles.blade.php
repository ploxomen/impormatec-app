<div class="modal fade" id="modalRol" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Lista de roles</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <ol class="ml-4" style="column-count: 2;" id="listaRoles">
            @foreach ($roles as $rol)
                <li class="mb-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" data-rol="{{$rol->id}}" id="idSelectModulo{{$rol->id}}">
                        <label class="custom-control-label" for="idSelectModulo{{$rol->id}}">{{$rol->nombreRol}}</label>
                    </div>
                </li>
            @endforeach
          </ol>
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