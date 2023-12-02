function loadPage() {
    const gen = new General();
    const tablaMisInformes = document.querySelector("#tablaMisInformes");
    const estadosInforme = [
        {
            class:"badge badge-warning",
            value: "Pendiente"
        },
        {
            class:"badge badge-success",
            value: "Generado"
        },
        {
            class:"badge badge-primary",
            value: "Con Certificado"
        }
    ];
    const dataTablaMisInformes = $(tablaMisInformes).DataTable({
        ajax: {
            url: 'informes/listar',
            method: 'POST',
            headers: gen.requestJson,
            data: function (d) {
                d.fechaInicio = $('#txtFechaInicio').val();
                d.fechaFin = $('#txtFechaFin').val();
            }
        },
        columns: [
        {
            data: 'nroInforme'
        },
        {
            data: 'nroOrdenServicio'
        },
        {
            data: 'fechaEmision'
        },
        {
            data: 'fechaTermino'
        },
        {
            data: 'fechaFinGarantia'
        },
        // {
        //     data: 'responsable'
        // },
        {
            data : 'estado',
            render : function(data,type,row){
                return `<span class="${estadosInforme[+data-1].class}">${estadosInforme[+data-1].value}</span>`;
            }
        },
        {
            data: 'id',
            render : function(data,type,row){
                return row.estado >= 2 ? `
                <a class="btn btn-sm btn-outline-danger" href="informe/ver/pdf/${row.id_orden_servicio}/${data}" target="_blank">
                    <i class="far fa-file-pdf"></i>
                    Ver informe
                </a>` : `<span class="badge badge-danger">Sin acciones</span>`
            }
        },
        ]
    });
    document.querySelector("#btnAplicarFiltros").onclick = e => dataTablaMisInformes.draw();
}
window.addEventListener("DOMContentLoaded",loadPage);