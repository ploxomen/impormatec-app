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
                <div class="form-group col-12">
                    <label for="idModalcodigoBarra">Codigo de Barra</label>
                    <input type="text" class="form-control" name="codigoBarra" id="idModalcodigoBarra">
                </div>
                <div class="form-group col-12 form-required">
                    <label for="idModalnombreProducto">Producto</label>
                    <input type="text" name="nombreProducto" class="form-control" id="idModalnombreProducto" required>
                </div>
                <div class="form-group col-12">
                    <label for="idModaldescripcion">Descripción</label>
                    <textarea name="descripcion" id="idModaldescripcion" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalcantidad">Stock</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalcantidad" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" name="cantidad" value="0" min="0" class="form-control" id="idModalcantidad" required>
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalcantidad" data-accion="aumentar">
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
                        <input type="number" value="0" name="cantidadMin" min="0" class="form-control" id="idModalcantidadMin">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalcantidadMin" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
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
                        <input type="number" value="0.00" name="precioCompra" min="0" step="0.01" class="form-control" id="idModalprecioCompra">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalprecioCompra" data-accion="aumentar">
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
                    <label for="idModalprecioVentaPorMayor">Precio Venta Mayor</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalprecioVentaPorMayor" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" value="0.00" name="precioVentaPorMayor" min="0" step="0.01" class="form-control" id="idModalprecioVentaPorMayor">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad" data-number="#idModalprecioVentaPorMayor" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalcategoriaFk">Categoría</label>
                    <select name="categoriaFk" id="idModalcategoriaFk" class="select2-simple">
                        @foreach ($categorias as $categoria)
                            <option value="{{$categoria->id}}">{{$categoria->nombreCategoria}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalmarcaFk">Marca</label>
                    <select name="marcaFk" id="idModalmarcaFk" class="select2-simple">
                        @foreach ($marcas as $marca)
                            <option value="{{$marca->id}}">{{$marca->nombreMarca}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-6 form-required">
                    <label for="idModalpresentacionFk">Presentación</label>
                    <select name="presentacionFk" id="idModalpresentacionFk" class="select2-simple">
                        @foreach ($presentaciones as $presentacion)
                            <option value="{{$presentacion->id}}">{{$presentacion->nombrePresentacion}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-8">
                    <label for="customFileLang">Imagen del Producto</label>
                    <input type="file" name="urlImagen" class="form-control-file form-control-sm" accept="image/*" id="customFileLang">
                </div>
                <div class="form-group col-12 col-md-4">
                    <label class="mb-0">Imagen Previa</label>
                    <div>
                        <img src="{{asset('img/imgprevproduc.png')}}" id="imgPrevio" alt="Imagen del producto" width="80px">
                    </div>
                </div>
                <div class="form-group col-12">
                    <div class="d-flex flex-wrap" style="gap: 20px;">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="estado" class="custom-control-input change-switch" data-selected="VIGENTE" data-noselected="DESCONTINUADO" disabled checked id="idModalestado">
                            <label class="custom-control-label" for="idModalestado">VIGENTE</label>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="igv" class="custom-control-input change-switch" data-selected="CON IGV" data-noselected="SIN IGV" checked id="idModaligv">
                            <label class="custom-control-label" for="idModaligv">CON IGV</label>
                        </div>
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