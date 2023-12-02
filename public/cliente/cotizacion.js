function loadPage(params) {
    let general = new General();
    const tablaCotizacion = document.querySelector("#tablaCotizaciones");
    const tablatablaCotizacionDatatable = $(tablaCotizacion).DataTable({
        ajax: {
            url: 'cotizaciones/listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.fechaInicio = $('#txtFechaInicio').val();
                d.fechaFin = $('#txtFechaFin').val();
            }
        },
        columns: [
        {
            data: 'nroCotizacion'
        },
        {
            data: 'nroPreCotizacion'
        },
        {
            data: 'fechaCotizada'
        },
        {
            data: 'fechaFinCotizada'
        },
        {
            data: 'atendidoPor'
        },
        {
            data : 'importeTotal',
            render : function(data,type,row){
                return general.resetearMoneda(data,row.tipoMoneda);
            }
        },
        {
            data : 'descuentoTotal',
            render : function(data,type,row){
                return "-"+ general.resetearMoneda(data,row.tipoMoneda);
            }
        },
        {
            data : 'igvTotal',
            render : function(data,type,row){
                return general.resetearMoneda(data,row.tipoMoneda);
            }
        },
        {
            data : 'total',
            render : function(data,type,row){
                return general.resetearMoneda(data,row.tipoMoneda);
            }
        },
        {
            data : 'estado',
            render : function(data,type,row){
                switch (data) {
                    case '0':
                        return `<span class="badge badge-danger">Anulado</span>`;
                    break;
                    case '1':
                        return `<span class="badge badge-warning">Pendiente</span>`;
                    break;
                    default:
                        return `<span class="badge badge-success">Aprobado</span>`;
                    break
                }
            }
        },
        {
            data: 'id',
            render : function(data){
                return `
                <a class="btn btn-sm btn-outline-danger" href="cotizacion/ver/pdf/${data}" target="_blank">
                    <i class="far fa-file-pdf"></i>
                    Ver cotización
                </a>`
            }
        },
        ]
    });
    document.querySelector("#btnAplicarFiltros").onclick = e => tablatablaCotizacionDatatable.draw();
}
window.addEventListener('DOMContentLoaded',loadPage);