function loadPage(params) {
    let general = new General();
    const tablaComprobantes = document.querySelector("#tablaComprobantes");
    const dataTableComprobantes = $(tablaComprobantes).DataTable({
        ajax: {
            url: 'comprobantes/listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.fechaInicio = $('#txtFechaInicio').val();
                d.fechaFin = $('#txtFechaFin').val();
            }
        },
        columns: [
        {
            data: 'numero_comprobante'
        },
        {
            data: 'fechaPagada'
        },
        {
            data : 'tipoComprobante',
            render : function(data,type,row){
                return general.obtenerNombreComprobante(data).nombre;
            }
        },
        {
            data : 'pagado',
            render : function(data,type,row){
                return general.resetearMoneda(data,row.tipo_moneda);
            }
        },
        {
            data: 'id',
            render : function(data,type,row){
                return `
                <a class="btn btn-sm btn-outline-danger" href="comprobante/ver/pdf/${row.tipo}/${data}" target="_blank">
                    <i class="far fa-file-pdf"></i>
                    Ver comprobante
                </a>`;
            }
        },
        ]
    });
    document.querySelector("#btnAplicarFiltros").onclick = e => dataTableComprobantes.draw();
}
window.addEventListener('DOMContentLoaded',loadPage);