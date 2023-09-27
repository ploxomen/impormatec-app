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
            cotizacionGeneral.calcularServiciosTotales(serviciosProductos,tablaServicios);
        }
    })
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
        branding: false,
        height: "400px",
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: (cb, value, meta) => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.addEventListener('load', () => {
                const id = 'blobid' + (new Date()).getTime();
                const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                const base64 = reader.result.split(',')[1];
                const blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), { title: file.name });
            });
            reader.readAsDataURL(file);
            });
            input.click();
        },
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
            const datosDetalle = {
                tipo : !tr.dataset.servicio ? 'producto' : 'servicio',
                idDetalle : tr.dataset.servicio || tr.dataset.producto
            };
            serviciosProductos = serviciosProductos.filter(function(detalle){
                if(datosDetalle.tipo === "servicio" && detalle.idServicio === datosDetalle.idDetalle && !detalle.idProducto){
                    return false;
                }
                if(datosDetalle.tipo === "producto" && detalle.idProducto === datosDetalle.idDetalle && !detalle.idServicio){
                    return false;
                }
                return true;
            });
            cotizacionGeneral.cbServiciosOpt(cbServicios,false,[{id:datosDetalle.idDetalle,tipo:datosDetalle.tipo}]);
            tr.remove();
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
                            s.id_servicio = s.id;
                            let total = 0;
                            serviciosCb.push({id:s.id,tipo:"servicio"});
                            s.productos.forEach(p => {
                                total += p.precioVenta * p.cantidadUsada;
                            });
                            s.cantidad = 1;
                            s.costo = total;
                            s.descuento = 0;
                            s.total = total;
                            serviciosProductos.push(cotizacionGeneral.asignarListaServiciosProductos(s));
                            tablaServicios.append(cotizacionGeneral.agregarServicio(k+1,s.id,s.servicio,1,total,0,total,"servicio","nuevo"));
                            tablaServicioProductos.append(cotizacionGeneral.agregarServicioProductos(s.id,s.servicio,s.productos));
                        });
                        $('#listaServiciosProductos .cb-servicios-productos').select2({
                            theme: 'bootstrap',
                            width: '100%',
                            placeholder: "Seleccionar un producto",
                        }).on("select2:select",function(e){
                            cotizacionGeneral.obtenerProducto($(this),serviciosProductos,tablaServicios);
                        });
                        for (const cambio of tablaServicios.children[tablaServicios.children.length - 1].querySelectorAll(".cambio-detalle")) {
                            cambio.addEventListener("change",function(){
                                cotizacionGeneral.modificarCantidad(cambio,serviciosProductos,tablaServicios);
                            });
                        }
                        for (const cambio of tablaServicioProductos.children[tablaServicioProductos.children.length - 1].querySelectorAll(".cambio-detalle")) {
                            cambio.addEventListener("change", function(){
                                cotizacionGeneral.modificarCantidad(cambio,serviciosProductos,tablaServicios);
                            });
                        }
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