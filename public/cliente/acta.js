function loadPage(params) {
    let general = new General();
    const tablaActas = document.querySelector("#tablaActas");
    const tablaActasDataTable = $(tablaActas).DataTable({
        ajax: {
            url: 'actas/listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.fechaInicio = $('#txtFechaInicio').val();
                d.fechaFin = $('#txtFechaFin').val();
            }
        },
        columns: [
        {
            data: 'nroActa'
        },
        {
            data: 'nroOs'
        },
        {
            data: 'fechaEntrega'
        },
        {
            data: 'responsable'
        },
        {
            data: 'dniRepresentante'
        },
        {
            data: 'representante'
        },
        {
            data : 'estado',
            render : function(data,type,row){
                switch (data) {
                    case '0':
                        return `<span class="badge badge-danger">Anulado</span>`;
                    break;
                    default:
                        return `<span class="badge badge-success">Generado</span>`;
                    break
                }
            }
        },
        {
            data: 'id',
            render : function(data,type,row){
                return row.estado >= 1 ? `
                <a class="btn btn-sm btn-outline-danger" href="acta/ver/pdf/${data}" target="_blank">
                    <i class="far fa-file-pdf"></i>
                    Ver acta
                </a>` : `<span class="badge badge-danger">Sin acciones</span>`
            }
        },
        ]
    });
    document.querySelector("#btnAplicarFiltros").onclick = e => tablaActasDataTable.draw();
}
window.addEventListener('DOMContentLoaded',loadPage);