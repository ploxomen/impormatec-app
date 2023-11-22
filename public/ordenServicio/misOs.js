function loadPage() {
    const gen = new General();
    let ordenServicio = new OrdenServicio();
    const tablaOs = document.querySelector("#tablaCotizaciones");
    const estadoOs = [
        {
            class:"badge badge-warning",
            value: "Generado"
        },
        {
            class:"badge badge-success",
            value: "Informado"
        },
        {
            class:"badge badge-info",
            value: "Facturado"
        },
        {
            class:"badge badge-primary",
            value: "Con OS"
        }
    ];
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
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipoMoneda);
            }
        },
        {
            data : 'descuento',
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipoMoneda);
            }
        },
        {
            data : 'igv',
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipoMoneda);
            }
        },
        {
            data : 'adicional',
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipoMoneda);
            }
        },
        {
            data : 'total',
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipoMoneda);
            }
        },
        {
            data : 'estado',
            render : function(data,type,row){
                return `<span class="${estadoOs[+data-1].class}">${estadoOs[+data-1].value}</span>`;
            }
        },
        {
            data: 'id',
            render : function(data,type,row){
                let opcionesInforme = "";
                if(row.estado > 1){
                    opcionesInforme = `
                    <a href="../informe/completado/${data}" target="_blank" class="dropdown-item">
                        <i class="fas fa-pencil-alt text-info"></i>
                        <span>Editar Informe</span>
                    </a>
                    <a href="javascript:void(0)" class="dropdown-item generar-acta" data-orden-servicio="${data}">
                        <i class="fas fa-share-square text-primary"></i>
                        <span>Acta de entrega</span>
                    </a>
                    <a href="../informe/reporte/previa/${data}" target="_blank" class="dropdown-item">
                        <i class="fas fa-file-pdf text-danger"></i> 
                        <span>Ver Informe PDF</span>
                    </a>
                    `
                }
                return `
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="javascript:void(0)" class="dropdown-item editar-os" data-orden-servicio="${data}">
                            <i class="fas fa-pencil-alt text-info"></i>
                            <span>Editar OS</span>
                        </a>
                        ${opcionesInforme}
                        <a href="javascript:void(0)" class="dropdown-item pago-cuotas" data-orden-servicio="${data}">
                            <i class="fas fa-hands-helping text-primary"></i>
                            <span>Pago a credito</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" data-orden-servicio="${data}" data-toggle="modal" data-target="#generarFactura">
                            <i class="fas fa-money-bill-alt text-success"></i>
                            <span>Pago al contado</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" data-orden-servicio="${data}" data-toggle="modal" data-target="#generarGuiaRemision">
                            <i class="fas fa-box-open text-warning"></i>
                            <span>Guía de remisión</span>
                        </a>
                        <a href="reporte/${data}" target="_blank" class="dropdown-item">
                            <i class="fas fa-file-pdf text-danger"></i>                        
                            <span>Reporte OS PDF</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item eliminar-os" data-orden-servicio="${data}">
                            <i class="fas fa-trash-alt text-danger"></i>
                            <span>Eliminar OS</span>
                        </a>
                    </div>
                </div>`
            }
        },
        ]
    });
    tinymce.init({
        selector: '#observacionesOrdenServicio',
        language: 'es',
        plugins: 'anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        image_title: true,
        branding: false,
        height: "400px",
        automatic_uploads: true,
        images_upload_url: window.origin + '/intranet/storage/editor/img-os/save',
        file_picker_types: 'image',
        images_upload_handler : (blobInfo, progress) => new Promise(async (resolve, reject) => {
            let datos = new FormData();
            datos.append('file', blobInfo.blob(), blobInfo.filename());
            try {
                const reponse = await gen.funcfetch(window.origin + '/intranet/storage/editor/img-os/save',datos,"POST");
                resolve(reponse.location);
            } catch (error) {
                reject(error);
            }
        })
    });
    const canvaFirma = document.querySelector("#idModalActafirma");
    const signaturePad = new SignaturePad(canvaFirma,{
        minWidth:1,
        maxWidth:1,
        backgroundColor: 'rgb(255, 255, 255)'
    });
    document.querySelector("#btnLimpiarFirma").onclick = e => signaturePad.clear();
    let tablaServicios = document.querySelector("#contenidoServicios");
    let tablaServiciosAdicionales = document.querySelector("#tablaServiciosAdicionales");
    let $tipoMoneda = document.querySelector("#editarOrdenServicio #idModaltipoMoneda");
    let listaServicios = [];
    let idOrdenServicio = null;
    const cbCotizaciones = document.querySelector("#idCotizacionServicio");
    document.querySelector("#btnGuardarCambiosActa").onclick = e => document.querySelector("#enviarActa").click();
    const formularioActa = document.querySelector("#frmActa");
    let idActaEntrega = null;
    formularioActa.addEventListener("submit",async (e)=>{
        e.preventDefault();
        try {
            let datos = new FormData(e.target);
            if(signaturePad.isEmpty()){
                return alertify.error("por favor establesca una firma");
            }
            datos.append("ordenServicio",idOrdenServicio);
            if(idActaEntrega){
                datos.append("idEntregaActa",idActaEntrega);
            }
            datos.append("imagenFirmaRepresentante",signaturePad.toDataURL("image/jpeg"));
            const response = await gen.funcfetch("acta-entrega/guardar",datos,"POST");
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => {
                    window.location.reload();
                });
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            return alertify.success(response.success);
        }catch(error){
            console.error(error);
            alertify.error("error al guardar los datos de la acta de entrega");
        }
    });
    function renderizarCuotaPagos({numeroCuota,fechaVencimiento,fechaPago,tipoMoneda,montoPagado,montoPagar,descripcion,estado,id}) {
        let $botonVerComprobante = "";
        let $estadoPago = `<span class="badge badge-danger">Pendiente</span>`;
        if(estado === 2){
            $estadoPago = `<span class="badge badge-success">Pagado</span>`;
            $botonVerComprobante = `
            <a class="btn btn-sm btn-danger" target="_blank" href="pago/comprobante-cuota/${id}" title="Ver comprobante">
                <i class="fas fa-file-pdf"></i>
            </a>
            `
        }
        const tr = document.createElement("tr");
        tr.innerHTML = `
        <th scope="row">${numeroCuota}</th>
        <td>${fechaVencimiento}</td>
        <td>${fechaPago}</td>
        <td>${gen.resetearMoneda(montoPagar,tipoMoneda)}</td>
        <td>${gen.resetearMoneda(montoPagado,tipoMoneda)}</td>
        <td>${descripcion}</td>
        <td>
            ${$estadoPago}
        </td>
        <td>
            <div class="d-flex flex-wrap justify-content-center" style="gap: 5px;">
                ${$botonVerComprobante}
                <button class="btn btn-sm btn-info modificar-cuota" data-cuota="${id}" type="button" title="Modificar cuota y/o pago">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger eliminar-cuota" data-cuota="${id}" type="button" title="Eliminar cuota">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </td>
        `
        return tr;
    }
    const tablaCuotasPagos = document.querySelector("#generarPagos #contenidoPagosCuotas")
    const linkReporteActas = document.querySelector("#verReporteActas");
    tablaOs.addEventListener("click",async (e)=>{
        if(e.target.classList.contains("editar-os")){
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
                const detalleOrdenServicio = response.ordenServicio;
                tinymce.activeEditor.setContent(!detalleOrdenServicio.observaciones ? "" : detalleOrdenServicio.observaciones);
                const {fecha,tipoMoneda,id,adicionales,nombreCliente} = detalleOrdenServicio;
                detalleOrdenServicio.cotizaciones.forEach((servicio, key) => {
                    servicio.index = key + 1;
                    servicio.tipoMoneda = tipoMoneda;
                    template += ordenServicio.agregarDetallServicios(servicio).outerHTML;
                    listaServicios.push(servicio);
                });
                idOrdenServicio = id;
                tablaServiciosAdicionales.dataset.tipo = "lleno";
                tablaServicios.innerHTML = !template ? `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios y/o productos</td></tr>` : template;
                tablaServiciosAdicionales.innerHTML = "";
                adicionales.forEach((adicional, key) => {
                    adicional.index = key + 1;
                    adicional.tipoMoneda = tipoMoneda;
                    tablaServiciosAdicionales.append(ordenServicio.generarServiciosAdicionales(adicional));
                });
                if(!adicionales.length){
                    tablaServiciosAdicionales.innerHTML = `
                    <tr><td colspan="100%" class="text-center">No se agregaron servicios adicionales</td></tr>
                    `;
                    tablaServiciosAdicionales.dataset.tipo = "vacio";
                }
                cbCotizaciones.append(new Option("",""));
                detalleOrdenServicio.listaServicios.forEach(cotizacion => {
                    const opcion = new Option("N° " + cotizacion.nroCotizacion,cotizacion.id);
                    cbCotizaciones.append(opcion);
                });
                for (const input of tablaServiciosAdicionales.querySelectorAll(
                    ".punitari-servicios, .cantidad-servicios"
                )) {
                    input.addEventListener("change", e => {ordenServicio.calcularMonto({e,listaServicios,tablaServiciosAdicionales,tipoMoneda})});
                }
                document.querySelector("#idModalcliente").value = nombreCliente;
                document.querySelector("#idModalfechaEmitida").value = fecha;
                $("#editarOrdenServicio #idModaltipoMoneda").val(tipoMoneda).trigger("change");
                ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales,tipoMoneda);
                $('#editarOrdenServicio').modal("show");
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener los datos de la orden de servicio");
            }
        }
        if(e.target.classList.contains("generar-acta")){
            try {
                const response = await gen.funcfetch("acta-entrega/" + e.target.dataset.ordenServicio,null,"GET");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => {
                        window.location.reload();
                    });
                }
                if(response.alerta){
                    return alertify.alert("Alerta",response.alerta);
                }
                idOrdenServicio = e.target.dataset.ordenServicio;
                for (const key in response.actas) {
                    if (Object.hasOwnProperty.call(response.actas, key)) {
                        const valor = response.actas[key];
                        const dom = document.querySelector("#generarActaEntrega #idModalActa" + key);
                        if(key === 'firma_representante' && valor){
                            signaturePad.fromDataURL(window.origin + '/intranet/storage/firmaEntregaActas/' + valor,{width: 300, height: 150, xOffset: 0, yOffset: 0 });
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                if(response.actas){
                    idActaEntrega = response.actas.id;
                    linkReporteActas.href= "acta-entrega/reporte/" + idActaEntrega;
                }
                $('#generarActaEntrega .select2-simple').trigger("change");
                $('#generarActaEntrega').modal("show")
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener los datos de la acta de entrega");
            }
        }
        if(e.target.classList.contains("pago-cuotas")){
            try {
                const response = await gen.funcfetch("pago/cuotas/" + e.target.dataset.ordenServicio,null,"GET");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => {
                        window.location.reload();
                    });
                }
                idOrdenServicio = e.target.dataset.ordenServicio;
                let template = "";
                response.cuotas.forEach(cuota => {
                    template += renderizarCuotaPagos(cuota).outerHTML;
                });
                radioFacturacionExterna.checked = response.facturacionExterna === 1 ? true : false;
                tablaCuotasPagos.innerHTML = !template ? `<tr><td colspan="100%" class="text-center">No se asignaron cuotas</td></tr>` : template;
                $('#generarPagos').modal("show");
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
                servicio.tipoMoneda = $tipoMoneda.value;
                tablaServicios.append(ordenServicio.agregarDetallServicios(servicio));
                listaServicios.push(servicio);
            });
            ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales,$tipoMoneda.value);
            const indiceBorrar = Array.from(cbCotizaciones.options).findIndex(option => option.value == $(cbCotizaciones).val());
            if(indiceBorrar >= 0){
                cbCotizaciones.remove(indiceBorrar);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar los servicios y/o de la cotizacion")
        }
    })
    const btnAgregarServiciosAdicionales = document.querySelector(
        "#btnAgregarServiciosAdicionales"
    );
    btnAgregarServiciosAdicionales.onclick = e => ordenServicio.agregarServiciosAdicionales(tablaServiciosAdicionales,listaServicios,$tipoMoneda.value);
    const frmOs = document.querySelector("#frmOrdenServicio");
    $('#generarActaEntrega').on('hidden.bs.modal', function (event) {
        formularioActa.reset();
        $('#generarActaEntrega .select2-simple').trigger("change");
        signaturePad.clear();
        idActaEntrega = null;
        idOrdenServicio = null;
        linkReporteActas.href = "#";
    });
    $('#editarOrdenServicio').on('hidden.bs.modal', function (event) {
        cbCotizaciones.innerHTML = "";
        tablaServiciosAdicionales.innerHTML = "";
        listaServicios = [];
        idOrdenServicio = null;
        ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales,$tipoMoneda.value);
        frmOs.reset();
        tinymce.activeEditor.setContent("");
        $("#editarOrdenServicio #idModaltipoMoneda").val("").trigger("change");
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
                ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales,$tipoMoneda.value);
                alertify.success("Servicio adicional eliminado correctamente");
            },()=>{})
        }
    });
    tablaServicios.addEventListener("click",(e) => {
        if(e.target.classList.contains("btn-danger")){
            alertify.confirm("Mensaje","¿Deseas quitar este item de la orden de servicio?",async ()=>{
                if(e.target.dataset.cotizacionServicio){
                    const cotizacionServicio = e.target.dataset.cotizacionServicio;
                    let datos = new FormData();
                    datos.append("cotizacionServicioId",cotizacionServicio);
                    datos.append("ordenServicioId",idOrdenServicio);
                    datos.append("acciones","eliminar-cotizacion");
                    datos.append("tipoDetalle",e.target.dataset.tipo);
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
                ordenServicio.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales,$tipoMoneda.value);
                alertify.success("Servicio eliminado correctamente");
            },()=>{})
        }
    });
    document.querySelector("#btnGuardarFrm").onclick = e => document.querySelector("#btnEnviar").click();
    frmOs.addEventListener("submit",async (e)=>{
        e.preventDefault();
        let datos = new FormData(e.target);
        datos.append("ordenServicioId",idOrdenServicio);
        datos.append("observaciones",tinymce.activeEditor.getContent());
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
    });
    //Pago a cuotas
    const tablaCuotas = document.querySelector("#contenidoPagosCuotas");
    const radioFacturacionExterna = document.querySelector("#generarPagos #facturacionExterna");
    const radioPagosCuotas = document.querySelector("#radioCambioPagos");
    const botonComprobanteSunat = document.querySelector("#modificarCuota #botonDocumentoComprobante");
    const contenidoPagosImagenes = document.querySelector("#modificarCuota #contenidoImagenPagos");
    const documentoSunat = document.querySelector("#modificarCuota #documentoComprobante");
    const enlaceComprobante = document.querySelector("#modificarCuota #enlaceDocumentoComprobante");
    const botonEliminarDocumentoSunat = document.querySelector("#modificarCuota #eliminarDocumentoSunat");
    document.querySelector("#btnGuardarCuota").onclick = e => document.querySelector("#enviarCuota").click();
    radioPagosCuotas.addEventListener("change",e => cambioPago(!e.target.checked));
    botonComprobanteSunat.onclick = e => documentoSunat.click();
    radioFacturacionExterna.addEventListener("change",async function(e){
        const valor = e.target.checked;
        let datos = new FormData();
        datos.append("valor", e.target.checked);
        datos.append("ordenServicio", idOrdenServicio);
        try {
            const response = await gen.funcfetch(`pago/cuota/facturacion-externa`,datos,"POST");
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => {
                    window.location.reload();
                });
            }
            alertify.success(response.success);
        }catch(error){
            e.target.checked = valor ? false : true;
            console.error(error);
            alertify.error("error al modificar la facturacion externa")
        }
    });
    function cambioPago(valor){
        for (const textoPago of document.querySelectorAll("#modificarCuota .pago-texto")) {
            textoPago.disabled = valor
        }
        for (const domPago of document.querySelectorAll("#modificarCuota .pagos-ocultar")) {
            domPago.hidden = valor
        }
        botonComprobanteSunat.hidden = radioFacturacionExterna.checked && radioPagosCuotas.checked ? false : true;
    }
    botonEliminarDocumentoSunat.addEventListener("click", e => {
        if(e.target.dataset.valor){
            alertify.confirm("Mensaje","¿Deseas eliminar este comprobante externo de manera permanente?",async ()=>{
                try {
                    const response = await gen.funcfetch(`pago/cuota-comprobante/${idOrdenServicio}/${idCuota}`,null,"DELETE");
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => {
                            window.location.reload();
                        });
                    }
                    if (response.alerta) {
                        return alertify.alert("Alerta",response.alerta);
                    }
                    e.target.parentElement.hidden = true;
                    documentoSunat.value = "";
                    alertify.success(response.success);
                }catch(error){
                    console.error(error);
                    alertify.error("error al eliminar el comprobante externo")
                }
            }, () =>{})
        }else{
            e.target.parentElement.hidden = true;
            documentoSunat.value = "";
            alertify.success("comprobante eliminado correctamente");
        }
    });
    documentoSunat.addEventListener("change", e => {
        const input = e.target;
        enlaceComprobante.removeAttribute("target");
        if(!input.value){
            enlaceComprobante.parentElement.hidden = true;
            return
        }
        enlaceComprobante.href = "javascript:void(0)";
        enlaceComprobante.parentElement.hidden = false;
        enlaceComprobante.textContent = input.files[0].name;
        enlaceComprobante.parentElement.querySelector("button").removeAttribute('data-valor');
    });
    let idCuota = null;
    const tituloCuota = document.querySelector("#modificarCuota #tituloCuota")
    function renderizarImagenesPagos({id,nombre,url}) {
        const contenidoImagen = document.createElement("div");
        contenidoImagen.className = "form-group col-12 col-lg-6 contenido-img";
        contenidoImagen.innerHTML = `
        <img src="${window.origin + "/intranet/storage/pagoCuotasImg/" + url}" class="img-fluid d-block m-auto"/>
        <button type="button" title="Eliminar imagen" class="btn btn-sm btn-danger" data-img="${id}">
        <i class="fas fa-trash-alt"></i>
        </button>`;
        return contenidoImagen;
    }
    async function cargarImagen(files,idOrdenServicio,idCuota) {
        if(!files.length){
            return
        }
        try {
            const imagenes = files;
            const pattern = /image-*/;
            for (let i = 0; i < imagenes.length; i++) {
                if(!files[i].type.match(pattern)){
                    alertify.alert("Mensaje", "El archivo " + files[i].name +" no es se cargo correctamente debido a que no es una imagen");
                    continue;
                }
                let datos = new FormData();   
                datos.append("ordenServicio",idOrdenServicio);
                datos.append("cuota",idCuota);
                datos.append("imagen",files[i]);
                let response = await gen.funcfetch("pago/cuota-prueba/imagen",datos,"POST");
                if(response.session){
                    return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
                }
                if(response.alerta){
                    return alertify.alert("Alerta",response.alerta);
                }
                const $img = renderizarImagenesPagos(response);
                contenidoPagosImagenes.append($img);
            }
            alertify.success(imagenes.length > 1 ? imagenes.length + ' imagenes cargadas correctamente' : '1 imagen cargada correctamente');
        } catch (error) {
            console.error(error);
            alertify.error("error al cargar las imagenes");
        }
    }
    const documentoImagenPagos = document.querySelector("#modificarCuota #documentoImagenPagos");
    const formularioCuotas = document.querySelector("#generarPagos #frmPagoCredito");
    const numeroCuota = document.querySelector("#generarPagos #numeroCuotas");
    const formularioPago = document.querySelector("#modificarCuota #frmCuotaPago");
    documentoImagenPagos.addEventListener("change", e => cargarImagen(e.target.files,idOrdenServicio,idCuota));
    document.querySelector("#modificarCuota #btnAgregarImagenPagos").addEventListener("click", e => documentoImagenPagos.click());
    contenidoPagosImagenes.addEventListener("click",e => {
        if(e.target.classList.contains("btn-danger")){
            alertify.confirm("Mensaje","¿Deseas eliminar esta imagen de manera permanente?",async ()=>{
                try {
                    const response = await gen.funcfetch(`pago/cuota-prueba/imagen/${idCuota}/${e.target.dataset.img}`,null,"DELETE");
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => {
                            window.location.reload();
                        });
                    }
                    if (response.alerta) {
                        return alertify.alert("Alerta",response.alerta);
                    }
                    e.target.parentElement.remove();
                    alertify.success(response.success);
                }catch(error){
                    console.error(error);
                    alertify.error("error al eliminar la imagen")
                }
            },()=>{})
        }
    });
    tablaCuotas.addEventListener("click",async e => {
        if(e.target.classList.contains("modificar-cuota")){
            $('#generarPagos').modal("hide");
            try {
                const response = await gen.funcfetch(`pago/cuota/${idOrdenServicio}/${e.target.dataset.cuota}`,null,"GET");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => {
                        window.location.reload();
                    });
                }
                idCuota = response.cuota.id;
                tituloCuota.textContent = "Modificar cuota " + response.cuota.numeroCuota;
                response.imagenesPagos.forEach(img => {
                    contenidoPagosImagenes.append(renderizarImagenesPagos(img));
                });
                for (const key in response.cuota) {
                    if (Object.hasOwnProperty.call(response.cuota, key)) {
                        const valor = response.cuota[key];
                        const dom = document.querySelector("#modificarCuota #idCuota" + key);
                        if(key === "comprobanteNombre" && valor){
                            enlaceComprobante.parentElement.hidden = false;
                            enlaceComprobante.textContent = valor;
                            enlaceComprobante.setAttribute("target","_blank");
                            enlaceComprobante.parentElement.querySelector("button").dataset.valor = "true";
                            enlaceComprobante.href = `pago/cuota/comprobante-sunat/${idOrdenServicio}/${idCuota}`;
                            continue;
                        }
                        if(key === "estado" && valor === 2){
                            radioPagosCuotas.checked = true;
                            cambioPago(false);
                            botonComprobanteSunat.hidden = false;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#modificarCuota .select2-simple').trigger("change");
                setTimeout(e => {
                    $('#modificarCuota').modal("show");
                },300);
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener los datos de la cuota")
            }
        }
        if(e.target.classList.contains("eliminar-cuota")){
            alertify.confirm("Alerta","¿Deseas eliminar esta cuota?",async () =>{
                try {
                    const response = await gen.funcfetch(`pago/cuota/${idOrdenServicio}/${e.target.dataset.cuota}`,null,"DELETE");
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => {
                            window.location.reload();
                        });
                    }
                    if (response.alerta) {
                        return alertify.alert("Alerta",response.alerta);
                    }
                    let template = "";
                    response.cuotas.forEach(cuota => {
                        template += renderizarCuotaPagos(cuota).outerHTML;
                    });
                    tablaCuotasPagos.innerHTML = !template ? `<tr><td colspan="100%" class="text-center">No se asignaron cuotas</td></tr>` : template;
                    alertify.success(response.success);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la cuota");
                }
            },()=>{})
        }
    })
    $('#modificarCuota').on('hidden.bs.modal', function (event) {
        enlaceComprobante.parentElement.hidden = true;
        enlaceComprobante.removeAttribute("target");
        enlaceComprobante.parentElement.querySelector("button").removeAttribute("data-valor");
        radioPagosCuotas.checked = false;
        botonComprobanteSunat.hidden = true;
        contenidoPagosImagenes.innerHTML = "";
        documentoSunat.value = "";
        idCuota = null;
        cambioPago(true);
        setTimeout(e => {
            $('#generarPagos').modal('show');
        },300);
    });
    formularioCuotas.addEventListener("submit",function(e){
        e.preventDefault();
        const valorCuota = numeroCuota.value;
        alertify.confirm("Mensaje","¿Deseas agregar " + valorCuota + " cuotas?",async () => {
            try {
                let datos = new FormData(e.target);
                datos.append("ordenServicioId",idOrdenServicio);
                const response = await gen.funcfetch("pago/cuota-agregar",datos,"POST");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => {
                        window.location.reload();
                    });
                }
                if (response.error) {
                    return alertify.alert("Alerta",response.error);
                }
                let template = "";
                response.cuotas.forEach(cuota => {
                    template += renderizarCuotaPagos(cuota).outerHTML;
                });
                tablaCuotasPagos.innerHTML = !template ? `<tr><td colspan="100%" class="text-center">No se asignaron cuotas</td></tr>` : template;
                alertify.success(response.success);
            } catch (error) {
                console.error(error);
                alertify.error("error al agregar las cuotas");
            }
        }, () => {})
    })
    formularioPago.addEventListener("submit",async function(e){
        e.preventDefault();
        try {
            let datos = new FormData(e.target);
            datos.append("ordenServicioId",idOrdenServicio);
            datos.append("cuotaId",idCuota);
            const response = await gen.funcfetch("pago/cuota-modificar",datos,"POST");
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => {
                    window.location.reload();
                });
            }
            if (response.alerta) {
                return alertify.alert("Alerta",response.alerta);
            }
            let template = "";
            response.cuotas.forEach(cuota => {
                template += renderizarCuotaPagos(cuota).outerHTML;
            });
            tablaCuotasPagos.innerHTML = !template ? `<tr><td colspan="100%" class="text-center">No se asignaron cuotas</td></tr>` : template;
            $('#modificarCuota').modal("hide");
            alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al editar la cuota");
        }
    })
    
}
window.addEventListener("DOMContentLoaded",loadPage);