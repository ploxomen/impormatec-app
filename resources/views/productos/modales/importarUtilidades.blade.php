<div class="modal fade" id="importarUtilidades" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloProducto">Agregar Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <p class="text-info">Las filas pintadas de <b class="text-danger">color rojo</b> son aquellas que no se encuentran en el sistema, por favor verifique en su excel el nombre del producto y vuelva a intentarlo.</p>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th style="width: 120px;">PRODUCTO</th>
                            <th>PRECIO COMPRA</th>
                            <th>UTILIDAD (%)</th>
                            <th>PRECIO VENTA</th>
                        </tr>
                    </thead>
                    <tbody id="contenidoUtilidades"></tbody>
                </table>
            </div>
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnImportarUtilidadesGuardar">
                <i class="fas fa-save"></i>
                <span>Importar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                <i class="far fa-times-circle"></i>
                <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>