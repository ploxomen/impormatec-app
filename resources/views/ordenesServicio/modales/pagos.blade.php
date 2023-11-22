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
                  <label for="numeroCuotas">No. Cuotas</label>
                  <input type="number" value="6" min="1" max="20" id="numeroCuotas" required class="form-control form-control-sm" name="numeroCuota">
              </div>
              <div class="col-12 col-md-6 col-lg-8 form-group">
                  <button class="btn btn-primary btn-sm" type="submit" title="Agregar cuotas" id="btnAgregarCuotas">
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
            </form>
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
                    <tbody id="contenidoPagosCuotas"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>