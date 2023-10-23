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
                    return '<span class="badge badge-success">PRINCIAPL</span>';
                }else{
                    return '<span class="badge badge-danger">SECUNDARIO</span>';
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
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    let idCajaChica = null;
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    const formCajaChica = document.querySelector("#formCajaChica");
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
                $('#agregarCaja .select2-simple').trigger("change");
                $('#agregarCaja').modal("show");
            } catch (error) {
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
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
    $('#agregarCaja').on("hidden.bs.modal",function(e){
        idCajaChica = null;
        modalTitulo.textContent = "Agregar caja chica";
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "ABIERTO";
        formCajaChica.reset();
        $('#agregarCaja .select2-simple').trigger("change");
    });
}
window.addEventListener("DOMContentLoaded",loadPage);