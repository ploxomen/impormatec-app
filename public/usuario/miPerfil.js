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