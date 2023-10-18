<div class="modal fade" id="agregarPublicidad" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="txtTituloPublicidad">Nueva Publicidad</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formAgregarPublicidad" enctype="multipart/form-data">
                <div class="form-group col-12">
                    <label for="txtModalasunto">Asunto</label>
                    <input type="text" required class="form-control form-control-sm" name="asunto" id="txtModalasunto">
                </div>
                <div class="form-group col-12">
                    <label for="txtModalasunto">Clientes</label>
                    <select name="id_cliente[]" required multiple id="idModalid_cliente" class="form-control select2-simple" required data-placeholder="Seleccione un cliente">
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="envio_cliente" class="custom-control-input" id="enviarTodosClientes">
                        <label class="custom-control-label" for="enviarTodosClientes">Enviar a todos los clientes</label>
                    </div>
                </div>
                <div class="form-group col-12">
                    <label for="txtCuerpoCorreo">Formato correo</label>
                    <textarea id="txtCuerpoCorreo" disabled required></textarea>>
                </div>
                <div class="form-group col-12 d-flex justify-content-between">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Adjuntar documentos
                    </h5>
                    <input type="file" multiple id="fileOtrosDocumentos" hidden>
                    <button type="button" class="btn btn-sm btn-light" id="btnAgregarDocumentos">
                        <i class="fas fa-plus"></i> 
                    </button>
                </div>
                <div class="col-12 form-group">
                    <div class="d-flex flex-wrap" id="contenedorArchivoPdf" style="gap:10px; font-size: 0.8rem;"></div>
                </div>
                <input type="submit" hidden id="btnFrmEnviar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrm">
                <i class="fas fa-save"></i>
                <span>Guardar y enviar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                <i class="far fa-times-circle"></i>
                <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>