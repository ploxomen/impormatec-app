@extends('general.index')
@section('head')
    @if ($caja == 'caja abierta')
        <script src="/general.js')}}"></script>
        <link rel="stylesheet" href="/productos/pestilos.css')}}">
        <script src="/ventas/nuevaVenta.js')}}"></script>
    @endif
    <title>Nueva venta</title>
@endsection
@section('body')
    @if ($caja == 'caja abierta')
        <section class="p-3">
            <div class="mb-4">
                <div class="m-auto" style="max-width: 400px;">
                    <img src="/img/modulo/ventas.png')}}" alt="Imagen de ventas" width="120px" class="img-fluid d-block m-auto">
                    <h4 class="text-center text-primary my-2">Nueva Venta</h4>
                </div>
            </div>
            <form id="generarVenta" class="form-row">
                <div class="form-group col-12 col-md-6">
                    <fieldset class="bg-white col-12 px-3 border form-row">
                        <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos del Comprobante</legend>
                            <div class="col-12 col-lg-6 form-group">
                                <label for="idVentatipoComprobanteFk" class="col-form-label col-form-label-sm">Comprobante</label>
                                <select name="tipoComprobanteFk" id="idVentatipoComprobanteFk" class="form-control form-control-sm" required>
                                    @foreach ($comprobantes as $comprobante)
                                        <option value="{{$comprobante->id}}">{{$comprobante->comprobante}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-lg-3 form-group">
                                <label for="SerieVenta" class="col-form-label col-form-label-sm">Serie</label>
                                <input type="text" id="SerieVenta" value="{{$numeroComprobante->serie}}" class="form-control form-control-sm" disabled required>
                            </div>
                            <div class="col-6 col-lg-3 form-group">
                                <label for="numeroVenta" class="col-form-label col-form-label-sm">Número</label>
                                <input type="text" id="numeroVenta" value="{{str_pad($numeroComprobante->inicio + $numeroComprobante->utilizados,strlen($numeroComprobante->fin),"0",STR_PAD_LEFT)}}" disabled class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12 form-group">
                                <label for="idfechaVenta" class="col-form-label col-form-label-sm">Fecha emitida</label>
                                <input type="date" required id="idfechaVenta" name="fechaVenta" min="{{date('Y-m-d',strtotime(date('Y-m-d') . " - 2 days"))}}" class="form-control form-control-sm" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}">
                            </div>                    
                    </fieldset>
                </div>
                <div class="form-group col-12 col-md-6">
                    <fieldset class="bg-white col-12 px-3 border form-row">
                        <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos del Cliente</legend>
                            <div class="col-12 form-group">
                                <label for="idVentaCliente" class="col-form-label col-form-label-sm">Nombres</label>
                                <select name="clienteFk" id="idVentaCliente" required class="form-control form-control-sm select2-simple" data-placeholder="Seleccione un cliente">
                                    <option value=""></option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 form-group">
                                <label for="tipoDocumentoCliente" class="col-form-label col-form-label-sm">Tipo Documento</label>
                                <select id="tipoDocumentoCliente" disabled class="form-control form-control-sm">
                                    <option value=""></option>
                                    @foreach ($tiposDocumentos as $tipoDocumento)
                                        <option value="{{$tipoDocumento->id}}">{{$tipoDocumento->documento}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 form-group">
                                <label for="nroDocumentoCliente" class="col-form-label col-form-label-sm">N° Documento</label>
                                <input type="tel" id="nroDocumentoCliente" disabled class="form-control form-control-sm">
                            </div>
                    </fieldset>
                </div>
                <div class="form-group col-12">
                    <fieldset class="bg-white col-12 px-3 border form-row">
                        <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Productos</legend>
                        <div class="col-12 form-group">
                            <label for="productoBuscar" class="col-form-label col-form-label-sm">Buscar producto</label>
                            <select id="productoBuscar" class="form-control form-control-sm">
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
                                checked id="idVentaPorMenor">
                                <label class="custom-control-label" for="idVentaPorMenor">PRODUCTO AL POR MENOR</label>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-12">
                    <fieldset class="bg-white col-12 px-3 border form-row">
                        <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Detalle venta</legend>
                        <div class="col-12 table-responsive">
                            <table class="table table-sm table-bordered table-striped" id="tablaDetalleVenta">
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
                                        <td colspan="2" id="tDetalleSubTotal"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">IGV</td>
                                        <td colspan="2" id="tDetalleIgv"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">Descuento</td>
                                        <td colspan="2" id="tDetalleDescuento"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">Total</td>
                                        <td colspan="2" id="tDetalleTotal"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-12">
                    <fieldset class="bg-white px-3 border form-row">
                        <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Pago y Envío</legend>
                        {{-- <div class="form-group col-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="estado" class="custom-control-input change-switch" data-selected="VENTA AL CONTADO" data-noselected="VENTA AL CREDITO" 
                                checked id="idVentaContado">
                                <label class="custom-control-label" for="idVentaContado">VENTA AL CONTADO</label>
                            </div>
                        </div> --}}
                        <div class="form-group col-12 col-md-3 form-group">
                            <label for="idVentaPago" class="col-form-label col-form-label-sm">Metodo de pago</label>
                            <select name="metodoPago" id="idVentaPago" class="form-control form-control-sm" required>
                                <option value="EN EFECTIVO">En efectivo</option>
                                <option value="A CREDITO">A credito</option>
                                <option value="DEPOSITO EN CUENTA">Deposito en cuenta</option>
                                <option value="CON TARJETA">Con tarjeta</option>
                                <option value="BILLETERAS DIGITALES">Billeteras digitales</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-3 form-group" hidden>
                            <label for="idCuentaBancaria" class="col-form-label col-form-label-sm">Cuentas Bancarias</label>
                            <select name="cuentaBancaria" id="idCuentaBancaria" class="form-control form-control-sm" disabled>
                                <option value="BCP">BCP</option>
                                <option value="BBVA">BBVA</option>
                                <option value="INTERBANK">INTERBANK</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-3 form-group" hidden>
                            <label for="idVentaBilleteraDigital" class="col-form-label col-form-label-sm">Billeteras digitales</label>
                            <select name="billeteraDigital" id="idVentaBilleteraDigital" class="form-control form-control-sm" disabled>
                                <option value="YAPE">Yape</option>
                                <option value="PLIM">Plim</option>
                                <option value="AGORA PAY">Agora PAY</option>
                                <option value="BIM">Bim</option>
                                <option value="OTROS">Otro</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-3 col-xl-2 form-group" hidden>
                            <label for="idVentaNumeroOperacion" class="col-form-label col-form-label-sm">N° de operación</label>
                            <input type="text" name="numeroOperacion" id="idVentaNumeroOperacion" class="form-control form-control-sm" disabled>
                        </div>
                        <div class="form-group col-12 col-md-3 col-xl-2 form-group">
                            <label for="idMetodoEnvio" class="col-form-label col-form-label-sm">Metodo de envio</label>
                            <select name="metodoEnvio" id="idMetodoEnvio" class="form-control form-control-sm" required>
                                <option value="ENVIO A DOMICILIO">Envio a domicilio</option>
                                <option value="RECOJO EN TIENDA">Recojo en tienda</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-3 col-xl-2 form-group">
                            <label for="idVentaEnvio" class="col-form-label col-form-label-sm">Envío S/</label>
                            <input type="number" name="envio" id="idVentaEnvio" step="0.01" min="0" value="0.00" class="form-control form-control-sm">
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-12">
                    <div class="px-3 d-flex justify-content-center align-items-center flex-wrap text-center" style="gap: 15px; font-size: 0.8rem;">
                        <div class="dinero bg-white p-2 border" title="El monto es en general, el pago total de los productos más el envio">
                            <i class="far fa-money-bill-alt text-danger"></i>
                            <b>TOTAL A PAGAR</b>
                            <span id="totalApagarEnvio" class="d-block">S/ 0.00</span>
                        </div>
                        <div class="dinero bg-white p-2 border">
                            <i class="far fa-money-bill-alt text-warning"></i>
                            <b>RECIBIDO</b>
                            <input type="number" style="max-width: 100px;" name="montoPagado" id="idMontoDado" step="0.01" min="0" value="0.00" class="form-control form-control-sm text-center" required>
                        </div>
                        <div class="dinero bg-white p-2 border">
                            <i class="far fa-money-bill-alt text-success"></i>
                            <b>VUELTO</b>
                            <span id="vueltoAdar" class="d-block">S/ 0.00</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 form-group text-center">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-shopping-basket"></i>
                        <span>Agregar venta</span>
                    </button>
                </div>
            </form>
        </section>
    @else
    <section class="container">
        <div class="bg-white p-3">
            <div class="form-group">
                <img src="/img/modulo/caja_cerrada.png')}}" alt="Imagen de cerrar caja" width="160px" class="img-fluid d-block m-auto">
            </div>
            <div class="form-group">
                <h3 class="text-center">{{$fechaLarga}}</h3>
            </div>
            <div class="form-group text-center">
                <p class="text-center"><strong class="text-danger">LA CAJA ESTÁ CERRADA</strong>, por favor solicitar al administrador o encargado correspondiente que abra la caja para registrar una nueva venta</p>
            </div>
        </div>
    </section>
    @endif
    
@endsection