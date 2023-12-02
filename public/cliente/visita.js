function loadPage(params) {
    let general = new General();
    const tablaPreCotizacion = document.querySelector("#tablaPreCotizaciones");
    const tablaPreCotizacionDatatable = $(tablaPreCotizacion).DataTable({
        ajax: {
            url: 'visitas/listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.fechaInicio = $('#txtFechaInicio').val();
                d.fechaFin = $('#txtFechaFin').val();
            }
        },
        columns: [
        {
            data: 'nroPreCotizacion'
        },
        {
            data: 'id',
            render : function(data,type,row){
                return row.nombreTecnico + " " + row.aspellidosTecnico;
            }
        },
        {
            data: 'fechaHrProgramada'
        },
        {
            data : 'estado',
            render : function(data,type,row){
                switch (data) {
                    case 0:
                        return `<span class="badge badge-danger">Anulado</span>`;
                    break;
                    case 1:
                        return `<span class="badge badge-warning">Programado</span>`;
                    break;
                    case 2:
                        return `<span class="badge badge-info">Informado</span>`;
                    break;
                    default:
                        return `<span class="badge badge-success">Cotizado</span>`;
                    break
                }
            }
        },
        {
            data: 'id',
            render : function(data,type,row){
                return row.estado >= 2 ? `
                <a class="btn btn-sm btn-outline-danger" href="visita/ver/pdf/${data}" target="_blank">
                    <i class="far fa-file-pdf"></i>
                    Ver i8nforme
                </a>` : `<span class="badge badge-danger">Sin acciones</span>`
            }
        },
        ]
    });
    document.querySelector("#btnAplicarFiltros").onclick = e => tablaPreCotizacionDatatable.draw();
}
window.addEventListener('DOMContentLoaded',loadPage);