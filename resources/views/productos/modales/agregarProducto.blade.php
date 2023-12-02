<div class="modal fade" id="agregarProducto" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloProducto">Agregar Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formProducto">
                <div class="form-group col-12 form-required">
                    <label for="idModalnombreProducto">Producto</label>
                    <input type="text" name="nombreProducto" class="form-control" id="idModalnombreProducto" required>
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" name="esIntangible" value="1" id="switchProductoIntangible">
                        <label class="custom-control-label" for="switchProductoIntangible">Productos intangibles</label>
                    </div>
                </div>
                <div class="form-group col-12 form-required">
                    <label for="idModalnombreProveedor">Proveedor</label>
                    <input type="text" name="nombreProveedor" maxlength="255" class="form-control" id="idModalnombreProveedor">
                </div>
                <div class="form-group col-12">
                    <label for="idModaldescripcion">Descripción</label>
                    <textarea name="descripcion" id="idModaldescripcion" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group col-12 col-md-6">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="tipoMonedaSoles" checked required name="tipoMoneda" value="PEN" class="custom-control-input">
                        <label class="custom-control-label" for="tipoMonedaSoles">PEN (Soles)</label>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="tipoMonedaDolares" value="USD" required name="tipoMoneda" class="custom-control-input">
                        <label class="custom-control-label" for="tipoMonedaDolares">USD (Dólares)</label>
                    </div>
                </div>
                <div class="form-group col-6">
                    <label for="idModalprecioCompra">Precio Compra</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalprecioCompra" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" required value="0.00" name="precioCompra" min="0" step="0.01" class="form-control" id="idModalprecioCompra">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalprecioCompra" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalutilidad">Utilidad (%)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalutilidad" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" name="utilidad" step="0.01" max="100" class="form-control" id="idModalutilidad" required>
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalutilidad" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalprecioVenta">Precio Venta</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalprecioVenta" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" value="0.00" name="precioVenta" min="0" step="0.01" class="form-control" id="idModalprecioVenta" required>
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalprecioVenta" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-6">
                    <label for="idModalcantidadMin">Stock Min</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalcantidadMin" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" value="0" name="stockMin" min="0" class="form-control" id="idModalcantidadMin">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalcantidadMin" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-md-8">
                    <label for="customFileLang">Imagen del Producto</label>
                    <input type="file" name="urlImagen" class="form-control-file form-control-sm" accept="image/*" id="customFileLang">
                </div>
                <div class="form-group col-12 col-md-4">
                    <label class="mb-0">Imagen Previa</label>
                    <button class="btn btn-sm btn-light" type="button" id="btnEliminarImagen" title="Eliminar imagen">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <div>
                        <img src="/img/imgprevproduc.png" class="img-vistas-pequena" id="imgPrevio" alt="Imagen del producto">
                    </div>
                </div>
                <div class="col-12 producto-tangible">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i>
                        Datos de almacen
                    </h5>
                </div>
                <div class="form-group col-12 producto-tangible">
                    <label for="cbAlmacen">Almacenes disponibles</label>
                    <select id="cbAlmacen" data-placeholder="Seleccione un almacen" class="select2-simple">
                        <option value=""></option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{$almacen->id}}">{{$almacen->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 producto-tangible">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Almacen</th>
                                    <th style="width: 20px;">Stock</th>
                                    <th style="width: 20px;">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody id="listaAlmacenes">
                                <tr>
                                    <td colspan="100%" class="text-center">No se seleccionaron almacenes</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                <i class="far fa-times-circle"></i>
                <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>