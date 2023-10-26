<div class="modal fade" id="agragarGastos" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar gastos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formCajaChica">
                <div class="col-12 form-group col-md-6">
                    <label for="idModalbanco">N° OS</label>
                    <select name="id_os" required id="idModalbanco" class="select2-simple" data-placeholder="Seleccionar una orden de servicio">
                        <option value="NINGUNO" selected>NINGUNO</option>
                        @foreach ($ordenesServicios as $os)
                            <option value="{{$os->id}}">{{str_pad($os->id,5,'0',STR_PAD_LEFT)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalfecha_gasto">Fecha</label>
                    <input type="date" value="{{date('Y-m-d')}}" id="idModalfecha_gasto" class="form-control" required name="fecha_gasto">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModaltipo_comprobante">Tipo comprobante</label>
                    <select name="tipo_comprobante" required id="idModaltipo_comprobante" class="select2-simple" data-placeholder="Seleccionar un comprobante">
                        <option value="NINGUNO">NINGUNO</option>
                        <option value="BOLETA">BOLETA</option>
                        <option value="FACTURA">FACTURA</option>
                        <option value="GUIA">GUIA</option>
                        <option value="OTRO">OTRO</option>
                    </select>
                </div>
                <div class="col-12 form-group col-md-6">
                    <label for="idModalnro_comprobante">N° Comprobante</label>
                    <input type="text" id="idModalnro_comprobante" class="form-control" name="nro_comprobante">
                </div>
                <div class="col-12 form-group col-md-6">
                    <label for="idModalproveedor">Proveedor</label>
                    <input type="text" id="idModalproveedor" class="form-control" name="proveedor">
                </div>
                <div class="col-12 form-group col-md-6">
                    <label for="idModalproveedor_ruc">RUC Proveedor</label>
                    <input type="text" id="idModalproveedor_ruc" class="form-control" name="proveedor_ruc">
                </div>
                <div class="col-12 form-group col-md-6">
                    <label for="idModalarea_costo">Área de costo</label>
                    <select name="area_costo" required id="idModalarea_costo" class="select2-simple" data-placeholder="Seleccionar un responsable">
                        <option value="VENTAS" selected>VENTAS</option>
                        <option value="TALLER">TALLER</option>
                        <option value="ADMINISTRACION">ADMINISTRACION</option>
                        <option value="OTRO">OTRO</option>
                    </select>
                </div>
                <div class="col-12 form-group">
                    <label for="idModaldescripcion_producto">Descripción del producto</label>
                    <textarea required maxlength="500" name="descripcion_producto" id="idModaldescripcion_producto"  class="form-control" rows="3"></textarea>
                </div>
                <div class="col-12 form-group col-12 col-md-6">
                    <label for="idModaltipo_moneda">Tipo moneda</label>
                    <select name="tipo_moneda" required id="idModaltipo_moneda" class="select2-simple" data-placeholder="Seleccionar un tipo moneda">
                        <option value=""></option>
                        <option value="PEN" {{$cajaChica->tipo_moneda === "PEN" ? 'selected' : ''}}>Soles</option>
                        <option value="USD" {{$cajaChica->tipo_moneda === "USD" ? 'selected' : ''}}>Dolares</option>
                    </select>
                </div>
                <div class="col-12 form-group col-md-6">
                    <label for="idModaltipo_cambio">Tipo de cambio</label>
                    <input type="number" step="0.01" value="3.8" min="0.01" id="idModaltipo_cambio" class="form-control" name="tipo_cambio">
                </div>
                <div class="col-12 form-group col-md-6">
                    <label for="idModalmonto_total">Monto total</label>
                    <input type="number" required step="0.01" min="0.01" id="idModalmonto_total" class="form-control" name="monto_total">
                </div>
                <div class="col-12 form-group col-md-6">
                    <label for="idModaligv">I.G.V</label>
                    <input type="number" value="0.00" step="0.01" min="0.01" id="idModaligv" class="form-control" name="igv">
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