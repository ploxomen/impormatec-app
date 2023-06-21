<div class="modal fade" id="agregarServicio" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloServicio">Crear Servicio</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formServicio">
                <div class="form-group col-12">
                    <label for="idModalservicio">Servicio</label>
                    <input type="text" id="idModalservicio" class="form-control" required name="servicio">
                </div>
                <div class="col-12 form-group">
                    <label for="idModaldescripcion" class="col-form-label col-form-label-sm">Descripción</label>
                    <textarea name="descripcion" id="idModaldescripcion" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="estado" class="custom-control-input change-switch" data-selected="VIGENTE" data-noselected="DESCONTINUADO" disabled checked id="idModalestado">
                        <label class="custom-control-label" for="idModalestado">VIGENTE</label>
                    </div>
                </div>
                <div class="col-12">
                  <h5 class="text-primary">
                      <i class="fas fa-caret-right"></i>
                      Datos de productos
                  </h5>
                </div>
                <div class="form-group col-12">
                    <label for="cbProductos">Productos disponibles</label>
                    <select id="cbProductos" data-placeholder="Seleccione un producto" class="select2-simple">
                        <option value=""></option>
                        @foreach ($productos as $producto)
                            <option value="{{$producto->id}}" data-url="{{$producto->urlImagen}}">{{$producto->nombreProducto}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12">
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th>Imagen</th>
                          <th style="min-width: 150px;">Producto</th>
                          <th>Cantidad</th>
                          <th>Eliminar</th>
                        </tr>
                      </thead>
                      <tbody id="detalleProductos">
                        <tr>
                          <td class="text-center" colspan="4">No se seleccionaron productos</td>
                        </tr>
                      </tbody>
                    </table>
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