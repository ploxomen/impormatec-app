function loadPage() {
    let general = new General();
    let cotizacionGeneral = new Cotizacion();
    let estadoCotizacion = ["Por aprobar","Aprobado","Pendiente OS","Con OS"]
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
                return general.resetearMoneda(data,'PEN');
            }
        },
        {
            data : 'descuentoTotal',
            render : function(data){
                return general.resetearMoneda(data,'PEN');
            }
        },
        {
            data : 'igvTotal',
            render : function(data){
                return general.resetearMoneda(data,'PEN');
            }
        },
        {
            data : 'total',
            render : function(data){
                return general.resetearMoneda(data,'PEN');
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
                        <a class="dropdown-item cotizacion-almacen" href="javascript:void(0)" data-cotizacion="${data}">
                            <i class="fas fa-store text-info"></i>
                            <span class="text-secondary">Almacenes</span>
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
    let idCotizacion = null;
    const listaServiciosAlmacen = document.querySelector("#contenidoServiciosProductos");
    const keysTotales = ["descuentoTotal","importeTotal","total","igvTotal"];
    const tablaServicios = document.querySelector("#contenidoServicios");
    const tablaServicioProductos = document.querySelector("#listaServiciosProductos");
    const formCotizacion = document.querySelector("#frmCotizacion");
    const cbRepresentastes = document.querySelector("#idModalrepresentanteCliente");
    let cbServicios = document.querySelector("#cbServicios");
    let serviciosProductos = [];
    const contenedorArchivoPdf = document.querySelector("#contenedorArchivoPdf");
    tablaCotizacion.addEventListener("click",async function(e){
        if (e.target.classList.contains("aprobar-cotizacion") || e.target.classList.contains("cotizacion-almacen")){
            const datos = new FormData();
            datos.append("idCotizacion",e.target.dataset.cotizacion);
            datos.append("acciones",e.target.classList.contains("aprobar-cotizacion") ? "consultar-aprobacion" : "consultar-almacenes" );
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
                $('#contenidoServiciosProductos select').select2({
                    theme: 'bootstrap',
                    width: '100%',
                    placeholder : "Seleccionar un almacen"
                });
            } catch (error) {
                console.error(error);
                alertify.error("error al consultar la cotizaicon");
            }
        }
        if (e.target.classList.contains("editar-cotizacion")) {
            try {
                tablaServicios.innerHTML = "";
                tablaServicioProductos.innerHTML = "";
                const response = await general.funcfetch("obtener/" + e.target.dataset.cotizacion,null,"GET");
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                if(response.alerta){
                    return alertify.alert("Mensaje",response.alerta);
                }
                idCotizacion = e.target.dataset.cotizacion;
                for (const key in response.cotizacion) {
                    if (Object.hasOwnProperty.call(response.cotizacion, key)) {
                        const valor = response.cotizacion[key];
                        const dom = document.querySelector("#idModal" + key);
                        if(key == "id_pre_cotizacion" && !valor){
                            dom.value = "ninguno";
                            continue;
                        }
                        if(key == "reporteDetallado" || key == "reportePreCotizacion"){
                            dom.checked = valor ? true : false;
                            if(key == "reportePreCotizacion" && response.cotizacion.id_pre_cotizacion) dom.disabled = false;
                            continue;
                        }
                        if(key == "documentosPdf"){
                            valor.forEach(pdf => {
                                cotizacionGeneral.renderPdfCargados({valorDocumento:null,contenedorArchivoPdf,nombreDocumento : pdf.nombre_archivo,idDocumento : pdf.id});
                            });
                            continue;
                        }
                        if(key == "contactosClientes"){
                            valor.forEach(contacto => {
                                cbRepresentastes.append(cotizacionGeneral.templateOpcionContacto(contacto));
                            });
                            continue;
                        }
                        if(key == "servicios"){
                            valor.forEach((servicio,k) => {
                                tablaServicios.append(cotizacionGeneral.agregarServicio(k+1,servicio.id_servicio,servicio.servicio,servicio.cantidad,servicio.costo,servicio.descuento,servicio.total,"antiguo"));
                                tablaServicioProductos.append(cotizacionGeneral.agregarServicioProductos(servicio.id_servicio,servicio.servicio,servicio.productos,"antiguo"));
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
                            dom.textContent = key == "descuentoTotal" ? "-" + general.resetearMoneda(valor,'PEN'): "" + general.resetearMoneda(valor,'PEN');
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#editarCotizacion .select2-simple').trigger("change");
                $('#editarCotizacion').modal("show");
            } catch (error) {
                console.error(error);
                alertify.error("error al consultar la cotizaicon");
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            
        }
    });
    const checkIncluirCotizacion = document.querySelector("#idModalreportePreCotizacion"); 
    $('#editarCotizacion').on('hidden.bs.modal', function (event) {
        serviciosProductos = [];
        cotizacionGeneral.limpiarCotizacion(serviciosProductos,cbRepresentastes,tablaServicios,tablaServicioProductos,cbServicios,checkIncluirCotizacion)
        formCotizacion.reset();
        $('#editarCotizacion .select2-simple').val("").trigger("change");
        contenedorArchivoPdf.innerHTML = "";
    })
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
    tablaServicioProductos.addEventListener("click",function (e) {  
        if (e.target.classList.contains("btn-danger")){
            const tr = e.target.parentElement.parentElement;
            const servicio = tr.dataset.servicio;
            const indexServicio = serviciosProductos.findIndex(s => s.idServicio == servicio);
            if(indexServicio < 0){
                return alertify.error("servicio no encontrado");
            }
            if(serviciosProductos[indexServicio].productosLista.length === 1){
                return alertify.alert("Mensaje","El servicio relacionado a este producto debe de tener al menos un producto, si requiere eliminar todos los productos, debe de eliminar el servicio completo de la cotización")
            }
            alertify.confirm("Mensaje","¿Deseas eliminar este producto?",async () => {
                const producto = tr.dataset.producto;
                if(e.target.dataset.tipo == "nuevo"){
                    serviciosProductos[indexServicio].productosLista = cotizacionGeneral.eliminarProducto({serviciosProductos,producto,tr,indexServicio,cbProducto : e.target.dataset.cbproducto});
                    return alertify.success("El prodcto se a eliminado de manera correcta");
                }
                let datos = new FormData();
                datos.append("acciones","eliminar-producto");
                datos.append("idCotizacion",idCotizacion);
                datos.append("idServicio",servicio);
                datos.append("idProducto",producto);
                try {
                    const response = await general.funcfetch("acciones",datos,"POST");
                    if (response.session) {
                        return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                    }
                    if(response.alerta){
                        return alertify.alert("Mensaje",response.alerta);
                    }
                    serviciosProductos[indexServicio].productosLista = cotizacionGeneral.eliminarProducto({serviciosProductos,tr,producto,indexServicio,cbProducto : e.target.dataset.cbproducto});
                    alertify.success(response.success);
                    cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar el servicio de la cotizacion");
                }
            },()=>{})
        }
    })
    const btnNuevaCotizacion = document.querySelector("#btnOtrosDocumentos");
    const fileOtrosDocumentos = document.querySelector("#fileOtrosDocumentos");
    btnNuevaCotizacion.onclick = e => fileOtrosDocumentos.click();
    fileOtrosDocumentos.addEventListener("change",function(e){
        const files = e.target.files;
        if(!files.length){
            return false
        }
        for (let i = 0; i < files.length; i++) {
            cotizacionGeneral.renderPdfCargados({valorDocumento : files[i],contenedorArchivoPdf, nombreDocumento : files[i].name,idDocumento : null});
        }
    });
    tablaServicios.addEventListener("click",function (e) {  
        if (e.target.classList.contains("btn-danger")){
            if(serviciosProductos.length === 1){
                return alertify.error("la cotización debe de contener al menos un servicio");
            }
            alertify.confirm("Mensaje","¿Deseas eliminar este servicio?",async () => {
                const tr = e.target.parentElement.parentElement;
                const servicio = tr.dataset.servicio;
                if(e.target.dataset.tipo == "nuevo"){
                    serviciosProductos = cotizacionGeneral.eliminarServicio({serviciosProductos,cbServicios,servicio,tr,tablaServicioProductos,tablaServicios});
                    return alertify.success("El servicio se a eliminado de manera correcta");
                }
                let datos = new FormData();
                datos.append("acciones","eliminar-servicio");
                datos.append("idCotizacion",idCotizacion);
                datos.append("idServicio",servicio);
                try {
                    const response = await general.funcfetch("acciones",datos,"POST");
                    if (response.session) {
                        return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                    }
                    if(response.alerta){
                        return alertify.alert("Mensaje",response.alerta);
                    }
                    serviciosProductos = cotizacionGeneral.eliminarServicio({serviciosProductos,cbServicios,servicio,tr,tablaServicioProductos,tablaServicios});
                    alertify.success(response.success);
                    cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar el servicio de la cotizacion");
                }
            },()=>{})
        }
    });
    contenedorArchivoPdf.addEventListener("click",function(e){
        if(e.target.classList.contains("btn-sm")){
            if(e.target.dataset.documento){
                alertify.confirm("Mensaje","¿Deseas eliminar este documento?",async ()=>{
                    const documento = e.target.dataset.documento;
                    let datos = new FormData();
                    datos.append("acciones","eliminar-pdf");
                    datos.append("idCotizacion",idCotizacion);
                    datos.append("idPdf",documento);
                    try {
                        const response = await general.funcfetch("acciones",datos,"POST");
                        if (response.session) {
                            return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                        }
                        if(response.alerta){
                            return alertify.alert("Mensaje",response.alerta);
                        }
                        e.target.parentElement.remove();
                        return alertify.success(response.success);
                    } catch (error) {
                        console.error(error);
                        alertify.error("error al eliminar el documento");
                    }
                },()=>{})
            }else{
                e.target.parentElement.remove();
                return alertify.success("El documento se a eliminado de manera correcta");
            }
        }
    });
    const btnActualizar = document.querySelector("#actualizarCotizacion");
    btnActualizar.onclick = e => document.querySelector("#btnActualizar").click();
    formCotizacion.addEventListener("submit",async function(e){
        e.preventDefault();
        if(!serviciosProductos.length){
            return alertify.error("la cotización debe contener al menos un servicio");
        }
        let datos = new FormData(this);
        datos.append("idCotizacion",idCotizacion);
        datos.append("servicios",JSON.stringify(serviciosProductos));
        general.cargandoPeticion(btnActualizar, general.claseSpinner, true);
        try {
            const response = await general.funcfetch("modificar",datos,"POST");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            $('#editarCotizacion').modal("hide");
            return alertify.alert("Mensaje",response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar la cotización");
        }finally{
            general.cargandoPeticion(btnActualizar, 'far fa-save', false);
        }
    })
 }
window.addEventListener("DOMContentLoaded",loadPage);