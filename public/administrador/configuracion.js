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
        images_upload_url: window.origin + '/intranet/storage/editor/img-configuracion/save',
        file_picker_types: 'image',
        images_upload_handler : (blobInfo, progress) => new Promise(async (resolve, reject) => {
            let datos = new FormData();
            datos.append('file', blobInfo.blob(), blobInfo.filename());
            try {
                const reponse = await general.funcfetch(window.origin + '/intranet/storage/editor/img-configuracion/save',datos,"POST");
                resolve(reponse.location);
            } catch (error) {
                reject(error);
            }
        })
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