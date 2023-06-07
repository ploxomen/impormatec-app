function loadPage() {
    const general = new General();
    const cbCliente = $('#cbCliente');
    const cbContactoCliente = document.querySelector("#cbContactoCliente");
    let nuevoCliente = false;
    cbCliente.on("select2:select",async function(e){
        let datos = new FormData();
        datos.append("acciones","obtener-cliente");
        datos.append("cliente",$(this).val());
        habilitarTextoClienteNuevo(true);
        try {
            const response = await general.funcfetch("acciones",datos, "POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            $(cbContactoCliente).select2("destroy");
            let opcionesClientes = {
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Seleccione los contactos',
                tags: false
            }
            if(response.cliente && Object.keys(response.cliente).length){
                nuevoCliente = false;
                for (const key in response.cliente) {
                    if (Object.hasOwnProperty.call(response.cliente, key)) {
                        const valor = response.cliente[key];
                        const dom = document.querySelector("#idModal" + key);
                        if(key == "contactos"){
                            let template = "<option></option>";
                            valor.forEach(c => {
                                template += `<option value="${c.id}">${c.nombreContacto} - ${c.numeroContacto}</option>`;
                            });
                            cbContactoCliente.innerHTML = template;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                        $('.select2-simple').trigger("change");
                    }
                }
            }else if(response.cliente && !Object.keys(response.cliente).length){
                nuevoCliente = true;
                opcionesClientes.tags = true;
                habilitarTextoClienteNuevo(false);
                limpiarFormularioCliente();
            }
            $(cbContactoCliente).select2(opcionesClientes);
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener la informacion del cliente");
        }
    });
    function limpiarFormularioCliente(){
        for (const txt of document.querySelectorAll(".limpiar-frm")) {
            txt.value = "";
        }
        cbContactoCliente.innerHTML = "<option></option>";
        $('.select2-simple').trigger("change");
    }
    function habilitarTextoClienteNuevo(condicion){
        for (const txt of document.querySelectorAll(".text-muted")) {
            txt.hidden = condicion;
        }
    }
    const cbTecnico = $("#cbTecnicoResponsable");
    const cbTecnicoOtros = $("#cbOtrosTecnicos");
    cbTecnico.on("select2:select",function(e){
        for (const cb of cbTecnicoOtros[0].options) {
            cb.disabled = false;
        }
        const optionCb = cbTecnicoOtros[0].options[e.target.selectedIndex];
        optionCb.disabled = true;
        if(cbTecnicoOtros[0].selectedIndex == e.target.selectedIndex){
            optionCb.selected = false;
            cbTecnicoOtros.trigger("change");
        }
    });
    cbTecnicoOtros.on("change",function(e){
        const valores = $(this).val();
        for (const cb of cbTecnico[0].querySelectorAll("option")) {
            cb.disabled = valores && valores.indexOf(cb.value) >= 0 ? true : false;
        }
    });
    const btnPreCoti = document.querySelector("#btnAgregarPreCoti");
    document.querySelector("#frmPreCotizacion").addEventListener("submit",async function(e){
        e.preventDefault();
        general.cargandoPeticion(btnPreCoti, general.claseSpinner, true);
        let datos = new FormData(this);
        datos.append("acciones","agregar-precotizacion");
        datos.append("nuevo",nuevoCliente);
        try {
            const response = await general.funcfetch("acciones",datos, "POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.success){
                alertify.alert("Mensaje",response.success,()=>{
                    window.location.reload();
                })
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al generar una pre - cotización");
        }finally{
            general.cargandoPeticion(btnPreCoti, 'fas fa-plus', false);
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);