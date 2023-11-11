<div class="modal fade" id="generarActaEntrega" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Acta de entrega</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-row" id="frmActa">
            <div class="col-12 col-md-6 form-group">
                <label for="idModalActafirmaEntrega">Responsable entrega</label>
                <select name="usuario_entrega" id="idModalActafirmaEntrega" required data-placeholder="Seleccione el responsable de entrega" class="select2-simple">
                    <option value=""></option>
                    @foreach ($firmasUsuarios as $firmaUsuario)
                        <option value="{{$firmaUsuario->id}}">{{$firmaUsuario->nombres . ' ' . $firmaUsuario->apellidos}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 form-group">
                <label for="idModalActadniRepresentante">DNI representante</label>
                <input required name="dni_representante" type="text" class="form-control form-control-sm" id="idModalActadniRepresentante">
            </div>
            <div class="col-12 form-group">
                <label for="idModalActanombreRepresentante">Nombre representante</label>
                <input required name="nombre_representante" type="text" class="form-control form-control-sm" id="idModalActanombreRepresentante">
            </div>
            <div class="form-group col-12 text-center">
                <span>Firma representante</span>
                <canvas id="idModalActafirma" class="m-auto border d-block" style="width: 300px; height: 150px;"></canvas>
            </div>
            <div class="form-group col-12 text-center">
                <button type="button" class="btn btn-sm btn-danger" id="btnLimpiarFirma">
                    <i class="fas fa-broom"></i>
                    <span>Limpiar firma</span>
                </button>
                <a target="_blank" type="button" class="btn btn-sm btn-success">
                    <i class="fas fa-eye"></i>
                    <span>Ver acta</span>
                </a>
            </div>
            <input type="submit" hidden id="enviarActa">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnGuardarCambiosActa">Guardar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>