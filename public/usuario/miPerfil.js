function loadPage(){
    let general = new General();
    const imgAvatar = document.querySelector("#file-avatar");
    const $prevAvatar = document.querySelector("#previewAvatar");
    document.querySelector("#btnCargarAvatar").onclick = e => imgAvatar.click();
    imgAvatar.addEventListener("change",function(e){
        const reader = new FileReader();
        reader.onload = function(r){
            $prevAvatar.src = reader.result;
        }
        reader.readAsDataURL(e.target.files[0])
    });
    const fileCargarFirma = document.querySelector("#fileFirma");
    document.querySelector("#btnSubirImagen").onclick = e => fileCargarFirma.click();
    const imagenFirma = document.getElementById('imgPrevioFirma');
    fileCargarFirma.addEventListener("change",cargarFirma)
    function cargarFirma() {
        if (this.files.length > 0) {
            const file = this.files[0];
            const fileURL = URL.createObjectURL(file);
            imagenFirma.src = fileURL;
            alertify.success("Firma cargada correctamente, para guardarlo precione el botón actualizar");
            return true;
        }
        imagenFirma.src = '/img/imgprevproduc.png';
    }
    document.querySelector("#btnEliminarImagen").addEventListener("click",e => {
        e.preventDefault();
        alertify.confirm("Alerta","¿Deseas eliminar esta firma de forma permanente?",async ()=>{
            try {
                general.cargandoPeticion(e.target,general.claseSpinner,true);
                const response = await general.funcfetch("miperfil/eliminar-firma",null,"POST");
                if(response.success){
                    imagenFirma.src = '/img/imgprevproduc.png';
                    return alertify.success(response.success);
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al eliminar la firma, porfavor intentelo más tarde");
            }finally{
                general.cargandoPeticion(e.target,'fas fa-trash-alt',false);
            }
        },()=>{})
    })
    const $btnActualizar = document.querySelector("#btnActualizar");
    document.querySelector("#formUpdatePerfil").addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            general.cargandoPeticion($btnActualizar,general.claseSpinner,true);
            const response = await general.funcfetch("miperfil/actualizar",datos);
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            if(imgAvatar.value){
                window.location.reload();
            }
            alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar el perfil, porfavor intentelo más tarde");
        }finally{
            general.cargandoPeticion($btnActualizar,'far fa-save',false);
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);