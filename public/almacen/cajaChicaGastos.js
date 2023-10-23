function loadPage(){
    let gen = new General();
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    const formCajaChica = document.querySelector("#formCajaChica");
    formCajaChica.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            gen.cargandoPeticion(btnModalSave, gen.claseSpinner, true);
            const response = await gen.funcfetch("gastos/agregar",datos);
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            alertify.alert("Mensaje",response.success,() => {
                $('#agragarGastos').modal("hide");
                window.location.reload();
            });
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar gastos a la caja chica");
        }finally{
            gen.cargandoPeticion(btnModalSave, 'fas fa-save', false);
        }
    });
    document.querySelector("#idModalmonto_total").oninput = e => {
        let valor = Number.parseFloat(e.target.value);
        if(isNaN(valor)){
            valor = 0;
        }
        document.querySelector("#idModaligv").value = (valor * 0.18).toFixed(2);
    }
    $('#agragarGastos').on("hidden.bs.modal",function(e){
        formCajaChica.reset();
        $('#agregarCaja .select2-simple').trigger("change");
    });
}
window.addEventListener("DOMContentLoaded",loadPage);