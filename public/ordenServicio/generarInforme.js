function loadPage() {
    let general = new General();
    let ordenServicio = new OrdenServicio();
    let $cbClientes = document.querySelector("#cbClientes");
    let $cbOrdenServicio = document.querySelector("#cbOrdenServicio");
    $($cbClientes).on("select2:select", async function(e){
        $cbOrdenServicio.innerHTML = "";
        const valor = $(this).val();
        try {
            const response = await general.funcfetch("cliente/" + valor , null, "GET");
            if (response.session) {
                return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
            }
            if(response.ordenesServicio && !response.ordenesServicio.length){
                return alertify.alert("Alerta","No se encontrar ordenes de servicio que requieran generar un nuevo informe");
            }
            $cbOrdenServicio.innerHTML = ordenServicio.obtenerOrdenServicio(response.ordenesServicio);
            return alertify.success("ordenes servicios listadas correctamente");
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener las ordenes de servicio");
        }
    });
    for (const editor of document.querySelectorAll("#contenidoInformes .informe")) {
        tinymce.init({
            selector: `#contenidoInformes #${editor.id}`,
            language: 'es',
            plugins: 'anchor autolink charmap codesample emoticons link lists searchreplace visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            image_title: true,
            branding: false,
            height: editor.dataset.height || "400px",
            automatic_uploads: true,
            file_picker_types: 'image',
            setup: (editor) => {
                editor.on("blur", async (e) => {
                    let datos = new FormData();
                    const {os,servicio,columna} = editor.targetElm.dataset;
                    datos.append("columna",columna);
                    datos.append("servicio",servicio);
                    datos.append("os",os);
                    datos.append("texto",editor.getContent());
                    const response = await general.funcfetch("servicios/actualizar",datos,"POST");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                });
            },
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
    }
    async function cargarImagen(e) {
        if(!this.files.length){
            return
        }
        const imagen = this.files[0];
        const pattern = /image-*/;
        let datos = new FormData();   
        if(!imagen.type.match(pattern)){
            return alertify.alert("Mensaje", "El archivo " + imagen.name +" no es una imagen");
        }
        datos.append("os",$dom.dataset.os);
        datos.append("servicio",$dom.dataset.servicio);
        datos.append("seccion",$dom.dataset.seccion);
        datos.append("imagen",imagen);
        let response = await general.funcfetch("seccion/imagen/agregar",datos,"POST");
        if(response.session){
            return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
        }
        if(response.alerta){
            return alertify.alert("Alerta",response.alerta);
        }
    }
    for (const imgFile of document.querySelectorAll("#contenidoInformes input[type='file']")) {
        imgFile.addEventListener("change",cargarImagen)
    }
    const $contenidoInformes = document.querySelector("#contenidoInformes");
    const $tituloSeccion = document.querySelector("#agregarSeccion #tituloSeccion");
    let datosSecciones = {
        os : null,
        servicio : null,
        contenido : null,
        idSeccion : null
    }
    $contenidoInformes.addEventListener("click",async function(e){
        $dom = e.target;
        if($dom.classList.contains("agregar-seccion")){
            datosSecciones.idSeccion = null;
            datosSecciones.os = $dom.dataset.os;
            datosSecciones.servicio = $dom.dataset.servicio;
            datosSecciones.contenido = $dom.dataset.contenido;
            $("#agregarSeccion").modal("show");
        }
        if($dom.classList.contains("agregar-imagen")){
            document.querySelector($dom.dataset.file).click();
        }
        if($dom.classList.contains("editar-seccion")){
            try {
                console.log($dom);
                let datos = new FormData();
                datos.append("os",$dom.dataset.os);
                datos.append("servicio",$dom.dataset.servicio);
                datos.append("seccion",$dom.dataset.seccion);
                const response = await general.funcfetch("seccion/obtener",datos,"POST");
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
                datosSecciones.os = $dom.dataset.os;
                datosSecciones.servicio = $dom.dataset.servicio;
                datosSecciones.idSeccion = response.success.id;
                $tituloSeccion.textContent = "Editar sección";
                $("#agregarSeccion").modal("show");
            } catch (error) {
                console.error(error);
                alertify.error("error al generar una nueva seccion")
            }
        }
        if($dom.classList.contains("eliminar-seccion")){
            alertify.confirm("Mensaje","Al continuar se eliminará la sección y las imágenes relacionadas a ella. <br> ¿Desea continuar de todas formas?",async () => {
                datosSecciones.os = $dom.dataset.os;
                datosSecciones.servicio = $dom.dataset.servicio;
                datosSecciones.idSeccion = $dom.dataset.seccion;
                let datos = new FormData();
                datos.append("os",datosSecciones.os);
                datos.append("servicio",datosSecciones.servicio);
                datos.append("seccion",datosSecciones.idSeccion);
                const response = await general.funcfetch("seccion/eliminar",datos,"POST");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.alerta){
                    return alertify.alert("Alerta",response.alerta);
                }
                $contenedor = $dom.parentElement.parentElement.parentElement;
                $contenedor.remove();
                $contenedorSecciones = Array.from(document.querySelector(`#contenidoSeccionServicio${datosSecciones.servicio}`).children);
                if($contenedorSecciones.length === 0){
                    document.querySelector(`#contenidoSeccionServicio${datosSecciones.servicio}`).innerHTML = `
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
            },()=>{})
            
            
        }
    });
    const $frmSeccion = document.querySelector("#agregarSeccion #frmSeccionNueva");
    console.log($frmSeccion);
    $frmSeccion.addEventListener("submit",async function(e){
        e.preventDefault();
        try {
            let datos = new FormData(this);
            datos.append("os",datosSecciones.os);
            datos.append("servicio",datosSecciones.servicio);
            if(datosSecciones.idSeccion){
                datos.append("seccion",datosSecciones.idSeccion);
            }
            console.log(datosSecciones);
            const response = await general.funcfetch(!datosSecciones.idSeccion ? "seccion/agregar" : "seccion/editar" ,datos,"POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(!datosSecciones.idSeccion){
                const $secciones = document.querySelector("#contenidoInformes " + datosSecciones.contenido);
                if($secciones.querySelector(".contenido-vacio")){
                    $secciones.querySelector(".contenido-vacio").remove();
                }
                response.index = $secciones.children.length + 1;
                $contenidoNuevo = generarDomSeccionServicio(response);
                $secciones.append($contenidoNuevo);
                for (const tol of $contenidoNuevo.querySelectorAll('[data-toggle="tooltip"]')) {
                    $(tol).tooltip();
                }
            }else{
                document.querySelector(`#contenidoInformes #servicio${response.idServicio}Seccion${response.idSeccion}`).value = response.titulo;
                document.querySelector(`#contenidoInformes #servicio${response.idServicio}Seccion${response.idSeccion}Columna`).textContent = response.columna;
            }
            $("#agregarSeccion").modal("hide");
            return alertify.success(response.success);

        } catch (error) {
            console.error(error);
            alertify.error("error al generar una nueva seccion")
        }
    })
    document.querySelector("#btnGuardarFrmSeccion").onclick = e => document.querySelector("#btnSeccionAgregar").click();
    $('#agregarSeccion').on("hidden.bs.modal",function(e){
        datosSecciones.os = null;
        datosSecciones.servicio = null;
        datosSecciones.contenido = null;
        datosSecciones.idSeccion = null;
        $tituloSeccion.textContent = "Agregar una sección";
        $frmSeccion.reset();
    });
    function generarDomSeccionImg({idImagen,idSeccion,idOs,idServicio,urlImagen,descripcion}) {
        const div = document.createElement("div");
        div.className = "col-12 col-lg-6 form-group contenido-img";
        div.innerHTML = `
        <div class="form-group">
            <img src="${urlImagen}" alt="Imagen ${descripcion}" class="img-guias">
        </div>
        <textarea class="form-control form-control-sm" rows="2">${descripcion}</textarea>
        <button class="btn btn-sm btn-danger" data-servicio="${idServicio}" data-os="${idOs}" data-seccion="${idSeccion}" data-img="${idImagen}" type="button">
            <i class="fas fa-trash-alt"></i>
        </button>
        `
        return div;
    }
    function generarDomSeccionServicio({index,idSeccion,idOs,idServicio,titulo,columna,listaImagenes}) {
        const div = document.createElement("div");
        let templateImagenes = "";
        listaImagenes.forEach(img => {
            templateImagenes += generarDomSeccionImg(img).outerHTML;
        });
        if(templateImagenes !== ""){
            templateImagenes = `<div class="form-group row">${templateImagenes}</div>`;  
        }else{
            templateImagenes = `
            <div class="text-center contenido-vacio">
                <span>No se agregaron imágenes para esta sección</span>
            </div>
            `
        }
        div.className = "form-group p-2";
        div.innerHTML = `
        <div class="justify-content-between align-items-center d-flex" style="gap: 5px;">
            <h6 class="mb-0 nombre-seccion">
                <i class="fas fa-caret-right"></i>
                Sección N° ${index}
            </h6>
            <div class="align-items-center d-flex" style="gap: 5px;">
                <button data-toggle="tooltip" data-placement="top" title="Número de columnas" class="btn btn-sm btn-light" id="servicio${idServicio}Seccion${idSeccion}Columna">
                    <i class="fas fa-columns"></i>
                    <span>${columna}</span>
                </button>
                <button class="btn btn-sm editar-seccion btn-info" data-toggle="tooltip" data-placement="top" title="Editar sección" data-servicio="${idServicio}" data-os="${idOs}" data-seccion="${idSeccion}" type="button">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button class="btn btn-sm eliminar-seccion btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar sección" data-servicio="${idServicio}" data-os="${idOs}" data-seccion="${idSeccion}" type="button">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
        <div class="form-group">
            <label for="servicio${idServicio}Seccion${idSeccion}">Título de la sección</label>
            <input required readonly id="servicio${idServicio}Seccion${idSeccion}" type="text" class="form-control form-control-sm" value="${titulo}">
        </div>
        <div class="form-group">
            <div class="d-flex justify-content-between flex-wrap" style="gap:5px;">
                <h6 class="text-primary mb-0">
                    <i class="fas fa-caret-right"></i>
                    Imágenes de la sección
                </h6>
                <input type="file" name="imagenSeccion" accept="image/*" id="imagenServicio${idServicio}Seccion${idSeccion}" data-servicio="${idServicio}" data-os="${idOs}" data-seccion="${idSeccion}" data-contenido="#contenidoImagenes${idServicio}Seccion${idSeccion}">
                <button data-toggle="tooltip" data-file="#imagenServicio${idServicio}Seccion${idSeccion}" data-placement="top" title="Agregar una imagen" class="btn btn-sm btn-light" type="button">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div id="contenidoImagenes${idServicio}Seccion${idSeccion}">
        ${templateImagenes}
        </div>
        `
        return div;
    }
    $('[data-toggle="tooltip"]').tooltip();
}
window.addEventListener("DOMContentLoaded", loadPage);
