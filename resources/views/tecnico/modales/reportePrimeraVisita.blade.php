<div class="modal fade" id="modalPrimeraVisita" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Reporte de Pre - Cotización</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <textarea id="sumernotePreCotizacion"></textarea>
            </div>
            <h5 class="text-primary">
                <i class="fas fa-caret-right"></i>
                Seleccionar servicios
            </h5>
            <div class="form-group">
                <div style="max-width: 500px;" class="mb-3">
                    <label for="cbServicio">Servicio:</label>
                    <select name="servicio" id="cbServicio" class="select2-simple" data-placeholder="Seleccione los servicios">
                        <option value=""></option>
                        @foreach ($servicios as $s)
                            <option value="{{$s->id}}">{{$s->servicio}}</option>                        
                        @endforeach
                    </select>
                </div>
                <form id="contenidoServicios" class="d-flex flex-wrap" style="gap: 10px;">
                    <div class="mb-2" id="txtNoServi">
                        <span>Sin servicios seleccionados</span>
                    </div>
                    <input type="submit" id="btnFrom" hidden>
                </form>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnGenerarReporte" class="btn btn-outline-primary"><i class="far fa-save"></i> Generar reporte</button>
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><i class="far fa-times-circle"></i> Cancelar</button>
        </div>
      </div>
    </div>
</div>