function loadPage() {
    let general = new General();
    let cotizacionGeneral = new Cotizacion();
    let estadoCotizacion = ["Por aprobar","Aprobado","Cotizado"]
    const tablaCotizacion = document.querySelector("#tablaCotizaciones");
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
                return `
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item editar-cotizacion" href="javascript:void(0)" data-cotizacion="${data}">
                            <i class="fas fa-pencil-alt text-primary mr-1"></i>
                            <span class="text-secondary">Editar</span>
                        </a>
                        <a class="dropdown-item" href="ver/pdf/${data}" target="_blank">
                            <i class="far fa-file-pdf text-danger mr-1"></i>
                            <span class="text-secondary">Cotización</span>
                        </a>
                        <a class="dropdown-item aprobar-cotizacion" href="javascript:void(0)" data-cotizacion="${data}">
                            <i class="fas fa-check text-success mr-1"></i>
                            <span class="text-secondary">Aprobar</span>
                        </a>
                        <a class="dropdown-item eliminar-cotizacion" href="javascript:void(0)">
                            <i class="fas fa-trash-alt text-danger mr-1"></i>
                            <span class="text-secondary">Eliminar</span>
                        </a>
                    </div>
                </div>`
            }
        },
        ]
    });
    let almacenCotizacion = [];
    let idCotizacion = null;
    const listaServiciosAlmacen = document.querySelector("#contenidoServiciosProductos");
    const keysTotales = ["descuentoTotal","importeTotal","total","igvTotal"];
    const tablaServicios = document.querySelector("#contenidoServicios");
    const tablaServicioProductos = document.querySelector("#listaServiciosProductos");
    const formCotizacion = document.querySelector("#frmCotizacion");
    let cbServicios = document.querySelector("#cbServicios");
    let serviciosProductos = [];
    tablaCotizacion.addEventListener("click",async function(e){
        console.log(e.target.classList);
        if (e.target.classList.contains("aprobar-cotizacion")){
            const datos = new FormData();
            datos.append("idCotizacion",e.target.dataset.cotizacion);
            datos.append("acciones","consultar-aprobacion");
            try {
                const response = await general.funcfetch("aprobar",datos,"POST");
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                if(response.alerta){
                    return alertify.alert("Mensaje",response.alerta);
                }
                listaServiciosAlmacen.innerHTML = "";
                response.servicios.forEach(servicio => {
                    listaServiciosAlmacen.append(cotizacionGeneral.almacenServicios(servicio));
                });
                idCotizacion = e.target.dataset.cotizacion;
                $('#almacenProductosCotizacion').modal("show");
            } catch (error) {
                console.error(error);
                alertify.error("error al consultar la cotizaicon");
            }

        }
        if (e.target.classList.contains("editar-cotizacion")) {
            try {
                const response = await general.funcfetch("obtener/" + e.target.dataset.cotizacion,null,"GET");
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                if(response.alerta){
                    return alertify.alert("Mensaje",response.alerta);
                }
                if(!serviciosProductos.length){
                    tablaServicios.innerHTML = "";
                    tablaServicioProductos.innerHTML = "";
                }
                serviciosProductos = [];
                for (const key in response.cotizacion) {
                    if (Object.hasOwnProperty.call(response.cotizacion, key)) {
                        const valor = response.cotizacion[key];
                        const dom = document.querySelector("#idModal" + key);
                        if(key == "servicios"){
                            valor.forEach((servicio,k) => {
                                tablaServicios.append(cotizacionGeneral.agregarServicio(k+1,servicio.id_servicio,servicio.servicio,servicio.cantidad,servicio.costo,servicio.descuento,servicio.total));
                                tablaServicioProductos.append(cotizacionGeneral.agregarServicioProductos(servicio.id_servicio,servicio.servicio,servicio.productos));
                                serviciosProductos.push(cotizacionGeneral.asignarListaServiciosProductos(servicio));
                                cotizacionGeneral.cbServiciosOpt(cbServicios,true,[+servicio.id_servicio]);
                            });
                            $('#listaServiciosProductos .cb-servicios-productos').select2({
                                theme: 'bootstrap',
                                width: '100%',
                                placeholder: "Seleccionar un producto",
                            }).on("select2:select",function(e){
                                cotizacionGeneral.obtenerProducto($(this),serviciosProductos,tablaServicios);
                            });
                            for (const cambio of formCotizacion.querySelectorAll(".cambio-detalle")) {
                                cambio.addEventListener("change", function(){
                                    cotizacionGeneral.modificarCantidad(cambio,serviciosProductos,tablaServicios);
                                });
                            }
                            continue;
                        }
                        if(keysTotales.indexOf(key) >= 0){
                            dom.textContent = key == "descuentoTotal" ? "-" + general.resetearMoneda(valor): "" + general.resetearMoneda(valor);
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                // listaServiciosAlmacen.innerHTML = "";
                // response.servicios.forEach(servicio => {
                //     listaServiciosAlmacen.append(cotizacionGeneral.almacenServicios(servicio));
                // });
                // idCotizacion = e.target.dataset.cotizacion;
                $('#editarCotizacion').modal("show");
            } catch (error) {
                console.error(error);
                alertify.error("error al consultar la cotizaicon");
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            
        }
    });
    document.querySelector("#actualizarAlmacenProductos").onclick = async function(e){
        const datos = new FormData();
        datos.append("acciones","aprobar-cotizacion");
        let listaCotizacion = cotizacionGeneral.resultadosAlmacenServicio(listaServiciosAlmacen);
        datos.append("idCotizacion",idCotizacion);
        datos.append("servicios",JSON.stringify(listaCotizacion));
        try {
            const response = await general.funcfetch("aprobar",datos,"POST");
            if (response.session) {
                return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
            }
            if(response.alerta){
                return alertify.alert("Mensaje",response.alerta);
            }
            $('#almacenProductosCotizacion').modal("hide");
            idCotizacion = null;
            return alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar el producto de los almacenes");
        }
    }
    $(cbServicios).on("select2:select", function (e) {
        cotizacionGeneral.obtenerServicios(cbServicios,$(this).val(),serviciosProductos,tablaServicios,tablaServicioProductos)
    });
}
window.addEventListener("DOMContentLoaded",loadPage);