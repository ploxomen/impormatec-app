function loadPage(params) {
    let general = new General();
    const tablaCertificados = document.querySelector("#tablaCertificados");
    const tablaCertificadosDataTable = $(tablaCertificados).DataTable({
        ajax: {
            url: 'certificados/listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.fechaInicio = $('#txtFechaInicio').val();
                d.fechaFin = $('#txtFechaFin').val();
            }
        },
        columns: [
        {
            data: 'nroCertificado'
        },
        {
            data: 'nroInforme'
        },
        {
            data: 'fechaEmision'
        },
        {
            data: 'fechaFinGarantia'
        },
        {
            data: 'asunto'
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
                <a class="btn btn-sm btn-outline-danger" href="certificado/ver/pdf/${data}" target="_blank">
                    <i class="far fa-file-pdf"></i>
                    Ver certificado
                </a>` : `<span class="badge badge-danger">Sin acciones</span>`
            }
        },
        ]
    });
    document.querySelector("#btnAplicarFiltros").onclick = e => tablaCertificadosDataTable.draw();
}
window.addEventListener('DOMContentLoaded',loadPage);