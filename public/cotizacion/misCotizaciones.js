function loadPage() {
    let general = new General();
    let cotizacionGeneral = new Cotizacion();
    let estadoCotizacion = [
        {
            class:"badge badge-danger",
            value: "Anulado"
        },
        {
            class:"badge badge-warning",
            value: "Generado"
        },
        {
            class:"badge badge-success",
            value: "Aprobado"
        },
        {
            class:"badge badge-info",
            value: "Pendiente OS"
        },
        {
            class:"badge badge-primary",
            value: "Con OS"
        }
    ]
    const tablaCotizacion = document.querySelector("#tablaCotizaciones");
    const tablatablaCotizacionDatatable = $(tablaCotizacion).DataTable({
        ajax: {
            url: 'lista-cotizacion',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.estado = $('#cbEstados').val();
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
                return `<span class="${estadoCotizacion[+data].class}">${estadoCotizacion[+data].value}</span>`;
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
    $('#cbEstados').on("select2:select",function(e){
        tablatablaCotizacionDatatable.draw();
    });
    tinymce.init({
        selector: '#idModalNotaCotizacion',
        language: 'es',
        plugins: 'anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        image_title: true,
        branding: false,
        height: "500px",
        automatic_uploads: true,
        images_upload_url: window.origin + '/intranet/storage/editor/img-cotizacion/save',
        file_picker_types: 'image',
        images_upload_handler : (blobInfo, progress) => new Promise(async (resolve, reject) => {
            let datos = new FormData();
            datos.append('file', blobInfo.blob(), blobInfo.filename());
            try {
                const reponse = await general.funcfetch(window.origin + '/intranet/storage/editor/img-cotizacion/save',datos,"POST");
                resolve(reponse.location);
            } catch (error) {
                reject(error);
            }
        })
    });
    let idCotizacion = null;
    const listaServiciosAlmacen = document.querySelector("#contenidoServiciosProductos");
    const keysTotales = ["descuentoTotal","importeTotal","total","igvTotal"];
    const tablaServicios = document.querySelector("#contenidoServicios");
    const tablaServicioProductos = document.querySelector("#listaServiciosProductos");
    const formCotizacion = document.querySelector("#frmCotizacion");
    const cbRepresentastes = document.querySelector("#idModalrepresentanteCliente");
    const tablaProductos = document.querySelector("#almacenProductosCotizacion #contanidoTablaProductos");
    const contenidoProductosAlmacen = document.querySelector("#almacenProductosCotizacion #contenidoProductos");
    const contenidoServiciosAlmacen = document.querySelector("#almacenProductosCotizacion #contenidoServicios");

    let cbServicios = document.querySelector("#cbServicios");
    let serviciosProductos = [];
    let calcularGarantia = false;
    const contenedorArchivoPdf = document.querySelector("#contenedorArchivoPdf");
    tablaCotizacion.addEventListener("click",async function(e){
        if (e.target.classList.contains("aprobar-cotizacion") || e.target.classList.contains("cotizacion-almacen")){
            tablaProductos.innerHTML = "";
            calcularGarantia = false;
            listaServiciosAlmacen.innerHTML = "";
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
                let contenidoProductosTangibles = 0;
                if(response.productos && response.productos.length){
                    contenidoProductosAlmacen.hidden = false;
                    contenidoProductosTangibles++;
                    response.productos.forEach((producto,key) => {
                        producto.index = key + 1;
                        tablaProductos.append(cotizacionGeneral.almacenProductos(producto));
                    });
                }
                if(response.servicios && response.servicios.length){
                    contenidoServiciosAlmacen.hidden = false;
                    response.servicios.forEach(servicio => {
                        if(servicio.productos.length){
                            contenidoProductosTangibles++;
                        }
                        const $tablaServicioDetalle = cotizacionGeneral.almacenServicios(servicio);
                        if($tablaServicioDetalle){
                            listaServiciosAlmacen.append(cotizacionGeneral.almacenServicios(servicio));
                        }
                    });
                }
                if(response.aprobar === 1){
                    calcularGarantia = true;
                }
                if(response.aprobar === 0 && contenidoProductosTangibles === 0){
                    return alertify.alert("Mensaje","Esta cotización no cuenta con productos tangibles para la asignación de almacen");
                }else if(response.aprobar === 1 && contenidoProductosTangibles === 0){
                    alertify.confirm("Mensaje","Esta cotización no cuenta con productos tangibles para la asignación de almacen.<br>¿Deseas aprobar esta cotización de todas formas?",async ()=>{
                        calcularGarantia = true;
                        const datos = new FormData();
                        datos.append("idCotizacion",e.target.dataset.cotizacion);
                        datos.append("acciones","aprobar-solo");
                        datos.append("calcularGarantia",calcularGarantia);
                        const response = await general.funcfetch("aprobar",datos,"POST");
                        if (response.session) {
                            return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                        }
                        if(response.alerta){
                            return alertify.alert("Mensaje",response.alerta);
                        }
                        alertify.success(response.success);
                    },()=>{})
                }else{
                    idCotizacion = e.target.dataset.cotizacion;
                    $('#almacenProductosCotizacion').modal("show");
                    $('#almacenProductosCotizacion select').select2({
                        theme: 'bootstrap',
                        width: '100%',
                        placeholder : "Seleccionar un almacen"
                    });
                }
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
                cotizacionGeneral.$cbTipoMoneda.value = response.cotizacion.tipoMoneda;
                cotizacionGeneral.$txtConversion.value = response.cotizacion.conversionMoneda;
                $('#editarCotizacion .select2-simple').trigger("change");
                for (const key in response.cotizacion) {
                    if (Object.hasOwnProperty.call(response.cotizacion, key)) {
                        const valor = response.cotizacion[key];
                        const dom = document.querySelector("#idModal" + key);
                        if(key == "tipoMoneda" || key == "conversionMoneda"){
                            continue;
                        }
                        if(key == "id_pre_cotizacion" && !valor){
                            dom.value = "ninguno";
                            continue;
                        }
                        if(key == "textoNota"){
                            tinymce.activeEditor.setContent(!valor ? "" : valor);
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
                        if(key == "serviciosProductos"){
                            valor.forEach((servicioProducto,k) => {
                                const {id_producto,nombreDescripcion,cantidad,precio,descuento,total,tipo} = servicioProducto;
                                tablaServicios.append(cotizacionGeneral.agregarServicio(k+1,id_producto,nombreDescripcion,cantidad,precio,descuento,total,tipo,"antiguo"));
                                if(tipo === "servicio"){
                                    tablaServicioProductos.append(cotizacionGeneral.listarDetalleProductosDeServicios(id_producto,nombreDescripcion,servicioProducto.detalleProductos,"antiguo"));
                                }
                                serviciosProductos.push(cotizacionGeneral.asignarListaServiciosProductosEditar(servicioProducto));
                                cotizacionGeneral.cbServiciosOpt(cbServicios,true,[{id:id_producto,tipo}]);
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
                            dom.textContent = key == "descuentoTotal" ? "-" + general.resetearMoneda(valor,response.cotizacion.tipoMoneda): "" + general.resetearMoneda(valor,response.cotizacion.tipoMoneda);
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                if(response.cotizacion?.cliente_pais !== general.idPais){
                    $('#idModalincluirIGV').prop('disabled',true);
                }
                if(response.cotizacion?.estado >= 4){
                    $('#editarCotizacion .bloquer-os').prop('disabled',true);
                }
                $('#editarCotizacion .select2-simple').trigger("change");
                cotizacionGeneral.ocultarMostrarIGV($('#idModalincluirIGV').val());
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
    });
    $('#almacenProductosCotizacion').on('hidden.bs.modal', function (event) {
        contenidoProductosAlmacen.hidden = true;
        contenidoServiciosAlmacen.hidden = true;
        tablaProductos.innerHTML = "";
        listaServiciosAlmacen.innerHTML = "";
        calcularGarantia = false;
    });
    $(cotizacionGeneral.$cbTipoMoneda).on("select2:select", function (e) {
        cotizacionGeneral.modificarMonedaTotal($(this).val(),serviciosProductos,tablaServicios)
    });
    document.querySelector("#actualizarAlmacenProductos").onclick = async function(e){
        const datos = new FormData();
        datos.append("acciones","aprobar-cotizacion");
        let listaServiciosProductos = cotizacionGeneral.resultadosAlmacenServicio(listaServiciosAlmacen);
        let listaProductos = cotizacionGeneral.resultadoAlmacenProducto(tablaProductos);
        datos.append("idCotizacion",idCotizacion);
        datos.append("servicios",JSON.stringify(listaServiciosProductos));
        datos.append("productos",JSON.stringify(listaProductos));
        datos.append("calcularGarantia",calcularGarantia);
        try {
            const response = await general.funcfetch("aprobar",datos,"POST");
            if (response.session) {
                return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
            }
            if(response.alerta){
                return alertify.alert("Mensaje",response.alerta);
            }
            tablatablaCotizacionDatatable.draw();
            $('#almacenProductosCotizacion').modal("hide");
            idCotizacion = null;
            return alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar el producto de los almacenes");
        }
    }
    $(cbServicios).on("select2:select", function (e) {
        cotizacionGeneral.obtenerServicios(cbServicios,$(this).val(),serviciosProductos,tablaServicios,tablaServicioProductos,e.params.data.element.dataset.tipo||"servicio");
    });
    $('#idModalincluirIGV').on("select2:select",async function(e){
        cotizacionGeneral.ocultarMostrarIGV($(this).val());
        cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
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
            const tr = e.target.parentElement.parentElement;
            const datosEnvio = {
                tipo : !tr.dataset.servicio ? 'producto' : 'servicio',
                accion : !tr.dataset.servicio ? 'eliminar-producto-servicio' : 'eliminar-servicio',
                idDetalle : tr.dataset.servicio || tr.dataset.producto
            }
            alertify.confirm("Mensaje",`¿Deseas eliminar este ${datosEnvio.tipo}?`,async () => {
                const {idDetalle,tipo} = datosEnvio;
                if(e.target.dataset.tipo == "nuevo"){
                    serviciosProductos = cotizacionGeneral.eliminarServicio({serviciosProductos,cbServicios,idDetalle,tipo,tr,tablaServicioProductos,tablaServicios});
                    return alertify.success(`El ${datosEnvio.tipo} se a eliminado de manera correcta`);
                }
                let datos = new FormData();
                datos.append("acciones",datosEnvio.accion);
                datos.append("idCotizacion",idCotizacion);
                datos.append("idDetalle",datosEnvio.idDetalle);
                try {
                    const response = await general.funcfetch("acciones",datos,"POST");
                    if (response.session) {
                        return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                    }
                    if(response.alerta){
                        return alertify.alert("Mensaje",response.alerta);
                    }
                    serviciosProductos = cotizacionGeneral.eliminarServicio({serviciosProductos,cbServicios,idDetalle,tipo,tr,tablaServicioProductos,tablaServicios});
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
    const cbClientes = document.querySelector("#idModalid_cliente");
    $(cbClientes).on("select2:select",async function(e){
        let datos = new FormData();
        datos.append("cliente",$(this).val());
        try {
            const response = await general.funcfetch("obtener/cliente",datos, "POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.cliente && Object.keys(response.cliente).length){
                nuevoCliente = false;
                cbRepresentastes.innerHTML = "";
                cbRepresentastes.append({id : null });
                response.cliente.contactos.forEach(c => {
                    cbRepresentastes.append(cotizacionGeneral.templateOpcionContacto(c));
                });
                document.querySelector("#idModaldireccionCliente").value = response.cliente.direccion;
                if(response.cliente.id_pais !== 165){
                    $('#idModalincluirIGV').val("0").trigger("change");
                    $('#idModalincluirIGV').prop("disabled",true);
                }else{
                    $('#idModalincluirIGV').prop("disabled",false);
                }
                cotizacionGeneral.ocultarMostrarIGV($('#idModalincluirIGV').val());
                cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener la informacion del cliente");
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
        datos.append("textoNota",tinymce.activeEditor.getContent());
        general.cargandoPeticion(btnActualizar, general.claseSpinner, true);
        try {
            const response = await general.funcfetch("modificar",datos,"POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            tablatablaCotizacionDatatable.draw();
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