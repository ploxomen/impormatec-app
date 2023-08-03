function loadPage() {
    let gen = new General();
    let ordenServicio = new OrdenServicio();
    let cbClientes = document.querySelector("#cbClientes");
    let tablaServicios = document.querySelector("#contenidoServicios");
    let tablaServiciosAdicionales = document.querySelector(
        "#tablaServiciosAdicionales"
    );
    let listaServicios = [];
    $(cbClientes).on("select2:select", async function (e) {
        const idClientes = $(this).val();
        listaServicios = [];
        if (idClientes == "ninguno") {
            ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales);
            tablaServicios.innerHTML = `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios</td></tr>`;
            return false;
        }
        try {
            const response = await gen.funcfetch(
                "clientes/" + idClientes,
                null,
                "GET"
            );
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => {
                    window.location.reload();
                });
            }
            listaServicios = response.servicios;
            let template = "";
            response.servicios.forEach((servicio, key) => {
                servicio.index = key + 1;
                template += ordenServicio.agregarDetallServicios(servicio).outerHTML;
            });
            tablaServicios.innerHTML = !template
                ? `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios</td></tr>`
                : template;
            ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales);
            } catch (error) {
            alertify.error("error al obtener las cotizaciones aprobadas");
            console.error(error);
        }
    });
    tablaServicios.addEventListener("click",(e)=>{
        if(e.target.dataset.cotizacionServicio){
            alertify.confirm("Alerta","¿Deseas quitar este servicio?",()=>{
                listaServicios = ordenServicio.eliminarServicio(e,listaServicios,tablaServicios);
                ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales);
            },()=>{});
        }
    })
    const btnAgregarServiciosAdicionales = document.querySelector(
        "#btnAgregarServiciosAdicionales"
    );
    btnAgregarServiciosAdicionales.onclick = e => ordenServicio.agregarServiciosAdicionales(tablaServiciosAdicionales,listaServicios);
    const btnAgregar = document.querySelector("#btnAgregarCotizacion");
    document.querySelector("#frmCotizacion").addEventListener("submit", async (e) => {
        e.preventDefault();
        const data = new FormData(e.target);
        gen.cargandoPeticion(btnAgregar, gen.claseSpinner, true);
        try {
            if($(cbClientes).val() == 'ninguno'){
                return alertify.error("debe seleccionar un cliente");
            }
            if(!listaServicios.length){
                return alertify.alert("Mensaje","Para generar una nueva orden de servicio debe tener al menos una cotización");
            }
            data.append('listaServicios',JSON.stringify(listaServicios));
            const response = await gen.funcfetch("agregar",data,"POST");
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
            }
            if (response.alerta) {
                return alertify.alert("Mensaje",response.alerta);
            }
            alertify.alert("Mensaje",response.success,() => { window.location.reload()} );
        } catch (error) {
            console.error(error);
            alertify.error(error);
        }finally{
            gen.cargandoPeticion(btnAgregar, 'fas fa-plus', false);
        }
    })
}
window.addEventListener("DOMContentLoaded", loadPage);
