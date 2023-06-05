function loadPage(){
    let gen = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",gen.switchs);
    }
    const tablaServicio = document.querySelector("#tablaServicios");
    const tablaServicioDatatable = $(tablaServicio).DataTable({
        ajax: {
            url: 'servicio/listar',
            method: 'POST',
            headers: gen.requestJson
        },
        columns: [
        {
            data: 'nroServicio'
        },
        {
            data: 'servicio'
        },
        {
            data: 'descripcion',
        },
        {
            data : 'estado',
            render : function(data){
                if(data === 1){
                    return '<span class="badge badge-success">Vigente</span>';
                }else if(data === 0){
                    return '<span class="badge badge-danger">Descontinuado</span>';
                }else{
                    return '<span class="text-danget">No establecido</span>';
                }
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-servicio="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-servicio="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    let idServicio = null;
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    const formServicio = document.querySelector("#formServicio");
    formServicio.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            gen.cargandoPeticion(btnModalSave, gen.claseSpinner, true);
            const response = await gen.funcfetch(idServicio ? "servicio/editar/" + idServicio : "servicio/crear",datos);
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            alertify.success(response.success);
            tablaServicioDatatable.draw();
            $('#agregarServicio').modal("hide");
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar un servicio");
        }finally{
            gen.cargandoPeticion(btnModalSave, 'fas fa-save', false);
        }
    });
    const switchEstado = document.querySelector("#idModalestado");
    const modalTitulo = document.querySelector("#tituloServicio");
    $('#agregarservicio').on("hidden.bs.modal",function(e){
        idServicio = null;
        modalTitulo.textContent = "Crear servicio";
        switchEstado.disabled = true;
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        formServicio.reset();
    });
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    tablaServicio.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            btnModalSave.querySelector("span").textContent = "Editar";
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("servicio/listar/" + e.target.dataset.servicio,null,"GET");
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                modalTitulo.textContent = "Editar servicio";
                idServicio = e.target.dataset.servicio;
                for (const key in response.servicio) {
                    if (Object.hasOwnProperty.call(response.servicio, key)) {
                        const valor = response.servicio[key];
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
                switchEstado.disabled = false;
                $('#agregarServicio').modal("show");
            } catch (error) {
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                console.error(error);
                alertify.error("error al obtener el servicio");
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Estás seguro de eliminar este servicio?",async ()=>{
                try {
                    gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                    const response = await gen.funcfetch("servicio/eliminar/" + e.target.dataset.servicio, null,"DELETE");
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    tablaServicioDatatable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    console.error(error);
                    alertify.error("error al eliminar el usuario");
                }
            },()=>{});
            
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);

