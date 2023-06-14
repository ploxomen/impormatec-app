function loadPage() {
    const general = new General();
    const txtFechaVisita = document.querySelector("#txtFecha");
    const contenidoVisitas = document.querySelector("#contenidoVisitas");
    const boxNoVisitas = document.querySelector("#contenidoNoVisitas");
    txtFechaVisita.onchange = function(e){
        if(this.value){
            cargarVisitas();
        }
    }
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
        txtFechaVisita.value = e.target.dataset.fecha;
        cargarVisitas();
    }
    let idVisita = null;
    contenidoVisitas.onclick = function(e){
        if(e.target.dataset.reporte){
            idVisita = e.target.dataset.reporte;
            $('#modalPrimeraVisita').modal("show");
        }
    }
    const btnImg = document.querySelector("#btnImagen");
    const imgCopia = document.querySelector("#imgCopia");
    const imgOriginal = document.querySelector("#imgsOriginal");
    const renderImg = document.querySelector("#renderImg");
    btnImg.onclick = e => imgCopia.click();
    function coinsidenciaImg(files,name){
        for (let i = 0; i < files.length; i++) {
            if(files[i].name == name){
                return 1;
            }
        }
        return 0;
    }
    function renderImagen(file){
        const render = new FileReader();
        render.onload = function(){
            const contenido = document.createElement("div");
            contenido.className = "form-group col-12 col-xl-6 d-flex align-items-center";
            contenido.style = "gap:5px;"
            let img = new Image();
            img.src = this.result;
            img.classList.add('img-guias');
            img.title = file.name;
            let txtDescripcion = document.createElement("textarea");
            txtDescripcion.name = "descripcionImagen[]";
            txtDescripcion.className = "form-control form-control-sm txtdescripcion";
            txtDescripcion.placeholder = "Añadir descripción";
            const btnDelete = document.createElement('button');
            btnDelete.classList.add('img-btn-delete','btn','btn-sm','btn-light');
            btnDelete.dataset.file = file.name;
            btnDelete.innerHTML = `<i class="fas fa-trash-alt"></i>`;
            btnDelete.addEventListener('click',deleteImg);
            contenido.append(img,txtDescripcion,btnDelete);
            renderImg.append(contenido);
        }
        render.readAsDataURL(file);
    }
    function deleteImg(){
        const nameImg = this.dataset.file;
        const newDataTrnasfer = new DataTransfer();
        const filesParent = imgOriginal;
        let deleteDom = false;
        for (let i = 0; i < filesParent.files.length; i++) {
            if(filesParent.files[i].name != nameImg){
                newDataTrnasfer.items.add(filesParent.files[i]);
            }else{
                deleteDom = true;
            }
        }
        filesParent.files = newDataTrnasfer.files;
        if(deleteDom){
            this.parentElement.remove();
            alertify.success('imagen eliminada');
        }
        if(!imgOriginal.files.length){
            renderImg.innerHTML = `
            <div class="form-grop col-12 text-center">
                <span>No se subieron imagenes</span>
            </div>
            `;
        }
    }
    imgCopia.addEventListener("change",function(e){
        if(!this.files.length){
            return
        }
        if(!imgOriginal.files.length){
            renderImg.innerHTML = "";
        }
        let countImgRepetidas = 0;
        const pattern = /image-*/;
        const transfer = new DataTransfer();
        for (let ed = 0; ed < imgOriginal.files.length; ed++) {
            transfer.items.add(imgOriginal.files[ed]);             
        }
        for (let i = 0; i < this.files.length; i++) {
            if(!this.files[i].type.match(pattern)){
                return alertify.alert("Mensaje", "El archivo " + this.files[i].name +" no es una imagen");
            }
            if(coinsidenciaImg(imgOriginal.files,this.files[i].name)){
                countImgRepetidas++;
                continue;
            }
            renderImagen(this.files[i]);
            transfer.items.add(this.files[i]);
        }
        if(countImgRepetidas){
            alertify.alert('Mensaje','Se detectaron ' + countImgRepetidas + ' imagen(es) que ya se cargaron, recuerda que no se perminte imágenes duplicadas.');
        }
        imgOriginal.files = transfer.files;
    })
    // $('#modalPrimeraVisita').modal("show");
    tinymce.init({
        selector: '#sumernotePreCotizacion',
        language: 'es',
        plugins: 'anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        /* enable title field in the Image dialog*/
        image_title: true,
        branding: false,
        height: "500px",
        /* enable automatic uploads of images represented by blob or data URIs*/
        automatic_uploads: true,
        /*
            URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
            images_upload_url: 'postAcceptor.php',
            here we add custom filepicker only to Image dialog
        */
        file_picker_types: 'image',
        /* and here's our custom image picker*/
        file_picker_callback: (cb, value, meta) => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.addEventListener('change', (e) => {
            const file = e.target.files[0];

            const reader = new FileReader();
            reader.addEventListener('load', () => {
                /*
                Note: Now we need to register the blob in TinyMCEs image blob
                registry. In the next release this part hopefully won't be
                necessary, as we are looking to handle it internally.
                */
                const id = 'blobid' + (new Date()).getTime();
                const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                const base64 = reader.result.split(',')[1];
                const blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);

                /* call the callback and populate the Title field with the file name */
                cb(blobInfo.blobUri(), { title: file.name });
            });
            reader.readAsDataURL(file);
            });

            input.click();
        },
    });
    const frmServicios = document.querySelector("#contenidoServicios");
    const txtNoServicios = document.querySelector("#txtNoServi");
    const cbServicios = $('#cbServicio');
    cbServicios.on("select2:select",function(e){
        const cb = $(this)[0];
        const optionCb = cb.options[e.target.selectedIndex];
        const div = document.createElement("div");
        div.className = "contenido rounded-pill bg-light p-2";
        div.innerHTML = `
        <input type="hidden" value="${cbServicios.val()}" name="servicios[]">
        <span>${optionCb.textContent}</span>
        <button type="button" class="btn btn-sm p-1" data-valor="${cbServicios.val()}"><i class="fas fa-trash-alt"></i></button>
        `
        frmServicios.append(div);
        if(frmServicios.elements.length >= 1){
            txtNoServicios.hidden = true;
        }
        optionCb.disabled = true;
        cbServicios.val("").trigger("change");
    });
    frmServicios.onclick = function(e){
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
            alertify.error("por favor redacte el informe");
        }
        const data = new FormData(this);
        data.append("acciones","generar-reporte");
        data.append("html",contenido);
        data.append("visita",idVisita);
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
                tinymce.activeEditor.setContent("");
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
    $('#modalPrimeraVisita').on('hidden.bs.modal', function (event) {
        txtNoServicios.hidden = false;
        for (const c of frmServicios.querySelectorAll(".contenido")) {
            c.remove();
        }
        for (const cb of cbServicios[0].options) {
            cb.disabled = false;
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);