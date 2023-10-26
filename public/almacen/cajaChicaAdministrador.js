function loadPage(){
    let gen = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",gen.switchs);
    }
    const tablaCajaChica = document.querySelector("#tablaCajaChica");
    const dataTablaCajaChica = $(tablaCajaChica).DataTable({
        ajax: {
            url: 'listar',
            method: 'GET',
            headers: gen.requestJson
        },
        columns: [
        {
            data: 'nroCaja'
        },
        {
            data: 'fechaInicio'
        },
        {
            data: 'fechaFin'
        },
        {
            data: 'monto_abonado',
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipo_moneda);
            }
        },
        {
            data: 'monto_gastado',
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipo_moneda);
            }
        },
        {
            data: 'montoRestante',
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipo_moneda);
            }
        },
        {
            data : 'estado',
            render : function(data){
                if(data === 1){
                    return '<span class="badge badge-success">ABIERTO</span>';
                }else{
                    return '<span class="badge badge-danger">CERRADO</span>';
                }
            }
        },
        {
            data: 'id',
            render : function(data){
                return `
                <div class="d-flex justify-content-center" style="gap:5px;">
                    <button class="btn btn-sm btn-outline-info p-1" data-caja="${data}">
                        <small>
                        <i class="fas fa-pencil-alt"></i>
                        <span>Editar</span>
                        </small>
                    </button>
                    <a target="_blank" class="btn btn-sm btn-outline-warning p-1" href="gastos/modificar/${data}">
                        <small>
                        <i class="fas fa-pencil-alt"></i>
                        <span>Editar gastos</span>
                        </small>
                    </a>
                    <button class="btn btn-sm btn-outline-success p-1" data-caja="${data}">
                        <small>
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Aumentar</span>
                        </small>
                    </button>

                    <button class="btn btn-sm btn-outline-danger p-1" data-caja="${data}">
                        <small>    
                            <i class="fas fa-trash-alt"></i>
                            <span>Eliminar</span>
                        </small>
                    </button>
                </div>`
            }
        },
        ]
    });
    let idCajaChica = null;
    const contenidoAumento = document.querySelector("#contenidoCajaChicaAumento");
    function visualizarAumento({id,fecha_deposito,banco,nro_operacion,monto_abonado}) {
        const div = document.createElement("div");
        div.className = "form-group border p-2 form-row";
        div.innerHTML = `
        <input name="id_aumento[]" value="${id}" type="hidden">
        <div class="form-group text-center col-12">
            <button type="button" class="btn btn-sm btn-light" data-aumento="${id}">
                <i class="fas fa-trash-alt"></i>
                <span>Eliminar aumento</span>
            </button>
        </div>
        <div class="form-group col-12 col-md-6">
            <label for="idModalAumentofecha_deposito${id}">Fecha Depósito</label>
            <input type="date" value="${fecha_deposito}" id="idModalAumentofecha_deposito${id}" class="form-control" required name="fecha_deposito[]">
        </div>
        <div class="col-12 form-group col-md-6 ocultar-costo">
            <label for="idModalAumentobanco">Banco</label>
            <select name="banco[]" id="idModalAumentobanco" required class="form-control">
                <option value="BCP" ${banco == 'BCP' ? 'selected' : '' }>BCP</option>
                <option value="BBVA" ${banco == 'BBVA' ? 'selected' : '' }>BBVA</option>
                <option value="INTERBANK" ${banco == 'INTERBANK' ? 'selected' : '' }>INTERBANK</option>
                <option value="SCOTIABANK" ${banco == 'SCOTIABANK' ? 'selected' : '' }>SCOTIABANK</option>
                <option value="OTRO" ${banco == 'OTRO' ? 'selected' : '' }>OTRO</option>
            </select>
        </div>
        <div class="col-12 form-group col-md-6 ocultar-costo">
            <label for="idModalAumentonro_operacion${id}">N° Operación</label>
            <input type="text" value="${nro_operacion}" id="idModalAumentonro_operacion${id}" class="form-control" name="nro_operacion[]">
        </div>
        <div class="col-12 col-md-6 form-group">
            <label for="idModalmonto_abonado">Monto abonado</label>
            <input type="number" value="${monto_abonado}" step="0.01" min="1" id="idModalmonto_abonado" class="form-control" required name="monto_abonado[]">
        </div>
        `
        return div;
    }
    document.querySelector("#btnAgregarAumento").onclick = e => {
        e.preventDefault();
        alertify.confirm("Alerta","¿Desea agregar un nuevo aumento?",async () => {
            if(!idCajaChica){
                return alertify.error("no se encontro el id de la caja chica");
            }
            try {
                let datos = new FormData();
                datos.append('id_caja_chica',idCajaChica);
                const response = await gen.funcfetch("agregar-aumento",datos,"POST");
                if(response.session){
                    return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
                }
                if(response.success){
                    alertify.success(response.success);
                    contenidoAumento.append(visualizarAumento(response.aumento));
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al agregar el aumento");
            }
        },()=>{})
    }
    contenidoAumento.onclick = e => {
        if(e.target.classList.contains("btn-light")){
            alertify.confirm("Alerta","¿Desea eliminar este aumento de forma permanente?",async () => {
                try {
                    const response = await gen.funcfetch("eliminar-aumento/" + e.target.dataset.aumento + "/" + idCajaChica ,null,"DELETE");
                    if(response.session){
                        return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.success){
                        alertify.success(response.success);
                        e.target.parentElement.parentElement.remove();
                        if(!contenidoAumento.children.length){
                            contenidoAumento.innerHTML = `<h5 class="text-center text-danger">No se encontraron aumentos disponibles</h5>`
                        }
                    }
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar el aumento");
                }
            },()=>{})
        }
    }
    function ocultarMontos(valor) {
        for (const ocultar of document.querySelectorAll(".ocultar-costo")) {
            ocultar.querySelector("select, input").disabled = valor;
            ocultar.hidden = valor;
        }
        document.querySelector("#idModalmonto_abonado").disabled = valor;
    }
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    const btnModalSaveAumento = document.querySelector("#btnGuardarFrmAumento");
    btnModalSaveAumento.onclick = e => document.querySelector("#btnFrmEnviarAumento").click();
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    const formCajaChica = document.querySelector("#formCajaChica");
    const formCajaChicaAumento = document.querySelector("#formCajaChicaAumento");
    formCajaChicaAumento.addEventListener("submit",async function(e){
        e.preventDefault();
        if(!idCajaChica){
            return alertify.error("no se encontro el id de la caja chica");
        }
        let datos = new FormData(this);
        datos.append("_method","PUT");
        try {
            gen.cargandoPeticion(btnModalSaveAumento, gen.claseSpinner, true);
            const response = await gen.funcfetch("modificar-aumentos/" + idCajaChica,datos,"POST");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            alertify.success(response.success);
            dataTablaCajaChica.draw();
            $('#agregarCajaAumento').modal("hide");
        } catch (error) {
            console.error(error);
            alertify.error("error al modificar aumentos en la caja chica");
        }finally{
            gen.cargandoPeticion(btnModalSaveAumento, 'fas fa-save', false);
        }
    });
    formCajaChica.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            gen.cargandoPeticion(btnModalSave, gen.claseSpinner, true);
            const response = await gen.funcfetch(idCajaChica ? "editar/" + idCajaChica : "crear",datos);
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            alertify.success(response.success);
            dataTablaCajaChica.draw();
            $('#agregarCaja').modal("hide");
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar caja chica");
        }finally{
            gen.cargandoPeticion(btnModalSave, 'fas fa-save', false);
        }
    });
    const switchEstado = document.querySelector("#idModalestado");
    const modalTitulo = document.querySelector("#tituloCajaChica");
    tablaCajaChica.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("listar/" + e.target.dataset.caja,null,"GET");
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                ocultarMontos(true);
                modalTitulo.textContent = "Editar caja chica";
                idCajaChica = response.id;
                for (const key in response) {
                    if (Object.hasOwnProperty.call(response, key)) {
                        const valor = response[key];
                        const dom = document.querySelector("#idModal" + key);
                        if (key == "estado"){
                            const label = switchEstado.parentElement.querySelector("label");
                            label.textContent = valor === 1 ? switchEstado.dataset.selected : switchEstado.dataset.noselected;
                            switchEstado.checked = valor === 1 ? true : false;
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#agregarCaja').modal("show");
            } catch (error) {
                ocultarMontos(false);
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                console.error(error);
                alertify.error("error al obtener la caja chica");
            }
        }
        if (e.target.classList.contains("btn-outline-success")){
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("listar-aumentos/" + e.target.dataset.caja,null,"GET");
                gen.cargandoPeticion(e.target, 'fas fa-money-bill-wave', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                idCajaChica = e.target.dataset.caja;
                if(response.aumentos && !response.aumentos.length){
                    contenidoAumento.innerHTML = `<h5 class="text-center text-danger">No se encontraron aumentos disponibles</h5>`
                    return false;
                }
                response.aumentos.forEach(aumento => {
                    contenidoAumento.append(visualizarAumento(aumento));
                });
                $('#agregarCajaAumento').modal("show");
            } catch (error) {
                ocultarMontos(false);
                gen.cargandoPeticion(e.target, 'fas fa-money-bill-wave', false);
                console.error(error);
                alertify.error("error al obtener la caja chica");
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Estás seguro de eliminar este caja chica?",async ()=>{
                try {
                    gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                    const response = await gen.funcfetch("eliminar/" + e.target.dataset.caja, null,"DELETE");
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    dataTablaCajaChica.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    console.error(error);
                    alertify.error("error al eliminar la caja chica");
                }
            },()=>{});
        }
    })
    $('#agregarCajaAumento').on("hidden.bs.modal",function(e){
        idCajaChica = null;
        contenidoAumento.innerHTML = "";
    });
    $('#agregarCaja').on("hidden.bs.modal",function(e){
        ocultarMontos(false);
        idCajaChica = null;
        modalTitulo.textContent = "Agregar caja chica";
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "ABIERTO";
        formCajaChica.reset();
    });
}
window.addEventListener("DOMContentLoaded",loadPage);