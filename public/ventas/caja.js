function loadPage() {
    let general = new General();
    const btnAbrirCaja = document.querySelector("#btnAbrirCaja");
    if(btnAbrirCaja){
        btnAbrirCaja.addEventListener("click",async function(e){
            try {
                const response = await general.funcfetch("abrir",null,"POST");
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                alertify.alert("Mensaje",response.success,() => {
                    window.location.reload();
                });
            } catch (error) {
                console.error(error);
                alertify.error("error al abrir caja");
            }
        });
    }
    
    const btnCerrarCaja = document.querySelector("#btnCerrarCaja");
    if(btnCerrarCaja){
        btnCerrarCaja.addEventListener("click",async function(e){
            try {
                const response = await general.funcfetch("cerrar",null,"POST");
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                if (response.error) {
                    return alertify.alert("Error",response.error);
                }
                alertify.alert("Mensaje",response.success,() => {
                    window.location.reload();
                });
            } catch (error) {
                console.error(error);
                alertify.error("error al cerrar caja");
            }
        });
    }
}
window.addEventListener("DOMContentLoaded",loadPage);