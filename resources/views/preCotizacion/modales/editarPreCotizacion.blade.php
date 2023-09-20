<div class="modal fade" id="modalPreCotizacion" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloPreCotizcion">Editar Pre - Cotización</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="contenidoPreCotizacion" class="form-row">
              <h5 class="text-primary col-12">
                  <i class="fas fa-caret-right"></i>
                  Cliente
              </h5>
              <div class="col-12 form-group">
                <label for="cbCliente" class="col-form-label col-form-label-sm">Cliente</label>
                <select name="id_cliente" id="cbCliente" class="form-control select2-simple" data-tags="true" required data-placeholder="Seleccione un cliente">
                    <option value=""></option>
                    @foreach ($clientes as $cliente)
                        <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                    @endforeach
                </select>
                <small hidden class="form-text text-muted">Si es un nuevo cliente no olvides que la contraseña temporal es: sistema{{date('Y')}}@</small>
              </div>
              <div class="form-group col-12 col-md-6 col-lg-4 form-required">
                  <label for="idModaltipoDocumento">Tipo Documento</label>
                  <select name="tipoDocumento" id="idModaltipoDocumento" class="select2-simple limpiar-frm">
                      <option value=""></option>
                      @foreach ($tiposDocumentos as $tipoDocumento)
                          <option value="{{$tipoDocumento->id}}">{{$tipoDocumento->documento}}</option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group col-12 col-md-6 col-lg-4 form-required">
                  <label for="idModalnroDocumento">N° Documento</label>
                  <input type="text" name="nroDocumento" class="form-control limpiar-frm" id="idModalnroDocumento">
              </div>
              <div class="form-group col-12 col-md-6 col-lg-4">
                  <label for="idModalcorreo">Correo</label>
                  <input type="email" name="correo" class="form-control limpiar-frm" id="idModalcorreo" required>
              </div>
              <div class="form-group col-12 col-md-6 col-lg-3">
                  <label for="idModalcelular">Celular</label>
                  <input type="tel" name="celular" class="form-control limpiar-frm" id="idModalcelular">
              </div>
              
              <div class="form-group col-12 col-md-6 col-lg-3">
                  <label for="idModaltelefono">Teléfono</label>
                  <input type="tel" name="telefono" class="form-control limpiar-frm" id="idModaltelefono">
              </div>
              <div class="col-12 col-md-6 form-group">
                  <label for="cbContactoCliente" class="col-form-label col-form-label-sm">Contacto</label>
                  <select name="id_cliente_contacto[]" id="cbContactoCliente" multiple class="form-control select2-simple limpiar-frm" data-placeholder="Seleccione los contactos"></select>
                  <small hidden class="form-text text-muted">Para diferenciar el nombre del número dividelo con un "-" (guion)</small>
              </div>
              <div class="col-12 form-group">
                  <label for="txtdireccion" class="col-form-label col-form-label-sm">Dirección</label>
                  <input type="text" name="direccion" id="idModaldireccion" required class="form-control limpiar-frm">
              </div>
              <h5 class="text-primary col-12">
                  <i class="fas fa-caret-right"></i>
                  Programación
              </h5>
              <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                  <label for="cbTecnicoResponsable" class="col-form-label col-form-label-sm">Técnico responsable</label>
                  <select name="cbTecnicoResponsable" class="form-control select2-simple" id="cbTecnicoResponsable" required data-placeholder="Seleccione un técnico">
                      <option value=""></option>
                      @foreach ($tecnicos as $tecnico)
                          <option value="{{$tecnico->id}}">{{$tecnico->nombres . ' ' . $tecnico->apellidos}}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-12 col-md-6 col-lg-4 col-xl-6 form-group">
                  <label for="cbOtrosTecnicos" class="col-form-label col-form-label-sm">Otros técnicos</label>
                  <select name="cbOtrosTecnicos[]" class="form-control select2-simple" id="cbOtrosTecnicos" multiple data-placeholder="Seleccione un técnico">
                      <option value=""></option>
                      @foreach ($tecnicos as $tecnico)
                          <option value="{{$tecnico->id}}">{{$tecnico->nombres . ' ' . $tecnico->apellidos}}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                  <label for="idModalfechaHrProgramada" class="col-form-label col-form-label-sm">Fecha y Hr. de visita</label>
                  <input type="datetime-local" id="idModalfechaHrProgramada" name="fecha_hr_visita" required class="form-control form-control-sm">
              </div>
              <div class="col-12 form-group">
                  <label for="idModaldetalle" class="col-form-label col-form-label-sm">Detalles</label>
                  <textarea name="detalle" class="form-control" id="idModaldetalle" rows="2"></textarea>
              </div>
              <div class="col-12 col-md-6 col-lg-3 form-group">
                <label for="idModalestado" class="col-form-label col-form-label-sm">Estado</label>
                <select name="estado" class="form-control" required id="idModalestado">
                    <option value="1">Programado</option>
                    <option value="2">Informado</option>
                    <option value="3">Cotizado</option>
                </select>
            </div>
              <input type="submit" hidden id="btnFrmEnviar">
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnEditarPreCotizacion" class="btn btn-outline-primary">
            <i class="fas fa-pencil-alt"></i> 
            <span>Actualizar</span>
          </button>
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><i class="far fa-times-circle"></i> Cerrar</button>
        </div>
      </div>
    </div>
</div>