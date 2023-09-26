<div class="modal fade" id="modalPrimeraVisita" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloReporteCotizcion">Reporte de Pre - Cotización</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <textarea id="sumernotePreCotizacion"></textarea>
            </div>
            <div class="d-flex align-items-center mb-3" style="gap: 10px;">
              <h5 class="text-primary">
                <i class="fas fa-caret-right"></i>
                Seleccionar imagenes
              </h5>
              <button class="btn btn-sm btn-primary" type="button" id="btnImagen" title="Añadir imagenes">
                <i class="fas fa-images"></i>
              </button>
              <input type="file" hidden id="imgCopia" multiple accept="image/*">
            </div>
            <form id="contenidoServicios">
              <div class="mb-4 row" id="renderImg" style="overflow-y: auto;">
                <div class="form-grop col-12 text-center" id="txtSinImagenes">
                  <span>No se subieron imagenes</span>
                </div>
              </div>
              <h5 class="text-primary">
                  <i class="fas fa-caret-right"></i>
                  Seleccionar servicios
              </h5>
              <div class="form-group">
                  <div style="max-width: 500px;" class="mb-3">
                      <label for="cbServicio">Servicio:</label>
                      <select id="cbServicio" class="select2-simple" data-placeholder="Seleccione los servicios">
                          <option value=""></option>
                          @foreach ($servicios as $s)
                              <option value="{{$s->id}}">{{$s->servicio}}</option>                        
                          @endforeach
                      </select>
                  </div>
                  <div class="d-flex flex-wrap mb-2" style="gap: 10px;" id="contenidoListaServicios"></div>
                  <div id="txtNoServi">
                    <span>Sin servicios seleccionados</span>
                  </div>
                  <input type="file" hidden name="imagenes[]" multiple accept="image/*" id="imgsOriginal">
                  <input type="submit" id="btnFrom" hidden>
              </div>
            </form>
            <div class="d-flex align-items-center mb-3" style="gap: 10px;">
              <h5 class="text-primary">
                <i class="fas fa-caret-right"></i>
                Subir formato de visitas
              </h5>
              <button class="btn btn-sm btn-danger" type="button" id="btnFormatoVisitas" title="Subir formato de visitas">
                <i class="fas fa-file-pdf"></i>
              </button>
              <input type="file" hidden id="documentoVisitas" accept=".pdf">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnGenerarReporte" class="btn btn-outline-primary">
            <i class="far fa-save"></i> 
            <span>Generar reporte</span>
          </button>
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><i class="far fa-times-circle"></i> Cerrar</button>
        </div>
      </div>
    </div>
</div>