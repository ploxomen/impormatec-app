<div class="modal fade" id="almacenProductosCotizacion" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" >Asignar almacen a los productos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="ml-3" id="contenidoProductos" hidden>
            <h5 class="text-primary">
              <i class="fas fa-caret-right"></i>
              <span class="text-primary">Productos tangibles</span>
            </h5>
            <table class="table-sm table-bordered table">
              <thead>
                <tr>
                  <th>N°</th>
                  <th>IMAGEN</th>
                  <th>DESCRIPCION</th>
                  <th>CANT.</th>
                  <th>ALMACEN</th>
                </tr>
              </thead>
              <tbody id="contanidoTablaProductos"></tbody>
            </table>
          </div>
          <div class="ml-3" id="contenidoServicios" hidden>
            <h5 class="text-primary">
              <i class="fas fa-caret-right"></i>
              <span class="text-primary">Productos de los servicios</span>
            </h5>
            <div id="contenidoServiciosProductos"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="actualizarAlmacenProductos">
              <i class="far fa-save"></i>
              <span>Actualizar</span>
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="far fa-times-circle"></i>
              <span>Cerrar</span>
          </button>
        </div>
      </div>
    </div>
  </div>