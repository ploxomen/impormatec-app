function loadPage() {
    let gen = new General();
    let ordenServicio = new OrdenServicio();
    let cbClientes = document.querySelector("#cbClientes");
    let tablaServicios = document.querySelector("#contenidoServicios");
    let tablaServiciosAdicionales = document.querySelector(
        "#tablaServiciosAdicionales"
    );
    let listaDetalleCotizacion = [];
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
    const $tipoMoneda = document.querySelector("#idModaltipoMoneda");
    const cbTipoIgv = document.querySelector("#idModalincluirIGV");
    document.querySelector("#aplicarFiltros").addEventListener("click",e => obtenerDetalleCotizacion($(cbClientes).val()));
    async function obtenerDetalleCotizacion(idClientes){
        listaDetalleCotizacion = [];
        if (idClientes == "ninguno") {
            ordenServicio.calcularServiciosTotales(listaDetalleCotizacion,tablaServiciosAdicionales,$tipoMoneda.value);
            tablaServicios.innerHTML = `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios y/o productos</td></tr>`;
            return false;
        }
        try {
            const response = await gen.funcfetch(
                "clientes/" + idClientes + "?tipoMoneda=" + $tipoMoneda.value + "&conIgv=" + cbTipoIgv.value,
                null,
                "GET"
            );
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => {
                    window.location.reload();
                });
            }
            const detalleCotizaciones = response.detalleCotizacion;
            let template = "";
            let index = 0;
            detalleCotizaciones.forEach(productoServicio => {
                productoServicio.detalleCotizacion.forEach(detalle => {
                    index++
                    const {id,tipo,cantidad,precio,descuento,total,nombreDescripcion} = detalle
                    const valores = {
                        index,
                        nroCotizacion : productoServicio.nroCotizacion,
                        idCotizacion : productoServicio.id,
                        servicio : nombreDescripcion,
                        idCotizacionServicio : id,
                        tipoServicioProducto : tipo,
                        tipoMoneda : $tipoMoneda.value,
                        cantidad,
                        precio,
                        descuento,
                        total
                    }
                    listaDetalleCotizacion.push(valores);
                    template += ordenServicio.agregarDetallServicios(valores).outerHTML;
                });
            });
            tablaServicios.innerHTML = !template
                ? `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios y/o productos</td></tr>`
                : template;
            ordenServicio.calcularServiciosTotales(listaDetalleCotizacion,tablaServiciosAdicionales,$tipoMoneda.value);
            } catch (error) {
            alertify.error("error al obtener las cotizaciones aprobadas");
            console.error(error);
        }
        
    }
    $(cbClientes).on("select2:select", e => {
        const opcion = e.target.options[e.target.selectedIndex];
        $(cbTipoIgv).prop('disabled',false);
        $(cbTipoIgv).val("1").trigger("change");
        if(opcion.dataset.igv === 'false'){
            $(cbTipoIgv).prop('disabled',true);
            $(cbTipoIgv).val("0").trigger("change");
        }
        obtenerDetalleCotizacion($(cbClientes).val());
    });
    $($tipoMoneda).on("select2:select", e => obtenerDetalleCotizacion($(cbClientes).val()));
    $(cbTipoIgv).on("select2:select", e => obtenerDetalleCotizacion($(cbClientes).val()));
    tablaServicios.addEventListener("click",(e)=>{
        if(e.target.dataset.cotizacionServicio){
            const mensaje = e.target.dataset.tipo === 'servicio' ? '¿Deseas eliminar este servicio?.<br>Recuerda que si este servicio cuenta con <strong>informe y certificado</strong>, tambien seran eliminados.' : '¿Deseas eliminar este producto?';
            alertify.confirm("Alerta",mensaje,()=>{
                listaDetalleCotizacion = ordenServicio.eliminarServicio(e,listaDetalleCotizacion,tablaServicios);
                ordenServicio.calcularServiciosTotales(listaDetalleCotizacion,tablaServiciosAdicionales,$tipoMoneda.value);
            },()=>{});
        }
    })
    const btnAgregarServiciosAdicionales = document.querySelector(
        "#btnAgregarServiciosAdicionales"
    );
    btnAgregarServiciosAdicionales.onclick = e => ordenServicio.agregarServiciosAdicionales(tablaServiciosAdicionales,listaDetalleCotizacion,$tipoMoneda.value);
    const btnAgregar = document.querySelector("#btnAgregarCotizacion");
    document.querySelector("#frmCotizacion").addEventListener("submit", async (e) => {
        e.preventDefault();
        const data = new FormData(e.target);
        gen.cargandoPeticion(btnAgregar, gen.claseSpinner, true);
        try {
            if($(cbClientes).val() == 'ninguno'){
                return alertify.error("por favor seleccione un cliente");
            }
            if(!listaDetalleCotizacion.length){
                return alertify.alert("Mensaje","Para generar una nueva orden de servicio debe tener al menos un item como detalle de la orden de servicio");
            }
            data.append('listaDetalleCotizacion',JSON.stringify(listaDetalleCotizacion));
            data.append("observaciones",tinymce.activeEditor.getContent());
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
