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
            url: 'obtener',
            method: 'GET',
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
        {
            data: 'nombreCliente'
        },
        {
            data: 'responsable'
        },
        {
            data : 'estado',
            render : function(data,type,row){
                return `<span class="${estadosInforme[+data-1].class}">${estadosInforme[+data-1].value}</span>`;
            }
        },
        {
            data: 'id',
            render : function(data,type,row){
                let opcionesInforme = "";
                if(row.estado > 1){
                    opcionesInforme = `
                    <a href="completado/${row.id_orden_servicio}#formularioInforme${data}" target="_blank" class="dropdown-item">
                        <i class="fas fa-pencil-alt text-info"></i>
                        <span>Editar Informe</span>
                    </a>
                    <a href="certificado/${data}" class="dropdown-item">
                        <i class="fas fa-share-square"></i>
                        <span>Realizar Certificado</span>
                    </a>
                    <a href="reporte/previa/${row.id_orden_servicio}/${data}" target="_blank" class="dropdown-item">
                        <i class="fas fa-file-pdf text-danger"></i> 
                        <span>Ver Informe PDF</span>
                    </a>
                    `
                }else{
                    opcionesInforme = `
                    <a href="generar/nuevo?cliente=${row.idCliente}&ordenServicio=${row.id_orden_servicio}#formularioInforme${data}" target="_blank" class="dropdown-item">
                        <i class="fas fa-pencil-alt text-info"></i>
                        <span>Generar Informe</span>
                    </a>
                    `
                }
                if(row.idCertificado){
                    opcionesInforme += `
                    <a href="certificado/reporte/${row.idCertificado}" target="_blank" class="dropdown-item">
                        <i class="fas fa-file-pdf text-danger"></i> 
                        <span>Ver Certificado PDF</span>
                    </a>
                    `
                }
                return `
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu">
                        ${opcionesInforme}
                    </div>
                </div>`
            }
        },
        ]
    });
    document.querySelector("#btnAplicarFiltros").onclick = e => dataTablaMisInformes.draw();
}
window.addEventListener("DOMContentLoaded",loadPage);