<div class="modal fade" id="agregarCaja" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloCajaChica">Agregar caja chica</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formCajaChica">
                <div class="form-group col-12 col-md-6">
                    <label for="idModalfecha_inicio">Fecha Inicio</label>
                    <input type="date" value="{{date('Y-m-d')}}" id="idModalfecha_inicio" class="form-control" required name="fecha_inicio">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalfecha_fin">Fecha Fin</label>
                    <input type="date" value="{{date('Y-m-d',strtotime(date('Y-m-d') . ' + 15 days'))}}" id="idModalfecha_fin" class="form-control" required name="fecha_fin">
                </div>
                <div class="col-12 form-group col-md-6">
                    <label for="idModalbanco">Banco</label>
                    <select name="banco" id="idModalbanco" class="select2-simple" data-placeholder="Seleccionar un banco">
                        <option value=""></option>
                        <option value="BCP">Banco de Crido del Perú</option>
                        <option value="BBVA">BBVA</option>
                        <option value="INTERBANK">INTERBANK</option>
                        <option value="SCOTIABANK">SCOTIABANK</option>
                        <option value="OTRO">OTRO</option>
                    </select>
                </div>
                <div class="col-12 form-group col-md-6">
                    <label for="idModalnro_operacion">N° Operación</label>
                    <input type="text" id="idModalnro_operacion" class="form-control" name="nro_operacion">
                </div>
                <div class="col-12 form-group col-12 col-md-6">
                    <label for="idModaltipo_moneda">Tipo moneda</label>
                    <select name="tipo_moneda" required id="idModaltipo_moneda" class="select2-simple" data-placeholder="Seleccionar un tipo moneda">
                        <option value=""></option>
                        <option value="PEN" selected>Soles</option>
                        <option value="USD">Dolares</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 form-group">
                    <label for="idModalmonto_abonado">Monto abonado</label>
                    <input type="number" step="0.01" min="1" id="idModalmonto_abonado" class="form-control" required name="monto_abonado">
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="estado" class="custom-control-input change-switch" data-selected="ABIERTO" data-noselected="CERRADO" checked id="idModalestado">
                        <label class="custom-control-label" for="idModalestado">ABIERTO</label>
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
                <i class="far fa-times-circle"></i>
                <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>