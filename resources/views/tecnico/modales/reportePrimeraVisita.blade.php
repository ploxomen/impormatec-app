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
            <form id="contenidoServicios" enctype="multipart/form-data">
              <div class="form-group d-flex justify-content-between flex-wrap" style="gap:5px;">
                  <h5 class="text-primary mb-0">
                      <i class="fas fa-caret-right"></i>
                      Lista de secciones
                  </h5>
                  <button data-toggle="tooltip" id="btnAgregarSeccion" data-placement="top" title="Agregar una sección" class="btn btn-sm btn-light agregar-seccion" type="button">
                      <i class="fas fa-plus"></i>
                  </button>
              </div>
              <div class="form-group" id="contenidoSecciones"></div>
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
                  <input type="submit" id="btnFrom" hidden>
              </div>
              <h5 class="text-primary">
                <i class="fas fa-caret-right"></i>
                Formato de visitas
              </h5>
              <div class="d-flex flex-wrap" style="gap: 5px;">
                <input type="file" id="documentoVisitas" accept=".pdf" class="form-control" style="max-width: 500px;">
                <button class="btn btn-sm btn-danger" hidden type="button" id="btnFormatoVisitasEliminar" title="Eliminar documento">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>
            </form>
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