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
    let tablaServicios = document.querySelector("#contenidoServicios");
    let tablaServiciosAdicionales = document.querySelector("#tablaServiciosAdicionales");
    let $tipoMoneda = document.querySelector("#editarOrdenServicio #idModaltipoMoneda");
    let listaServicios = [];
    let idOrdenServicio = null;
    const cbCotizaciones = document.querySelector("#idCotizacionServicio");
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
    })

}
window.addEventListener("DOMContentLoaded",loadPage);