function loadPage() {
    const urlCertificado = window.location.href.split('/');
    const idCertificado = urlCertificado[urlCertificado.length - 1];
    let general = new General();
    tinymce.init({
        selector: `#txtDescripcionCertificado`,
        language: 'es',
        plugins: 'anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        image_title: true,
        branding: false,
        height: "500px",
        automatic_uploads: true,
        images_upload_url: window.origin + '/intranet/storage/editor/img-certificado/save',
        file_picker_types: 'image',
        images_upload_handler : (blobInfo, progress) => new Promise(async (resolve, reject) => {
            let datos = new FormData();
            datos.append('file', blobInfo.blob(), blobInfo.filename());
            try {
                const reponse = await general.funcfetch(window.origin + '/intranet/storage/editor/img-certificado/save',datos,"POST");
                resolve(reponse.location);
            } catch (error) {
                reject(error);
            }
        })
    });
    const $frmCertificado = document.querySelector("#formCertificado");
    $frmCertificado.addEventListener("submit",async function(e){
        e.preventDefault();
        try {
            if(!tinymce.activeEditor.getContent()){
                return alertify.alert("Alerta","La descripción no debe estar vacía");
            }
            let datos = new FormData(this);
            datos.append("certificado",idCertificado);
            datos.append("descripcion",tinymce.activeEditor.getContent());
            const response = await general.funcfetch('actualizar',datos,"POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            return alertify.alert("Mensaje",response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar el certificado")
        }
    });
    $('[data-toggle="tooltip"]').tooltip();
    document.querySelector("#visualizarCertificado").addEventListener("click",function(e){
        e.preventDefault();
        window.open('reporte/' + idCertificado,'target');
    })
}
window.addEventListener("DOMContentLoaded", loadPage);