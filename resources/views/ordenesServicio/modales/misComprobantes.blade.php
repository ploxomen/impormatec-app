<div class="modal fade" id="verComprobantes" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Mis comprobantes</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Comprobante</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaComprobantes"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" id="btnAtrasFrmContacto">
              <i class="far fa-times-circle"></i>
              <span>Cerrar</span>
            </button>
        </div>
      </div>
    </div>
  </div>