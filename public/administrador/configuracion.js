function loadPage() {
    let general = new General();
    tinymce.init({
        selector: '#sumernoteNumeroCuenta',
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
    const btnActualizarDatos = document.querySelector("#btnSubmitNegocio");
    const frmConfiguracion = document.querySelector("#configuracionMiNegocio");
    frmConfiguracion.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("texto_datos_bancarios",tinymce.activeEditor.getContent());
        try {
            general.cargandoPeticion(btnActualizarDatos, general.claseSpinner, true);
            const response = await general.funcfetch("mi-negocio/actualizar",datos,"POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.success){
                alertify.success(response.success);
            }
        } catch (error) {
            alertify.error("error al actualizar los datos");
        }finally{
            general.cargandoPeticion(btnActualizarDatos, 'fas fa-pencil-alt', false);
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);