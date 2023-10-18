function loadPage() {
    let general = new General();
    let cotizacionGeneral = new Cotizacion();
    const tablaPublicidad = document.querySelector("#tablaPublicidad");
    const dataTablePublicidad = $(tablaPublicidad).DataTable({
        ajax: {
            url: 'obtener',
            method: 'GET',
            headers: general.requestJson,
            data: function (d) {
                // d.acciones = 'obtener';
                // d.area = $("#cbArea").val();
                // d.rol = $("#cbRol").val();
            }
        },
        columns: [
        {
            data: 'nroPublicidad'
        },
        {
            data: 'asunto'
        },
        {
            data: 'fechaHrCreada'
        },
        {
            data: 'fechaHrUltimoEnvio'
        },
        {
            data: 'creadoPor'
        },
        {
            data: 'id',
            render : function(data){
                return `
                <div class="d-flex flex-wrap" style="gap:5px;">
                    <button type="button" class="btn btn-outline-success reenviar btn-sm" data-publicidad="${data}">
                        <small>
                            <i class="fas fa-paper-plane"></i>
                            <span>Reenviar</span>
                        </small>    
                    </button>
                    <button type="button" class="btn btn-outline-info editar btn-sm" data-publicidad="${data}">
                        <small>    
                            <i class="fas fa-pen"></i>
                            <span>Editar</span>
                        </small> 
                    </button>
                    <button type="button" class="btn btn-outline-danger eliminar btn-sm" data-publicidad="${data}">
                        <small>     
                            <i class="fas fa-trash-alt"></i>
                            <span>Eliminar</span>
                        </small> 
                    </button>
                </div>`
            }
        },
        ]
    });
    tinymce.init({
        selector: '#txtCuerpoCorreo',
        language: 'es',
        plugins: 'anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        image_title: true,
        content_style: "body { font-family: andale mono, monospace; }",
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
    const contenedorArchivoPdf = document.querySelector("#contenedorArchivoPdf");
    const radioTodosClientes = document.querySelector("#enviarTodosClientes");
    const tituloModalPublicidad = document.querySelector("#txtTituloPublicidad");
    let idPublicidad = null;
    tablaPublicidad.addEventListener("click",async function(e){
        if(e.target.classList.contains("reenviar")){
            alertify.confirm("Mensaje","¿Deseas reeenviar esta publicidad?",async ()=>{
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                try {
                    const response = await general.funcfetch(`reenviar/${e.target.dataset.publicidad}`,null,"GET");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.error){
                        return alertify.alert("Alerta",response.error);
                    }
                    dataTablePublicidad.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al reenviar la pubicidad");
                }finally{
                    general.cargandoPeticion(e.target, 'fas fa-paper-plane', false);
                }
            },()=>{})
        }
        if(e.target.classList.contains("editar")){
            general.cargandoPeticion(e.target, general.claseSpinner, true);
            try {
                const response = await general.funcfetch(`obtener/${e.target.dataset.publicidad}`,null,"GET");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.error){
                    return alertify.alert("Alerta",response.error);
                }
                idPublicidad = response.id;
                btnGuardarEnvio.querySelector("span").textContent = "Guardar";
                tituloModalPublicidad.textContent = "Editar Publicidad";
                for (const key in response) {
                    if (Object.hasOwnProperty.call(response, key)) {
                        const valor = response[key];
                        const dom = document.querySelector("#txtModal" + key);
                        if(key === "clientes"){
                            $('#idModalid_cliente').val(valor.map(cliente => cliente.id_cliente)).trigger("change");
                            continue;
                        }
                        if(key === "documentos"){
                            valor.forEach(pdf => {
                                cotizacionGeneral.renderPdfCargados({valorDocumento:null,contenedorArchivoPdf,nombreDocumento : pdf.nombre_real_documento,idDocumento : pdf.id});
                            });
                            continue;
                        }
                        if(key === "cuerpo"){
                            tinymce.activeEditor.setContent(!valor ? "" : valor);
                            continue;
                        }
                        if(key === "enviarTodosClientes" && valor === 1){
                            radioTodosClientes.checked = true;
                            $('#idModalid_cliente').prop('disabled',true);
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#agregarPublicidad').modal("show");
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener la pubicidad");
            }finally{
                general.cargandoPeticion(e.target, 'fas fa-pen', false);
            }
        }
        if(e.target.classList.contains("eliminar")){
            alertify.confirm("Mensaje","¿Deseas eliminar esta publicidad?",async ()=>{
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                try {
                    const response = await general.funcfetch(`eliminar/${e.target.dataset.publicidad}`,null,"DELETE");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.error){
                        return alertify.alert("Alerta",response.error);
                    }
                    dataTablePublicidad.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la pubicidad");
                }finally{
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                }
            },()=>{})
        }
    })
    contenedorArchivoPdf.addEventListener("click",function(e){
        if(e.target.classList.contains("btn-sm") && !e.target.dataset.documento){
            e.target.parentElement.remove();
            return alertify.success("archivo eliminado");
        }
        if(e.target.classList.contains("btn-sm") && e.target.dataset.documento){
            alertify.confirm("Mensaje","¿Deseas eliminar este documento de la publicidad?",async ()=>{
                try {
                    const response = await general.funcfetch(`documento/${idPublicidad}/${e.target.dataset.documento}`,null,"DELETE");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.error){
                        return alertify.alert("Alerta",response.error);
                    }
                    e.target.parentElement.remove();
                    return alertify.success(response.success);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar el documento de la pubicidad");
                }
            },()=>{})
        }
    });
    $('#agregarPublicidad').on('hidden.bs.modal', function (event) {
        idPublicidad = null;
        formPublicidad.reset();
        btnGuardarEnvio.querySelector("span").textContent = "Guardar y enviar";
        tinymce.activeEditor.setContent("");
        radioTodosClientes.checked = false;
        contenedorArchivoPdf.innerHTML = "";
        $('#idModalid_cliente').val("").trigger("change");
        $('#idModalid_cliente').prop('disabled',false);
        tituloModalPublicidad.textContent = "Nueva Publicidad";
    });
    radioTodosClientes.onchange = e => {
        if(e.target.checked){
            $('#idModalid_cliente').val("").trigger("change");
        }
        $('#idModalid_cliente').prop('disabled',e.target.checked);
    }
    const btnGuardarEnvio = document.querySelector("#btnGuardarFrm");
    btnGuardarEnvio.onclick = e => document.querySelector("#btnFrmEnviar").click();
    const formPublicidad = document.querySelector("#formAgregarPublicidad");
    formPublicidad.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("cuerpo_publicidad",tinymce.activeEditor.getContent());
        if(idPublicidad){
            datos.append("_method","PUT");
        }
        general.cargandoPeticion(btnGuardarEnvio, general.claseSpinner, true);
        try {
            const response = await general.funcfetch(idPublicidad ? `actualizar/${idPublicidad}` : "guardar",datos);
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Alerta",response.error);
            }
            $('#agregarPublicidad').modal("hide");
            dataTablePublicidad.draw();
            return alertify.alert("Mensaje",response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al generar una pubicidad");
        }finally{
            general.cargandoPeticion(btnGuardarEnvio, 'fas fa-save', false);
        }
    })
    const fileOtrosDocumentos = document.querySelector("#fileOtrosDocumentos");
    document.querySelector("#btnAgregarDocumentos").onclick = e => fileOtrosDocumentos.click();
    fileOtrosDocumentos.addEventListener("change",function(e){
        const files = e.target.files;
        if(!files.length){
            return false
        }
        for (let i = 0; i < files.length; i++) {
            cotizacionGeneral.renderPdfCargados({valorDocumento : files[i],contenedorArchivoPdf, nombreDocumento : files[i].name,idDocumento : null});
        }
    });
}
window.addEventListener("DOMContentLoaded",loadPage);