function loadPage() {
    const general = new General();
    const frmLogin = document.querySelector("#frmLogin");
    frmLogin.onsubmit = async function (e) {
        e.preventDefault();
        let datos = new FormData(this);
        if (datos.get('password') !== datos.get('password2')) {
            return alertify.alert("Mensaje", "Las contraseñas no coinciden");
        }
        if (datos.get('password').length < 8) {
            return alertify.alert("Mensaje", "La contraseña debe tener al menos 8 caracteres");
        }
        try {
            let result = await general.funcfetch('restaurar', datos);
            if (result.noExistCookie) {
                return window.location.href = "/intranet/login";
            } else if (result.error) {
                alertify.alert('Mensaje', result.error);
            } else if (result.success){
                alertify.alert('Mensaje', result.success, () => {
                    return window.location.href = "/intranet/inicio";
                });
            }
        } catch (error) {
            alertify.error('error al iniciar sesión');
            console.error(error);
        }
    }
}
window.addEventListener("DOMContentLoaded", loadPage);
