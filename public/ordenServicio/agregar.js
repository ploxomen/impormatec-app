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
    const $tipoMoneda = document.querySelector("#idModaltipoMoneda");
    async function obtenerDetalleCotizacion(idClientes){
        listaDetalleCotizacion = [];
        if (idClientes == "ninguno") {
            ordenServicio.calcularServiciosTotales(listaDetalleCotizacion,tablaServiciosAdicionales,$tipoMoneda.value);
            tablaServicios.innerHTML = `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios y/o productos</td></tr>`;
            return false;
        }
        try {
            const response = await gen.funcfetch(
                "clientes/" + idClientes + "?tipoMoneda=" + $tipoMoneda.value,
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
                    const {id,tipo,cantidad,importe,descuento,total,nombreDescripcion} = detalle
                    const valores = {
                        index,
                        nroCotizacion : productoServicio.nroCotizacion,
                        idCotizacion : productoServicio.id,
                        servicio : nombreDescripcion,
                        idCotizacionServicio : id,
                        tipoServicioProducto : tipo,
                        tipoMoneda : $tipoMoneda.value,
                        cantidad,
                        importe,
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
    $(cbClientes).on("select2:select", e => obtenerDetalleCotizacion($(cbClientes).val()));
    $($tipoMoneda).on("select2:select", e => obtenerDetalleCotizacion($(cbClientes).val()));
    tablaServicios.addEventListener("click",(e)=>{
        if(e.target.dataset.cotizacionServicio){
            alertify.confirm("Alerta","¿Deseas quitar este item de la orden de servicio?",()=>{
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
