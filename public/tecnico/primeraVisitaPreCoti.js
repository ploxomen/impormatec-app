function loadPage() {
    const general = new General();
    const preCotizacion = new PreCotizacion();
    const txtFechaVisita = document.querySelector("#txtFecha");
    const contenidoVisitas = document.querySelector("#contenidoVisitas");
    const boxNoVisitas = document.querySelector("#contenidoNoVisitas");
    txtFechaVisita.onchange = function(e){
        if(this.value){
            cargarVisitas();
        }
    }
    const documentoFormatoVisita = document.querySelector("#documentoVisitas");
    const btnEliminarDocumento = document.querySelector("#btnFormatoVisitasEliminar");
    btnEliminarDocumento.onclick = e => {
        alertify.confirm("Alerta","¿Deseas eliminar el reporte de visita?",async () => {
            try {
                const response = await general.funcfetch(preCotizacion.url + `/eliminar/formato-visita/${idVisita}`,null, "DELETE");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.alerta){
                    return alertify.alert("Alerta",response.alerta);
                }
                documentoFormatoVisita.value = "";
                if(document.querySelector("#documento-mostrar-formato-visita")){
                    document.querySelector("#documento-mostrar-formato-visita").remove();
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al eliminar el formato de visita");
            }
        },() => {});
    };
    const contenidoFiltro = document.querySelector("#contenidoFiltro");
    async function cargarVisitas() {
        boxNoVisitas.hidden = true;
        general.banerLoader.hidden = false;
        const valorVisita = txtFechaVisita.value;
        let datos = new FormData();
        datos.append("acciones",'ver-visitas');
        datos.append("fecha",valorVisita);
        try {
            const response = await general.funcfetch("acciones",datos, "POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            let template = "";
            if(response.filtros){
                response.filtros.forEach(f => {
                    template += `
                    <div class="contenido-filtro">
                        <button class="btn btn-sm btn-outline-primary px-2 rounded-pill" data-fecha="${f.fechaNormal}">
                            <small class="font-weight-bold">
                            ${f.fecha + ': ' + f.nroVisitas}
                            </small>
                        </button>
                    </div>
                    `
                });
                contenidoFiltro.innerHTML = template == "" ? `<span>No se encontraron fechas de las visitas</span> ` : template;
                template = "";
            }
            if(response.visitas && response.visitas.length){
                response.visitas.forEach(v => {
                    let tempContacto = "";
                    let tempResponsable = "No se asigno responsable";
                    let tempTecnico = "";
                    let btnReporte = "";
                    v.contactos.forEach(c => {
                        tempContacto += `
                        <li>
                            <span>${c.nombreContacto}</span>
                            <a href="tel:${c.numeroContacto}">${c.numeroContacto}</a>
                        </li>
                        `
                    });
                    v.tecnicos.forEach(t => {
                        if(t.responsable && t.activo){
                            btnReporte = `
                            <button class="btn btn-sm btn-primary" title="Realizar informe" data-reporte="${v.id}">
                                <i class="fas fa-paper-plane"></i>
                            </button>`
                        }
                        if(t.responsable){
                            tempResponsable = t.nombres + " " + t.apellidos;
                        }else{
                            tempTecnico += `<li>${t.nombres + " " + t.apellidos}</li>`;
                        }
                    });
                    template += `
                    <div class="col-12 col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-header px-3 py-2 d-flex justify-content-between align-items-center">
                                <span>
                                    N° ${v.nroVisita}
                                </span>
                                ${btnReporte}
                            </div>
                            <div class="card-body px-3 py-2">
                            <div class="form-row">
                                <div class="col-12 mb-1">
                                    <strong><i class="fas fa-user"></i> Cliente:</strong>
                                    <span>${v.nombreCliente}</span>
                                </div>
                                <div class="col-12 mb-1 col-lg-6">
                                    <strong><i class="fas fa-mobile-alt"></i> Celular:</strong>
                                    <a href="tel:${!v.celular ?0:v.celular}">${!v.celular ? '-' : v.celular}</a>
                                </div>
                                <div class="col-12 mb-1 col-lg-6">
                                    <strong><i class="fas fa-phone-alt"></i> Teléfono:</strong>
                                    <a href="tel:${!v.telefono ?0:v.telefono}">${!v.telefono ? '-':v.telefono}</a>
                                </div>
                                <div class="col-12 mb-1">
                                    <strong><i class="fas fa-address-book"></i> Contactos:</strong>
                                    <ul class="ml-4 mb-0">
                                        ${tempContacto == "" ? "<li>No se asignaron contactos</li>" : tempContacto}
                                    </ul>
                                </div>
                                <div class="col-12 mb-1">
                                    <strong><i class="fas fa-calendar-check"></i> Fecha Hr. Visita:</strong>
                                    <span>${v.fechaHrVisita}</span>
                                </div>
                                <div class="col-12 mb-1">
                                    <strong><i class="fas fa-street-view"></i> Dirección:</strong>
                                    <span>${v.direccion}</span>
                                </div>
                                <div class="col-12 mb-1">
                                    <strong><i class="fas fa-user-tie"></i> Técnico responsable:</strong>
                                    <span>${tempResponsable}</span>
                                </div>
                                <div class="col-12 mb-1">
                                    <strong><i class="fas fa-users"></i> Otros técnicos:</strong>
                                    <ul class="ml-4 mb-0">
                                    ${tempTecnico == "" ? "<li>No se asignaron a otros técnicos</li>" : tempTecnico}
                                    </ul>
                                </div>
                                <div class="col-12">
                                    <strong>Descripción adicional:</strong>
                                    <p class="mb-0">
                                        ${!v.detalle ? '-' : v.detalle}
                                    </p>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    
                    `
                });
            }else if(response.visitas && !response.visitas.length){
                boxNoVisitas.hidden = false;
            }
            contenidoVisitas.innerHTML = template;
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener las visitas")
        }finally{
            general.banerLoader.hidden = true;
        }
    }
    for (const fc of document.querySelectorAll('.fechaCambio')) {
        fc.addEventListener("click",modificarFechas);
    }
    function modificarFechas(e) {
        const fechaActual = new Date(txtFecha.value);
        const fechaFinal = e.target.dataset.tipo == "atras" ? fechaActual.getDate() - 1 : fechaActual.getDate() + 1;
        fechaActual.setDate(fechaFinal);
        txtFecha.value = fechaActual.toISOString().split("T")[0];
        cargarVisitas();
    }
    cargarVisitas();
    contenidoFiltro.onclick = function(e){
        if(e.target.dataset.fecha){
            txtFechaVisita.value = e.target.dataset.fecha;
            cargarVisitas();
        }
    }
    let idVisita = null;
    let borrarDatosInformeModal = true;
    contenidoVisitas.onclick = async function(e){
        if(e.target.dataset.reporte){
            idVisita = e.target.dataset.reporte;
            preCotizacion.cargarPreInformeEditar({idVisita,cbServicios,listaServicios,txtNoServicios,btnEliminarDocumento,documentoFormatoVisita});
        }
    }
    preCotizacion.$contenidoSecciones.addEventListener("click",async function(e){
        $dom = e.target;
        if($dom.classList.contains("agregar-imagen")){
            document.querySelector($dom.dataset.file).click();
        }
        if($dom.classList.contains("editar-seccion")){
            try {
                let datos = new FormData();
                datos.append("preCotizacion",idVisita);
                datos.append("seccion",$dom.dataset.seccion);
                const response = await general.funcfetch(preCotizacion.url + "/seccion/obtener",datos,"POST");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.alerta){
                    return alertify.alert("Alerta",response.alerta);
                }
                for (const key in response.success) {
                    if (Object.hasOwnProperty.call(response.success, key)) {
                        const element = response.success[key];
                        const $domValor = document.querySelector("#agregarSeccion #idModalSeccion" + key);
                        if(!$domValor){
                            continue;
                        }
                        $domValor.value = element;
                    }
                }
                idSeccion = response.success.id;
                borrarDatosInformeModal = false;
                $('#modalPrimeraVisita').modal("hide");
                setTimeout(e => {
                    $('#agregarSeccion').modal('show');
                },500);
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener la seccion")
            }
        }
        if($dom.classList.contains("eliminar-img")){
            alertify.confirm("Mensaje","Al continuar se eliminará la imagen. <br> ¿Desea continuar de todas formas?",async () => {
                let datos = new FormData();
                datos.append("preCotizacion",idVisita);
                datos.append("seccion", $dom.dataset.seccion);
                datos.append("img",$dom.dataset.img);
                try {
                    const response = await general.funcfetch(preCotizacion.url + "/seccion/imagen/eliminar",datos,"POST");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                    $contenedor = $dom.parentElement;
                    $contenedor.remove();
                    $contenedorSecciones = document.querySelector(`#idModalContenidoImagenes${$dom.dataset.seccion}`);
                    if($contenedorSecciones.children.length === 0){
                        $contenedorSecciones.innerHTML = `
                        <div class="text-center contenido-vacio-img col-12">
                            <span>No se agregaron imágenes para esta sección</span>
                        </div>
                        `
                    }
                    return alertify.success(response.success);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la imagen")
                }
            },()=>{})
        }
        if($dom.classList.contains("eliminar-seccion")){
            alertify.confirm("Mensaje","Al continuar se eliminará la sección y las imágenes relacionadas a ella. <br> ¿Desea continuar de todas formas?",async () => {
                let datos = new FormData();
                datos.append("preCotizacion",idVisita);
                datos.append("seccion",$dom.dataset.seccion);
                try {
                    const response = await general.funcfetch(preCotizacion.url + "/seccion/eliminar",datos,"POST");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                    $contenedor = $dom.parentElement.parentElement.parentElement;
                    $contenedor.remove();
                    $contenedorSecciones = Array.from((preCotizacion.$contenidoSecciones).children);
                    if($contenedorSecciones.length === 0){
                        preCotizacion.$contenidoSecciones.innerHTML = `
                        <div class="text-center contenido-vacio">
                            <span>No se agregaron secciones</span>
                        </div>
                        `
                    }else{
                        $contenedorSecciones.forEach((seccion,kseccion) => {
                            if(seccion.querySelector(".nombre-seccion")){
                                seccion.querySelector(".nombre-seccion").textContent = `Sección N° ${kseccion + 1}`
                            }
                        })
                    }
                    return alertify.success(response.success);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la seccion")
                }
            },()=>{})
        }
    });
    document.querySelector("#btnAgregarSeccion").onclick = e => {
        borrarDatosInformeModal = false;
        idSeccion = null;
        $('#modalPrimeraVisita').modal("hide");
        setTimeout(e => {
            $('#agregarSeccion').modal('show');
        },500);
    }
    let idSeccion = null;
    const $frmSeccion = document.querySelector("#frmSeccionNueva");
    $frmSeccion.addEventListener("submit",function(e){
        e.preventDefault();
        preCotizacion.agregarEditarSeccion(this,idSeccion,idVisita);
    });
    document.querySelector("#btnGuardarFrmSeccion").onclick = e => document.querySelector("#btnSeccionAgregar").click();
    tinymce.init({
        selector: '#sumernotePreCotizacion',
        language: 'es',
        plugins: 'anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        image_title: true,
        content_style: "body { font-family: andale mono, monospace; }",
        branding: false,
        height: "500px",
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
    }).then(response => {
        tinymce.activeEditor.setContent(general.$contenidoPreInforme);
    });
    const frmServicios = document.querySelector("#contenidoServicios");
    const txtNoServicios = document.querySelector("#txtNoServi");
    const listaServicios = document.querySelector("#contenidoListaServicios");
    const cbServicios = $('#cbServicio');
    cbServicios.on("select2:select",function(e){
        general.seleccionarServicios(cbServicios,listaServicios,txtNoServicios);
    });
    listaServicios.onclick = function(e){
        if(e.target.classList.contains("btn-sm")){
            for (const cb of cbServicios[0].options) {
                if(cb.value == e.target.dataset.valor){
                    cb.disabled = false;
                }
            }
            e.target.parentElement.remove();
            if(!frmServicios.elements.length){
                txtNoServicios.hidden = false;
            }
        }
    }
    const btnSaveModal = document.querySelector("#btnGenerarReporte");
    btnSaveModal.onclick = e => document.querySelector("#btnFrom").click();
    frmServicios.addEventListener("submit",async function(e){
        e.preventDefault();
        const contenido = tinymce.activeEditor.getContent();
        if(!contenido){
            return alertify.error("por favor redacte el informe");
        }
        const data = new FormData(this);
        data.append("acciones","generar-reporte");
        data.append("html",contenido);
        data.append("visita",idVisita);
        if(documentoFormatoVisita.value){
            data.append("formatoVisitaPdf",documentoFormatoVisita.files[0]);
        }
        general.cargandoPeticion(btnSaveModal, general.claseSpinner, true);
        try {
            const response = await general.funcfetch("acciones",data, "POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.success){
                idVisita = null;
                tinymce.activeEditor.setContent(general.$contenidoPreInforme);
                $('#modalPrimeraVisita').modal("hide");
                cargarVisitas();
                return alertify.success(response.success);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al generar el reporte");
        }finally{
            general.cargandoPeticion(btnSaveModal, 'far fa-save', false);
        }
    })
    $('#agregarSeccion').on('hidden.bs.modal', function (event) {
        borrarDatosInformeModal = true;
        setTimeout(e => {
            $('#modalPrimeraVisita').modal('show');
        },500);
    });
    $('#modalPrimeraVisita').on('hidden.bs.modal', function (event) {
        if(!borrarDatosInformeModal){
            return false;
        }
        if(document.querySelector("#documento-mostrar-formato-visita")){
            document.querySelector("#documento-mostrar-formato-visita").remove();
        }
        btnEliminarDocumento.hidden = true;
        preCotizacion.$contenidoSecciones.innerHTML = "";
        txtNoServicios.hidden = false;
        documentoFormatoVisita.value = "";
        if(document.querySelector("#documento-mostrar-formato-visita")){
            document.querySelector("#documento-mostrar-formato-visita").remove();
        }
        for (const c of listaServicios.querySelectorAll(".contenido")) {
            c.remove();
        }
        for (const cb of cbServicios[0].options) {
            cb.disabled = false;
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);