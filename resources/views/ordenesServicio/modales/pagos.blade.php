<div class="modal fade" id="generarPagos" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pago a crédito</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-row" id="frmPagoCredito">
            <div class="col-12 col-md-6 col-lg-4 form-group">
                <label for="numeroMesesAgregar">No. Cuotas</label>
                <input type="number" value="6" min="1" max="20" required class="form-control form-control-sm" id="numeroMesesAgregar">
            </div>
            <div class="col-12 col-md-6 col-lg-8 form-group">
                <button class="btn btn-primary btn-sm" title="Agregar cuotas">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="form-group col-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="facturacionExterna">
                    <label class="custom-control-label" for="facturacionExterna">Facturación externa</label>
                </div>
            </div>
            <div class="col-12 form-group">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-caret-right"></i>
                    Lista de cuotas
                </h5>
            </div>
            <div class="col-12 table-responsive">
                <table class="table table-sm table-bordered" style="font-size: 12px; min-width: 1050px;">
                    <thead>
                        <tr>
                            <th scope="col">No. Cuota</th>
                            <th scope="col">Fecha Vencimiento</th>
                            <th scope="col">Fecha Pago</th>
                            <th scope="col">Monto a pagar</th>
                            <th scope="col">Monto pagado</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenidoPagosCuotas">
                        <tr>
                            <th scope="row">1</th>
                            <td>23/10/2023</td>
                            <td>21/10/2023</td>
                            <td>S/ 1500.00</td>
                            <td>S/ 1800.00</td>
                            <td>Avance por el primer proyecto empezado</td>
                            <td>
                                <span class="badge badge-success">Pagado</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap justify-content-center" style="gap: 5px;">
                                    <button class="btn btn-sm btn-danger py-1 px-2" type="button" title="Ver comprobante">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info py-1 modificar-cuota px-2" type="button" title="Modificar cuota y/o pago">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger py-1 px-2" type="button" title="Eliminar cuota">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>23/11/2023</td>
                            <td></td>
                            <td>S/ 1500.00</td>
                            <td></td>
                            <td></td>
                            <td>
                                <span class="badge badge-danger">Por pagar</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap justify-content-center" style="gap: 5px;">
                                    <button class="btn btn-sm btn-info py-1 px-2"  type="button" title="Modificar cuota y/o pago">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger py-1 px-2"  type="button" title="Eliminar cuota">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="submit" hidden id="enviarActa">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnGuardarCambiosActa">Guardar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>