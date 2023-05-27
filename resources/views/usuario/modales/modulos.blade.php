<div class="modal fade" id="modalRol" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Lista de módulos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="font-size: 0.85em;">
            <div class="form-group">
                <label for="buscarModulo" class="mb-0">
                    <span class="material-icons">search</span>
                    <span>Buscar módulo</span>
                </label>
                <input type="search" id="buscarModulo" class="form-control form-control-sm">
            </div>
            <div class="form-group d-flex justify-content-between">
                <p class="text-info mb-0">
                    <i class="fas fa-globe-americas"></i>
                    Total de módulos:
                    <strong>{{$modulosLista->count()}}</strong>
                </p>
                <p class="text-primary mb-0">
                    <i class="fas fa-check"></i>
                    Módulos seleccionados:
                    <strong id="textoInfoSelecionado">0</strong>
                </p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered" style="font-size: 0.85em;min-width: 700px;">
                <thead class="text-center">
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="selecionarTodoCheckbox">
                                <label class="custom-control-label" for="selecionarTodoCheckbox"></label>
                            </div>
                        </th>
                        <th>
                            <span>Grupo</span>
                            <b class="text-info mx-1"><i class="fas fa-chevron-right"></i></b>
                            <span>Módulo</span>
                        </th>
                        <th>Descipción</th>
                    </tr>
                </thead>
                <tbody class="text-secondary" id="modulosBuscar">
                    @foreach ($modulosLista as $modulo)
                        <tr>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" data-modulo="{{$modulo->id}}" id="idSelectModulo{{$modulo->id}}">
                                <label class="custom-control-label" for="idSelectModulo{{$modulo->id}}"></label>
                                </div>
                            </td>
                            <td style="max-width: 100px;">
                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <span class="material-icons">{{$modulo->grupos->icono}}</span> 
                                    <span class="grupo-buscar">{{$modulo->grupos->grupo}}</span>
                                    <b class="text-info mx-1"><i class="fas fa-chevron-right"></i></b>
                                    <span class="material-icons">{{$modulo->icono}}</span>
                                    <span class="titulo-buscar">{{$modulo->titulo}}</span>
                                </div>
                            </td>
                            <td>
                                @empty($modulo->discription)
                                    <span>No establacido</span>
                                @else
                                    <p class="descripcion-buscar">{{$modulo->discription}}</p>
                                @endempty
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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