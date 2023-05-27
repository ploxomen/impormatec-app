<div class="modal fade" id="usurioModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloUsuario">Crear usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="frmUsuario">
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos personales
                    </h5>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idValorModaltipoDocumento">Tipo de documento</label>
                    <select name="tipoDocumento" id="idValorModaltipoDocumento" class="form-control">
                        <option></option>
                        @foreach ($tiposDocumentos as $tipoDocumento)
                            <option value="{{$tipoDocumento->id}}">{{$tipoDocumento->documento}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idValorModalnroDocumento">N° de documento</label>
                    <input type="tel" name="nroDocumento" id="idValorModalnroDocumento" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idValorModalnombres">Nombres</label>
                    <input type="text" required name="nombres" id="idValorModalnombres" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idValorModalapellidos">Apellidos</label>
                    <input type="text" required name="apellidos" id="idValorModalapellidos" class="form-control">
                </div>
                <div class="form-group col-12">
                    <label for="idValorModalcorreo">Correo</label> 
                    <input type="email" required class="form-control" name="correo" id="idValorModalcorreo">
                    <small class="form-text text-muted">Con este correo se inicia sesión en el sistema</small>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idValorModalcelular">Celular</label> 
                    <input type="tel" class="form-control" name="celular" id="idValorModalcelular">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idValorModaltelefono">Telefono</label>
                    <input type="tel"  name="telefono" id="idValorModaltelefono" class="form-control">
                </div>
                <div class="form-group col-12">
                    <label for="idValorModaldireccion">Direccion</label>
                    <input type="text"  name="direccion" id="idValorModaldireccion" class="form-control">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idValorModalfechaNacimiento">Fecha Nacimiento</label> 
                    <input type="date" class="form-control" name="fechaNacimiento" id="idValorModalfechaNacimiento">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idValorModalsexo">Sexo</label> 
                    <select name="sexo" id="idValorModalsexo" class="form-control select2-simple">
                        <option value="">Ninguno</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos del sistema
                    </h5>
                </div>
                <div class="form-group col-12 form-required">
                    <label for="idValorModalroles">Roles</label>
                    <select name="roles[]" class="form-control select2-simple" multiple id="idValorModalroles" required>
                        @foreach ($roles as $rol)
                            <option value="{{$rol->id}}">{{$rol->nombreRol}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 form-required" id="boxContrasena">
                    <label for="txtContrasena">Contraseña</label> 
                    <input type="text" required class="form-control" name="password" id="txtContrasena" minlength="8" value="sistema{{date('Y')}}">
                    <small class="form-text text-muted">Esta contraseña es temporal hasta que el usuario ingrese por primera vez</small>
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