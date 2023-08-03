function loadPage() {
    const gen = new General();
    let ordenServicio = new OrdenServicio();
    const tablaOs = document.querySelector("#tablaCotizaciones");
    const estadoOs = ["Generado","Facturado"];
    const tablaDataOs = $(tablaOs).DataTable({
        ajax: {
            url: 'obtener',
            method: 'POST',
            headers: gen.requestJson,
            data: function (d) {
                // d.acciones = 'obtener';
                // d.area = $("#cbArea").val();
                // d.rol = $("#cbRol").val();
            }
        },
        columns: [
        {
            data: 'nroOs'
        },
        {
            data: 'fechaOs'
        },
        {
            data: 'nombreCliente'
        },
        {
            data : 'importe',
            render : function(data){
                return gen.resetearMoneda(data);
            }
        },
        {
            data : 'descuento',
            render : function(data){
                return gen.resetearMoneda(data);
            }
        },
        {
            data : 'igv',
            render : function(data){
                return gen.resetearMoneda(data);
            }
        },
        {
            data : 'adicional',
            render : function(data){
                return gen.resetearMoneda(data);
            }
        },
        {
            data : 'total',
            render : function(data){
                return gen.resetearMoneda(data);
            }
        },
        {
            data : 'estado',
            render : function(data){
                return estadoOs[+data-1];
            }
        },
        {
            data: 'id',
            render : function(data){
                return `
                <div class="d-flex justify-content-center" style="gap:5px;">
                    <button class="btn btn-sm btn-outline-info p-1" data-orden-servicio="${data}">
                        <small>
                        <i class="fas fa-pencil-alt"></i>
                        Editar
                        </small>
                    </button>
                    <button class="btn btn-sm btn-outline-danger p-1" data-orden-servicio="${data}">
                        <small>    
                        <i class="fas fa-trash-alt"></i>
                        Eliminar
                        </small>
                    </button>
                </div>`
            }
        },
        ]
    });
    let tablaServicios = document.querySelector("#contenidoServicios");
    let tablaServiciosAdicionales = document.querySelector("#tablaServiciosAdicionales");
    let listaServicios = [];
    let idOrdenServicio = null;
    const cbCotizaciones = document.querySelector("#idCotizacionServicio");
    tablaOs.addEventListener("click",async (e)=>{
        if(e.target.classList.contains("btn-outline-info")){
            try {
                const response = await gen.funcfetch("mostrar/" + e.target.dataset.ordenServicio,null,"GET");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => {
                        window.location.reload();
                    });
                }
                if(!response.ordenServicio){
                    return alertify.alert("Alerta","No se encontró la información para esta orden de servicio");
                }
                let template = "";
                response.ordenServicio.cotizaciones.forEach((servicio, key) => {
                    servicio.index = key + 1;
                    template += ordenServicio.agregarDetallServicios(servicio).outerHTML;
                });
                idOrdenServicio = response.ordenServicio.id;
                tablaServiciosAdicionales.dataset.tipo = "lleno";
                listaServicios = response.ordenServicio.cotizaciones;
                tablaServicios.innerHTML = !template ? `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios</td></tr>` : template;
                tablaServiciosAdicionales.innerHTML = "";
                response.ordenServicio.adicionales.forEach((adicional, key) => {
                    adicional.index = key + 1;
                    tablaServiciosAdicionales.append(ordenServicio.generarServiciosAdicionales(adicional));
                });
                if(!response.ordenServicio.adicionales.length){
                    tablaServiciosAdicionales.innerHTML = `
                    <tr><td colspan="100%" class="text-center">No se agregaron servicios adicionales</td></tr>
                    `;
                    tablaServiciosAdicionales.dataset.tipo = "vacio";
                }
                cbCotizaciones.append(new Option("",""));
                response.ordenServicio.listaServicios.forEach(cotizacion => {
                    const opcion = new Option("N° " + cotizacion.nroCotizacion,cotizacion.idCotizacion);
                    cbCotizaciones.append(opcion);
                });
                for (const input of tablaServiciosAdicionales.querySelectorAll(
                    ".punitari-servicios, .cantidad-servicios"
                )) {
                    input.addEventListener("change", e => {ordenServicio.calcularMonto({e,listaServicios,tablaServiciosAdicionales})});
                }
                document.querySelector("#idModalcliente").value = response.ordenServicio.nombreCliente;
                document.querySelector("#idModalfechaEmitida").value = response.ordenServicio.fecha;
                ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales);
                $('#editarOrdenServicio').modal("show");
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener los datos de la orden de servicio");
            }
        }
    });
    $(cbCotizaciones).on('select2:select',async (e)=>{
        try {
            let datos = new FormData();
            datos.append("idCotizacion",$(cbCotizaciones).val());
            datos.append("idOrdenServicio",idOrdenServicio);
            datos.append("acciones","agregar-cotizacion");
            const response = await gen.funcfetch("acciones",datos,"POST");
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => {
                    window.location.reload();
                });
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            alertify.success(response.success);
            response.listaServicios.forEach(servicio => {
                servicio.index = listaServicios.length + 1;
                tablaServicios.append(ordenServicio.agregarDetallServicios(servicio));
                listaServicios.push(servicio);
            });
            ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales);
            const indiceBorrar = Array.from(cbCotizaciones.options).findIndex(option => option.value == $(cbCotizaciones).val());
            if(indiceBorrar >= 0){
                cbCotizaciones.remove(indiceBorrar);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar los servicios de la cotizacion")
        }
    })
    const btnAgregarServiciosAdicionales = document.querySelector(
        "#btnAgregarServiciosAdicionales"
    );
    btnAgregarServiciosAdicionales.onclick = e => ordenServicio.agregarServiciosAdicionales(tablaServiciosAdicionales,listaServicios);
    const frmOs = document.querySelector("#frmOrdenServicio");
    $('#editarOrdenServicio').on('hidden.bs.modal', function (event) {
        frmOs.reset();
        cbCotizaciones.innerHTML = "";
        tablaServiciosAdicionales.innerHTML = "";
        listaServicios = [];
        idOrdenServicio = null;
        ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales);
    });
    tablaServiciosAdicionales.addEventListener("click",(e) => {
        if(e.target.classList.contains("btn-danger")){
            alertify.confirm("Mensaje","¿Deseas eliminar este servicio adicional?",async ()=>{
                if(e.target.dataset.adicional){
                    const adicionalId = e.target.dataset.adicional;
                    let datos = new FormData();
                    datos.append("adicionalId",adicionalId);
                    datos.append("ordenServicioId",idOrdenServicio);
                    datos.append("acciones","eliminar-adicional");
                    const response = await gen.funcfetch("acciones",datos,"POST");
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => {
                            window.location.reload();
                        });
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                }
                e.target.parentElement.parentElement.remove();
                if(!tablaServiciosAdicionales.children.length){
                    tablaServiciosAdicionales.innerHTML = `
                    <tr><td colspan="100%" class="text-center">No se agregaron servicios adicionales</td></tr>
                    `;
                    tablaServiciosAdicionales.dataset.tipo = "vacio";
                }
                ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales);
                alertify.success("Servicio adicional eliminado correctamente");
            },()=>{})
        }
    });
    tablaServicios.addEventListener("click",(e) => {
        if(e.target.classList.contains("btn-danger")){
            alertify.confirm("Mensaje","¿Deseas eliminar este servicio?",async ()=>{
                if(e.target.dataset.cotizacionServicio){
                    const cotizacionServicio = e.target.dataset.cotizacionServicio;
                    let datos = new FormData();
                    datos.append("cotizacionServicioId",cotizacionServicio);
                    datos.append("ordenServicioId",idOrdenServicio);
                    datos.append("acciones","eliminar-cotizacion");
                    const response = await gen.funcfetch("acciones",datos,"POST");
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => {
                            window.location.reload();
                        });
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                }
                listaServicios = ordenServicio.eliminarServicio(e,listaServicios,tablaServicios);
                ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales);
                alertify.success("Servicio eliminado correctamente");
            },()=>{})
        }
    });
    document.querySelector("#btnGuardarFrm").onclick = e => document.querySelector("#btnEnviar").click();
    frmOs.addEventListener("submit",async (e)=>{
        e.preventDefault();
        let datos = new FormData(e.target);
        datos.append("ordenServicioId",idOrdenServicio);
        datos.append("acciones","actualizar-orden");
        const response = await gen.funcfetch("acciones",datos,"POST");
        if (response.session) {
            return alertify.alert([...gen.alertaSesion], () => {
                window.location.reload();
            });
        }
        if(response.alerta){
            return alertify.alert("Alerta",response.alerta);
        }
        $('#editarOrdenServicio').modal("hide");
        alertify.success(response.success);
        tablaDataOs.draw();
    })

}
window.addEventListener("DOMContentLoaded",loadPage);