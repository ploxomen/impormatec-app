<div class="modal fade" id="editarVenta" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloProveedor">Editar Venta</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formEditarVenta">
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i> Datos del Comprobante
                    </h5>
                </div>
                <div class="col-12 col-md-6 col-lg-3 form-group">
                    <label for="idModalidtipoComprobanteFk" class="col-form-label col-form-label-sm">Comprobante</label>
                    <select name="tipoComprobanteFk" id="idModalidtipoComprobanteFk" class="form-control form-control-sm" required>
                        @foreach ($comprobantes as $comprobante)
                            <option value="{{$comprobante->id}}">{{$comprobante->comprobante}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3 form-group">
                    <label for="idModalserieComprobante" class="col-form-label col-form-label-sm">Serie</label>
                    <input type="text" name="serieComprobante" id="idModalserieComprobante" class="form-control form-control-sm" required>
                </div>
                <div class="col-6 col-md-6 col-lg-3 form-group">
                    <label for="idModalnumeroComprobante" class="col-form-label col-form-label-sm">Número</label>
                    <input type="text" name="numeroComprobante" id="idModalnumeroComprobante" class="form-control form-control-sm" required>
                </div>
                <div class="col-12 col-md-6 col-lg-3 form-group">
                    <label for="idModalfechaVenta" class="col-form-label col-form-label-sm">Fecha emitida</label>
                    <input type="date" required id="idModalfechaVenta" name="fechaVenta" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i> Datos del Cliente
                    </h5>
                </div>
                <div class="col-12 col-lg-4 col-xl-6 form-group">
                    <label for="idModalclienteFk" class="col-form-label col-form-label-sm">Nombres</label>
                    <select name="clienteFk" id="idModalclienteFk" required class="form-control form-control-sm select2-simple" data-placeholder="Seleccione un cliente">
                        <option value=""></option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-4 col-xl-3 form-group">
                    <label for="idModalTipoDocumentoCliente" class="col-form-label col-form-label-sm">Tipo Documento</label>
                    <select id="idModalTipoDocumentoCliente" class="form-control form-control-sm">
                        <option value="">Ninguno</option>
                        @foreach ($tiposDocumentos as $tipoDocumento)
                            <option value="{{$tipoDocumento->id}}">{{$tipoDocumento->documento}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-4 col-xl-3 form-group">
                    <label for="idModalnroDocumentoCliente" class="col-form-label col-form-label-sm">N° Documento</label>
                    <input type="tel" id="idModalnroDocumentoCliente" class="form-control form-control-sm">
                </div>
                <div class="col-12 form-group">
                    <label for="productoBuscar" class="col-form-label col-form-label-sm">Buscar producto</label>
                    <select id="idModalproductoBuscar" class="form-control form-control-sm">
                        <option value=""></option>
                        @foreach ($productos as $producto)
                            <option value="{{$producto->id}}" data-venta="{{$producto->precioVenta}}" data-venta-mayor="{{$producto->precioVentaPorMayor}}" data-codigo="{{empty($producto->codigoBarra) ? $producto->id : $producto->codigoBarra}}" data-url="{{$producto->urlImagen}}" {{$producto->cantidad > 0 ? "" : "disabled"}}>{{$producto->nombreProducto}}</option>
                        @endforeach
                    </select>
                    <small class="text-info">Los productos mostrados que no se pueden seleccionar es porque no hay stock</small>
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input change-switch" data-selected="PRODUCTO AL POR MENOR" data-noselected="PRODUCTO AL POR MAYOR" 
                        checked id="idModalidVentaPorMenor">
                        <label class="custom-control-label" for="idVentaPorMenor">PRODUCTO AL POR MENOR</label>
                    </div>
                </div>
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i> Detalle de Venta
                    </h5>
                </div>
                <div class="col-12 table-responsive">
                    <table class="table table-sm table-bordered table-striped" id="idModaltablaDetalleVenta">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Imagen</th>
                                <th>Producto</th>
                                <th style="width: 100px; max-width: 100px;">Precio</th>
                                <th style="width: 100px; max-width: 100px;">Cantidad</th>
                                <th style="width: 100px; max-width: 100px;">Descuento<br>S/</th>
                                <th style="width: 130px; max-width: 130px;">Vencimiento</th>
                                <th style="width: 100px; max-width: 100px;">Importe<br>S/</th>
                                <th style="width: 100px; max-width: 100px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr>
                                <td colspan="100%" class="text-center">No se seleccionó ningún producto</td>
                            </tr>
                        </tbody>
                        <tfoot class="text-right">
                            <tr>
                                <td colspan="7">SubTotal</td>
                                <td colspan="2" id="subTotalInfo"></td>
                            </tr>
                            <tr>
                                <td colspan="7">IGV</td>
                                <td colspan="2" id="idModaligvTotal"></td>
                            </tr>
                            <tr>
                                <td colspan="7">Descuento</td>
                                <td colspan="2" id="idModaldescuentoTotal"></td>
                            </tr>
                            <tr>
                                <td colspan="7">Total</td>
                                <td colspan="2" id="idModalsubTotal"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-12">
                    <h5 class="text-primary">
                        <i class="fas fa-caret-right"></i> Pago y Envío
                    </h5>
                </div>
                <div class="form-group col-12 col-md-3 form-group">
                    <label for="idModalmetodoPago" class="col-form-label col-form-label-sm">Metodo de pago</label>
                    <select name="metodoPago" id="idModalmetodoPago" class="form-control form-control-sm" required>
                        <option value="EN EFECTIVO">En efectivo</option>
                        <option value="A CREDITO">A credito</option>
                        <option value="DEPOSITO EN CUENTA">Deposito en cuenta</option>
                        <option value="CON TARJETA">Con tarjeta</option>
                        <option value="BILLETERAS DIGITALES">Billeteras digitales</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 form-group" hidden>
                    <label for="idModalcuentaBancaria" class="col-form-label col-form-label-sm">Cuentas Bancarias</label>
                    <select name="cuentaBancaria" id="idModalcuentaBancaria" class="form-control form-control-sm" disabled>
                        <option value="BCP">BCP</option>
                        <option value="BBVA">BBVA</option>
                        <option value="INTERBANK">INTERBANK</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 form-group" hidden>
                    <label for="idModalbilleteraDigital" class="col-form-label col-form-label-sm">Billeteras digitales</label>
                    <select name="billeteraDigital" id="idModalbilleteraDigital" class="form-control form-control-sm" disabled>
                        <option value="YAPE">Yape</option>
                        <option value="PLIM">Plim</option>
                        <option value="AGORA PAY">Agora PAY</option>
                        <option value="BIM">Bim</option>
                        <option value="OTROS">Otro</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 col-xl-2 form-group" hidden>
                    <label for="idModalnumeroOperacion" class="col-form-label col-form-label-sm">N° de operación</label>
                    <input type="text" name="numeroOperacion" id="idModalnumeroOperacion" class="form-control form-control-sm" disabled>
                </div>
                <div class="form-group col-12 col-md-3 col-xl-2 form-group">
                    <label for="idModalmetodoEnvio" class="col-form-label col-form-label-sm">Metodo de envio</label>
                    <select name="metodoEnvio" id="idModalmetodoEnvio" class="form-control form-control-sm" required>
                        <option value="ENVIO A DOMICILIO">Envio a domicilio</option>
                        <option value="RECOJO EN TIENDA">Recojo en tienda</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 col-xl-2 form-group">
                    <label for="idModalenvio" class="col-form-label col-form-label-sm">Envío S/</label>
                    <input type="number" name="envio" id="idModalenvio" step="0.01" min="0" value="0.00" class="form-control form-control-sm">
                </div>
                <div class="form-group col-12">
                    <div class="px-3 d-flex justify-content-center align-items-center flex-wrap text-center" style="gap: 15px; font-size: 0.8rem;">
                        <div class="dinero bg-white p-2 border" title="El monto es en general, el pago total de los productos más el envio">
                            <i class="far fa-money-bill-alt text-danger"></i>
                            <b>TOTAL A PAGAR</b>
                            <span id="idModaltotal" class="d-block">S/ 0.00</span>
                        </div>
                        <div class="dinero bg-white p-2 border">
                            <i class="far fa-money-bill-alt text-warning"></i>
                            <b>RECIBIDO</b>
                            <input type="number" style="max-width: 100px;" name="montoPagado" id="idModalmontoPagado" step="0.01" min="0" value="0.00" class="form-control form-control-sm text-center" required>
                        </div>
                        <div class="dinero bg-white p-2 border">
                            <i class="far fa-money-bill-alt text-success"></i>
                            <b>VUELTO</b>
                            <span id="idModalvuelto" class="d-block">S/ 0.00</span>
                        </div>
                    </div>
                </div>
                <input type="submit" hidden id="btnFrmEnviar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrm">
                <i class="fas fa-save"></i>
                <span>Actualizar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                <i class="fas fa-eraser"></i>
                <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>