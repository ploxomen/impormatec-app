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
    
}
window.addEventListener("DOMContentLoaded", loadPage);
