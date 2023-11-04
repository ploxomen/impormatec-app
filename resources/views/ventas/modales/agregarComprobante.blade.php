<div class="modal fade" id="agregarComprobante" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloComprobante">Agregar comprobante</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-row" id="formComprobante">
                <div class="form-group col-12 col-md-6 form-required">
                    <label for="idModalcomprobante">Comprobante</label>
                    <input type="text" name="comprobante" placeholder="Ej: BOLETA DE VENTA" class="form-control" id="idModalcomprobante" required>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalserie">Serie</label>
                    <input type="text" name="serie" accept="[0-9]" placeholder="Ej: 0001" class="form-control" id="idModalserie" required>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalinicio">Inicio Comprobante</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad btn-calculo-disponible" data-number="#idModalinicio" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" value="1" name="inicio" min="1" class="form-control calculo-disponible" id="idModalinicio" required>
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad btn-calculo-disponible" data-number="#idModalinicio" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalfin">Fin Comprobante</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad btn-calculo-disponible" data-number="#idModalfin" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" value="1000" name="fin" min="2" class="form-control calculo-disponible" id="idModalfin" required>
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad btn-calculo-disponible" data-number="#idModalfin" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModaldisponibles">Disponibles</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad btn-cambio-disponibilidad" data-number="#idModaldisponibles" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" value="1000" name="disponibles" min="0" class="form-control cambio-disponibilidad" id="idModaldisponibles" required>
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad btn-cambio-disponibilidad" data-number="#idModaldisponibles" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-md-6">
                    <label for="idModalutilizados">Utilizados</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad btn-cambio-utilizados" data-number="#idModalutilizados" data-accion="disminuir">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" value="0" name="utilizados" min="0" class="form-control cambio-utilizados" id="idModalutilizados" required>
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-sm btn-outline-info cambiar-cantidad btn-cambio-utilizados" data-number="#idModalutilizados" data-accion="aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="estado" class="custom-control-input change-switch" data-selected="VIGENTE" data-noselected="DESCONTINUADO" disabled checked id="idModalestado">
                        <label class="custom-control-label" for="idModalestado">VIGENTE</label>
                    </div>
                </div>
                <input type="submit" hidden id="btnFrmEnviar">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnGuardarFrm">
                <i class="fas fa-save"></i>
                <span>Guardar</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-eraser"></i>
                    <span>Cancelar</span>
            </button>
        </div>
      </div>
    </div>
  </div>