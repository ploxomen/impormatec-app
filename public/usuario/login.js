function loadPage(){
    const general = new General();
    const frmLogin = document.querySelector("#frmLogin");
    frmLogin.onsubmit = async function (e) {
        e.preventDefault();
        let datos = new FormData(this);
        try {
            let result = await general.funcfetch('autenticacion', datos);
            if (result.success) {
                alertify.success("iniciando sesión");
                return window.location.reload();
            } else if (result.not_user) {
                alertify.alert('Mensaje', 'El usuario y/o contraseña no son los correctos.');
            }
        } catch (error) {
            alertify.error('error al iniciar sesión');
            console.error(error);
        }
    }
}
window.addEventListener("DOMContentLoaded",loadPage);
