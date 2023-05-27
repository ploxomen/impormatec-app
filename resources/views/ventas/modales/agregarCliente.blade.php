<div class="modal fade" id="agregarCliente" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="titulocliente">Agregar cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formCliente">
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del cliente
                    </h5>
                </div>
                <div class="form-group col-12 col-md-6 form-required">
                    <label for="idModaltipoDocumento">Tipo Documento</label>
                    <select name="tipoDocumento" id="idModaltipoDocumento" class="select2-simple">
                        <option value=""></option>
                        @foreach ($tiposDocumentos as $tipoDocumento)
                            <option value="{{$tipoDocumento->id}}">{{$tipoDocumento->documento}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 form-required">
                    <label for="idModalnroDocumento">N° Documento</label>
                    <input type="text" name="nroDocumento" class="form-control" id="idModalnroDocumento">
                </div>
                <div class="form-group col-12">
                    <label for="idModalnombreCliente">Nombres</label>
                    <input type="text" name="nombreCliente" class="form-control" id="idModalnombreCliente" required>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalcelular">Celular</label>
                    <input type="tel" name="celular" class="form-control" id="idModalcelular">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModaltelefono">Teléfono</label>
                    <input type="tel" name="telefono" class="form-control" id="idModaltelefono">
                </div>
                <div class="form-group col-12">
                    <label for="idModaldireccion">Dirección</label>
                    <input type="text" name="direccion" id="idModaldireccion" rows="3" class="form-control">
                </div>
                <div class="form-group col-12 d-flex justify-content-between">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos de los contactos
                    </h5>
                    <button type="button" class="btn btn-sm btn-light" id="btnAgregarContacto">
                        <i class="fas fa-plus"></i> 
                    </button>
                </div>
                <div class="col-12">
                    <p class="text-info text-center" id="txtSinContacto">Sin contactos</p>
                    <ol id="listaContactos" class="ml-3"></ol>
                </div>
                <div class="col-12 ocultar-editar">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del sistema
                    </h5>
                </div>
                <div class="ocultar-editar col-12">
                    <small class="text-danger">Por lo general al crear un cliente se le asignará el rol <strong>cliente</strong></small>
                </div>
                <div class="form-group ocultar-editar col-12 form-required">
                    <label for="idModalcorreo">Correo</label>
                    <input type="email" required name="correo" class="form-control ocultar-editar" id="idModalcorreo">
                </div>
                <div class="form-group ocultar-editar col-12 form-required" id="boxContrasena">
                    <label for="txtContrasena">Contraseña</label> 
                    <input type="text" required class="form-control ocultar-editar" name="password" id="txtContrasena" minlength="8" value="sistema{{date('Y')}}">
                    <small class="form-text text-muted">Esta contraseña es temporal hasta que el usuario ingrese por primera vez</small>
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