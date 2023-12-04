function loadPage(){
    let gen = new General();
    let cotizacionGeneral = new Cotizacion();
    const cbPreCotizacion = document.querySelector("#cbPreCotizacion");
    const cbClientes = document.querySelector("#idModalid_cliente");
    const cbContactos = document.querySelector("#cbContactosCliente");
    const tablaServicios = document.querySelector("#contenidoServicios");
    const tablaServicioProductos = document.querySelector("#listaServiciosProductos");
    const formCotizacion = document.querySelector("#frmCotizacion");
    const checkIncluirCotizacion = document.querySelector("#incluirPreCotizacion");
    let cbServicios = document.querySelector("#cbServicios");
    let serviciosProductos = [];
    tablaServicioProductos.addEventListener("click",function(e){
        if(e.target.classList.contains("btn-danger")){
            const tr = e.target.parentElement.parentElement;
            const tablaBody = e.target.parentElement.parentElement.parentElement;
            const indexServicio = serviciosProductos.findIndex(s => s.idServicio == tr.dataset.servicio);
            if(indexServicio < 0){
                return alertify.error("servicio no encontrado");
            }
            if(serviciosProductos[indexServicio].productosLista.length === 1){
                return alertify.error("el servicio debe contener al menos un producto");
            }
            serviciosProductos[indexServicio].productosLista = serviciosProductos[indexServicio].productosLista.filter(producto => producto.idProducto != tr.dataset.producto);
            $("#" + e.target.dataset.cbproducto)[0].querySelector('option[value="' + tr.dataset.producto + '"]').disabled = false;
            tr.remove();
            cotizacionGeneral.calcularNumeroItem(tablaBody);
            cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
        }
    });
    $('#idModalincluirIGV').on("select2:select",async function(e){
        cotizacionGeneral.ocultarMostrarIGV(serviciosProductos,$(this).val());
        cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
    });
    const txtDireccion = document.querySelector("#idModaldireccion");
    $(cbClientes).on("select2:select",async function(e){
        let datos = new FormData();
        datos.append("cliente",$(this).val());
        try {
            const response = await gen.funcfetch("obtener/cliente",datos, "POST");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.cliente && Object.keys(response.cliente).length){
                nuevoCliente = false;
                cbContactos.innerHTML = "";
                cbContactos.append({id : null });
                response.cliente.contactos.forEach(c => {
                    cbContactos.append(cotizacionGeneral.templateOpcionContacto(c));
                });
                txtDireccion.value = response.cliente.direccion;
                if(response.cliente.id_pais !== 165){
                    $('#idModalincluirIGV').val("0").trigger("change");
                    $('#idModalincluirIGV').prop("disabled",true);
                }else{
                    $('#idModalincluirIGV').prop("disabled",false);
                }
                cotizacionGeneral.ocultarMostrarIGV(serviciosProductos,$('#idModalincluirIGV').val());
                cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener la informacion del cliente");
        }
    });
    const btnNuevaCotizacion = document.querySelector("#btnOtrosDocumentos");
    const fileOtrosDocumentos = document.querySelector("#fileOtrosDocumentos");
    btnNuevaCotizacion.onclick = e => fileOtrosDocumentos.click();
    const contenedorArchivoPdf = document.querySelector("#contenedorArchivoPdf");
    contenedorArchivoPdf.addEventListener("click",function(e){
        if(e.target.classList.contains("btn-sm")){
            e.target.parentElement.remove();
            return alertify.success("archivo eliminado");
        }
    });
    tinymce.init({
        selector: '#notaCotizacion',
        language: 'es',
        plugins: 'anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        image_title: true,
        content_style: "body { font-family: andale mono, monospace; }",
        branding: false,
        height: "400px",
        automatic_uploads: true,
        images_upload_url: window.origin + '/intranet/storage/editor/img-cotizacion/save',
        file_picker_types: 'image',
        images_upload_handler : (blobInfo, progress) => new Promise(async (resolve, reject) => {
            let datos = new FormData();
            datos.append('file', blobInfo.blob(), blobInfo.filename());
            try {
                const reponse = await gen.funcfetch(window.origin + '/intranet/storage/editor/img-cotizacion/save',datos,"POST");
                resolve(reponse.location);
            } catch (error) {
                reject(error);
            }
        })
    });
    fileOtrosDocumentos.addEventListener("change",function(e){
        const files = e.target.files;
        if(!files.length){
            return false
        }
        for (let i = 0; i < files.length; i++) {
            cotizacionGeneral.renderPdfCargados({valorDocumento : files[i],contenedorArchivoPdf, nombreDocumento : files[i].name,idDocumento : null});
        }
    });
    const btnAgregarCoti = document.querySelector("#btnAgregarCotizacion");
    formCotizacion.addEventListener("submit",async function(e){
        e.preventDefault();
        if(!serviciosProductos.length){
            return alertify.error("la cotización debe contener al menos un servicio o producto");
        }
        let datos = new FormData(this);
        if(!datos.has('id_cliente')){
            datos.append('id_cliente',$(cbClientes).val());
        }
        datos.append("servicios",JSON.stringify(serviciosProductos));
        datos.append("textoNota",tinymce.activeEditor.getContent());
        gen.cargandoPeticion(btnAgregarCoti, gen.claseSpinner, true);
        try {
            const response = await gen.funcfetch("agregar",datos,"POST");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            gen.abrirPesatana(response.urlPdf);
            return alertify.alert("Mensaje",response.success,() => window.location.reload());
        } catch (error) {
            console.error(error);
            alertify.error("error al generar una cotización");
        }finally{
            gen.cargandoPeticion(btnAgregarCoti, 'fas fa-plus', false);
        }
    })
    tablaServicios.addEventListener("click",function (e) {  
        if (e.target.classList.contains("btn-danger")){
            const tr = e.target.parentElement.parentElement;
            const tablaBody = tr.parentElement;
            const datosDetalle = {
                tipo : !tr.dataset.servicio ? 'producto' : 'servicio',
                idDetalle : tr.dataset.servicio || tr.dataset.producto
            };
            serviciosProductos = serviciosProductos.filter(function(detalle){
                if(datosDetalle.tipo === "servicio" && detalle.idServicio === +datosDetalle.idDetalle && !detalle.idProducto){
                    return false;
                }
                if(datosDetalle.tipo === "producto" && detalle.idProducto === +datosDetalle.idDetalle && !detalle.idServicio){
                    return false;
                }
                return true;
            });
            cotizacionGeneral.cbServiciosOpt(cbServicios,false,[{id:datosDetalle.idDetalle,tipo:datosDetalle.tipo}]);
            tr.remove();
            cotizacionGeneral.calcularNumeroItem(tablaBody);
            if(datosDetalle.tipo === "servicio"){
                const tablaProductoServicio = tablaServicioProductos.querySelector(`[data-domservicio="${datosDetalle.idDetalle}"]`);
                if(tablaProductoServicio){
                    tablaProductoServicio.remove();
                }
                if (!serviciosProductos.length) {
                    tablaServicios.innerHTML = `<tr>
                        <td colspan="100%" class="text-center">No se seleccionaron servicios o productos</td>
                    </tr>`;
                    tablaServicioProductos.innerHTML = `
                    <h5 class="col-12 text-primary text-center">
                        Sin productos para mostrar  
                    </h5>
                    `
                }
            }
            alertify.success("item eliminado correctamente");
            cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
        }
    });
    $(cotizacionGeneral.$cbTipoMoneda).on("select2:select", function (e) {
        cotizacionGeneral.modificarMonedaTotal($(this).val(),serviciosProductos,tablaServicios)
    });
    $(cbServicios).on("select2:select", function (e) {
        cotizacionGeneral.obtenerServicios(cbServicios,$(this).val(),serviciosProductos,tablaServicios,tablaServicioProductos,e.params.data.element.dataset.tipo||"servicio");
    });
    $(cbPreCotizacion).on("select2:select", async function (e) {
        const preCotizacionId = $(this).val();
        $(cbClientes).prop("disabled",false);
        if(preCotizacionId == "ninguno"){
            cotizacionGeneral.limpiarCotizacion(serviciosProductos,cbContactos,tablaServicios,tablaServicioProductos,cbServicios,checkIncluirCotizacion);
            return false;
        }
        checkIncluirCotizacion.disabled = false;
        try {
            $(cbClientes).prop("disabled",true);
            cotizacionGeneral.cbServiciosOpt(cbServicios,false,null);
            serviciosProductos = [];
            const response = await gen.funcfetch("obtener/precotizacion/" + preCotizacionId, null, "GET");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            let serviciosCb = [];
            for (const key in response.preCotizacion) {
                if (Object.hasOwnProperty.call(response.preCotizacion, key)) {
                    const valor = response.preCotizacion[key];
                    const dom = document.querySelector("#idModal" + key);
                    if(key == "contactos"){
                        cbContactos.append({id : null });
                        valor.forEach(c => {
                            cbContactos.append(cotizacionGeneral.templateOpcionContacto(c));
                        });
                        continue;
                    }
                    if(key == "servicios"){
                        tablaServicios.innerHTML = valor.length ? "" : `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios<td></tr>`;
                        tablaServicioProductos.innerHTML = valor.length ? "" : `<h5 class="col-12 text-primary text-center">Sin productos para mostrar</h5>`
                        valor.forEach((s,k) => {
                            serviciosCb.push({id:s.id,tipo:"servicio"});
                            const serviciosCotizacion = cotizacionGeneral.asignarListaServiciosProductos(s,"servicio","nuevo");
                            serviciosCotizacion.cantidad = 1;
                            serviciosProductos.push(serviciosCotizacion);
                            serviciosCotizacion.numeroItem = tablaServicios.children.length + 1;
                            tablaServicios.append(cotizacionGeneral.agregarServicio(serviciosCotizacion));
                            tablaServicios.children[tablaServicios.children.length - 1].querySelector(".cambio-detalle").addEventListener("change",function(){
                                cotizacionGeneral.modificarCantidad(this,serviciosProductos,tablaServicios);
                            });
                            tablaServicioProductos.append(cotizacionGeneral.agregarServicioProductos(serviciosCotizacion));
                            for (const cambio of tablaServicioProductos.children[tablaServicioProductos.children.length - 1].querySelectorAll(".cambio-detalle")) {
                                cambio.addEventListener("change", function(){
                                    cotizacionGeneral.modificarCantidad(cambio,serviciosProductos,tablaServicios);
                                });
                            }
                        });
                        $('#listaServiciosProductos .cb-servicios-productos').select2({
                            theme: 'bootstrap',
                            width: '100%',
                            placeholder: "Seleccionar un producto",
                        }).on("select2:select",function(e){
                            cotizacionGeneral.obtenerProducto($(this),serviciosProductos,tablaServicios);
                        });
                        cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
                        continue;
                    }
                    if(!dom){
                        continue;
                    }
                    dom.value = valor;
                }
            }
            cotizacionGeneral.cbServiciosOpt(cbServicios,true,serviciosCb);
            $(cbServicios).val("").trigger("change");
            $('.select2-simple').trigger("change");
        } catch (error) {
            alertify.error("error al obtener la pre - cotizacion");
            console.error(error);
        }
    });
    
}
window.addEventListener("DOMContentLoaded",loadPage);