function loadPage() {
    let general = new General();
    let estadoCotizacion = ["Por aprobar","Aprobado","Cotizado"]
    const tablaCotizacion = document.querySelector("#tablaCotizacion");
    const tablatablaCotizacionDatatable = $(tablaCotizacion).DataTable({
        ajax: {
            url: 'lista-cotizacion',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                // d.acciones = 'obtener';
                // d.area = $("#cbArea").val();
                // d.rol = $("#cbRol").val();
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
            data: 'nombreCliente'
        },
        {
            data: 'atendidoPor'
        },
        {
            data : 'importeTotal',
            render : function(data){
                return general.resetearMoneda(data);
            }
        },
        {
            data : 'descuentoTotal',
            render : function(data){
                return general.resetearMoneda(data);
            }
        },
        {
            data : 'igvTotal',
            render : function(data){
                return general.resetearMoneda(data);
            }
        },
        {
            data : 'total',
            render : function(data){
                return general.resetearMoneda(data);
            }
        },
        {
            data : 'estado',
            render : function(data){
                return estadoCotizacion[+data-1];
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;">
                <button class="btn btn-sm btn-outline-info p-1" data-cotizacion="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                        Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-cotizacion="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    tablaCotizacion.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            return alertify.alert("Error - Server Protocol","Server port 8000 disabled, please enable the port for data exchange")
        }
        if (e.target.classList.contains("btn-outline-success")) {
            
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);