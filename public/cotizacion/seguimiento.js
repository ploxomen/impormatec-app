function loadPage(){
    let general = new General();
    const tablaSeguimiento = document.querySelector("#tablaSeguimiento");
    const radioCotizacionAprobar = document.querySelector("#radioCotizacionesPendientes");
    const radioCotizacionFinGarantia = document.querySelector("#radioGarantia");
    const tablaDataSeguimiento = $(tablaSeguimiento).DataTable({
        ajax: {
            url: 'seguimiento/listar',
            method: 'GET',
            headers: general.requestJson,
            data: function (d) {
                d.tipo = radioCotizacionAprobar.checked ? 'pendientes' : 'finalizados';
                d.fechaFin = $("#txtFechaFin").val();
                d.fechaInicio = $("#txtFechaInicio").val();
                d.year = $("#cbYearFinGarantia").val();
                d.mes = $("#cbMesFinGarantia").val();
            }
        },
        columns: [
        {
            data: 'nroCotizacion'
        },
        {
            data: 'fechaCotizada'
        },
        {
            data: 'fechaFinCotizada'
        },
        {
            data: 'fechaFinGarantia'
        },
        {
            data: 'nombreCliente'
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
            data: 'id',
            render : function(data){
                if(radioCotizacionAprobar.checked){
                    return `
                    <div class="d-flex justify-content-center" style="gap:5px;">
                        <button class="btn btn-sm btn-outline-info p-1" data-cotizacion="${data}">
                            <small>
                                <i class="fas fa-plus"></i>
                                <span>Agregar</span>
                            </small>
                        </button>
                        <button class="btn btn-sm btn-outline-primary p-1" data-cotizacion="${data}">
                            <small>
                                <i class="fas fa-eye"></i>
                                <span>Historial</span>
                            </small>
                        </button>
                    </div>`
                }
                return `
                <button class="btn btn-sm btn-outline-success p-1" data-cotizacion="${data}">
                    <small>
                        <i class="fab fa-whatsapp"></i>
                        <span>Notificar</span>
                    </small>
                </button>
                `
            }
        },
        ]
    });
    function renderizarFiltros(e) {
        const filtroOcultar = document.querySelectorAll(`#filtrosSeguimiento .${e.target.dataset.filtrosOcultar}`);
        const filtroMostrar = document.querySelectorAll(`#filtrosSeguimiento .${e.target.dataset.filtrosMostrar}`);
        for (const filtro of filtroOcultar) {
            filtro.hidden = true;
            filtro.querySelector("input, select").removeAttribute("required");
        }
        for (const filtro of filtroMostrar) {
            filtro.hidden = false;
            filtro.querySelector("input, select").setAttribute("required","required");
        }
        tablaDataSeguimiento.draw();
    }
    radioCotizacionAprobar.onchange = renderizarFiltros;
    radioCotizacionFinGarantia.onchange = renderizarFiltros;
    let idCotizacion = null;
    const contenidoSeguientoHistorial = document.querySelector("#contenidoHistorialSeguimiento");
    const contenidoSeguientoHistorialEditar = document.querySelector("#contenidoHistorialSeguimientoEditar"); 

    tablaSeguimiento.addEventListener("click",async function(e){
        if(e.target.classList.contains("btn-outline-info")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("seguimiento/historial/" + e.target.dataset.cotizacion,null,"GET");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(!response.historialSeguimientos){
                    throw Error("No se devolvio el objeto de seguimientos");
                }
                idCotizacion = e.target.dataset.cotizacion;
                let template = "";
                response.historialSeguimientos.forEach(seguimiento => {
                    seguimiento.nombreUsuario = seguimiento.usuario.nombres;
                    template += general.contenidoHistorialSeguimiento(seguimiento).outerHTML;
                });
                contenidoSeguientoHistorial.innerHTML = !template ? '<h6 class="text-center">No se registro historial para esta cotización</h6>' : template;
                $('#egregarSeguimiento').modal("show");
            } catch (error) {
                alertify.error("error al visualizar los seguimientos");
            }finally{
                general.cargandoPeticion(e.target, 'fas fa-plus', false);
            }
        }
        if(e.target.classList.contains("btn-outline-primary")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("seguimiento/historial/" + e.target.dataset.cotizacion,null,"GET");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(!response.historialSeguimientos){
                    throw Error("No se devolvio el objeto de seguimientos");
                }
                idCotizacion = e.target.dataset.cotizacion;
                let template = "";
                response.historialSeguimientos.forEach(seguimiento => {
                    seguimiento.nombreUsuario = seguimiento.usuario.nombres;
                    template += general.contenidoHistorialSeguimientoEditar(seguimiento).outerHTML;
                });
                contenidoSeguientoHistorialEditar.innerHTML = !template ? '<h6 class="text-center">No se registro historial para esta cotización</h6>' : template;
                $('#editarSeguimiento').modal("show");
            } catch (error) {
                alertify.error("error al visualizar los seguimientos");
            }finally{
                general.cargandoPeticion(e.target, 'fas fa-eye', false);
            }
        }
        if(e.target.classList.contains("btn-outline-success")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("seguimiento/notificar/" + e.target.dataset.cotizacion,null,"GET");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(!response.celular){
                    return alertify.alert("Mensaje","Por favor establesca un numero de celular válido al cliente para proceder a la notificación por whatsapp");
                }
                let templateSms = `Estimado cliente *${response.cliente}* le informamos que la cotización *N° - ${response.nroCotizacion}* vence el día *${response.fechaFinGarantia}*\nDescripción de los servicios\n`;
                response.servicios.forEach((servicio,key) => {
                    templateSms += `${key + 1}. ${servicio.servicio}\n`
                });
                templateSms += `Atentamente ${response.usuario} - IMPORMATEC`;
                general.enviarNotificacionWhatsApp(response.celular,templateSms);
            } catch (error) {
                alertify.error("error al visualizar los seguimientos");
            }finally{
                general.cargandoPeticion(e.target, 'fas fa-eye', false);
            }
        }
    });
    const btnFrmAgregarSeguimiento = document.querySelector("#btnGuardarFrm");
    const btnFrmEditarSeguimiento = document.querySelector("#btnEditarFrm");

    const frmAgregarSeguimiento = document.querySelector("#frmAgregarSeguimiento");
    const frmEditarSeguimiento = document.querySelector("#frmEditarSeguimiento");

    btnFrmAgregarSeguimiento.onclick = e => document.querySelector("#btnAgregarSeguimiento").click();
    btnFrmEditarSeguimiento.onclick = e => document.querySelector("#btnEditarSeguimiento").click();

    frmAgregarSeguimiento.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("id_cotizacion",idCotizacion);
        try {
            general.cargandoPeticion(btnFrmAgregarSeguimiento, general.claseSpinner, true);
            const response = await general.funcfetch("seguimiento/agregar",datos,"POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.success){
                alertify.success(response.success);
                frmAgregarSeguimiento.reset();
                $('#egregarSeguimiento').modal("hide");
            }
        } catch (error) {
            alertify.error("error al agregar un nuevo seguimiento");
        }finally{
            general.cargandoPeticion(btnFrmAgregarSeguimiento, 'fas fa-save', false);
        }
    });
    document.querySelector("#btnAplicarFiltros").onclick = e => tablaDataSeguimiento.draw();
    frmEditarSeguimiento.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            general.cargandoPeticion(btnFrmAgregarSeguimiento, general.claseSpinner, true);
            const response = await general.funcfetch("seguimiento/editar/" + idCotizacion ,datos,"POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.success){
                alertify.success(response.success);
                frmEditarSeguimiento.reset();
                $('#editarSeguimiento').modal("hide");
            }
        } catch (error) {
            alertify.error("error al agregar un nuevo seguimiento");
        }finally{
            general.cargandoPeticion(btnFrmAgregarSeguimiento, 'fas fa-save', false);
        }
    });
    $('#egregarSeguimiento').on("hidden.bs.modal",function(e){
        idCotizacion = null;
        contenidoSeguientoHistorial.innerHTML = "";
        frmAgregarSeguimiento.reset();
    });
    contenidoSeguientoHistorialEditar.onclick = e => {
        if(e.target.classList.contains("btn-light")){
            alertify.confirm("Alerta","¿Desea eliminar este seguimiento de forma permanente?",async () => {
                try {
                    const response = await general.funcfetch("seguimiento/eliminar/" + e.target.dataset.seguimiento + "/" + idCotizacion ,null,"DELETE");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.success){
                        alertify.success(response.success);
                        e.target.parentElement.parentElement.parentElement.remove();
                        if(!contenidoSeguientoHistorialEditar.children.length){
                            contenidoSeguientoHistorialEditar.innerHTML = '<h6 class="text-center">No se registro historial para esta cotización</h6>';
                        }
                    }
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar el seguimiento");
                }
            },()=>{})
        }
    }
    $('#editarSeguimiento').on("hidden.bs.modal",function(e){
        idCotizacion = null;
        contenidoSeguientoHistorialEditar.innerHTML = "";
        frmEditarSeguimiento.reset();
    });


}
window.addEventListener("DOMContentLoaded",loadPage);