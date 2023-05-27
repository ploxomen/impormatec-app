<div class="modal fade" id="agregarPerecedero" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloPerecedero">Agregar Perecedero</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formPerecedero">
                <div class="form-group col-12">
                    <label for="idModalproductoFk">Producto</label>
                    <select name="productoFk" id="idModalproductoFk" required class="select2-simple">
                        <option value=""></option>
                        @foreach ($productos as $producto)
                            <option value="{{$producto->id}}">{{$producto->nombreProducto}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalvencimiento">Vencimiento</label>
                    <input type="date" id="idModalvencimiento" required class="form-control" name="vencimiento" min="{{date('Y-m-d')}}">
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalcantidad">Cantidad</label>
                    <input type="number" id="idModalcantidad" required class="form-control" name="cantidad">
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