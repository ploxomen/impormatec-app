function loadPage(){
    const general = new General();
    const tablaPresentacion = document.querySelector("#tablaPresentacion");
    const tablaPresentacionDataTable = $(tablaPresentacion).DataTable({
        ajax: {
            url: 'presentacion/listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.accion = 'obtener';
            }
        },
        columns: [{
            data: 'id',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data: 'nombrePresentacion'
        },
        {
            data: 'siglas'
        }
        ,{
            data: 'estado',
            render:function(data){
                return data ? `<span class="badge badge-success">Vigenta</span>` : `<span class="badge badge-danger">Descontinuado</span>`
            }
        },
        // {
        //     data: 'fechaCreada'
        // },
        // {
        //     data: 'fechaActualizada'
        // },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-presentacion="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-presentacion="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    const txtPresentacion = document.querySelector("#txtPresentacion");
    const txtSiglas = document.querySelector("#txtSiglas");
    const formPresentacion = document.querySelector("#formPresentacion");
    const btnGuardarForm = document.querySelector("#btnGuardarForm");
    const checkEstado = document.querySelector("#customSwitch1");
    let idPresentacion = null;
    tablaPresentacion.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("presentacion/listar/" + e.target.dataset.presentacion,null, "GET");
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if(response.session){
                    return alertify.alert([...alertaSesion],() => {window.location.reload()});
                }
                if(response.success){
                    alertify.success("pendiente para editar");
                    const presentacion = response.success;
                    txtPresentacion.value = presentacion.nombrePresentacion;
                    idPresentacion = presentacion.id;
                    txtSiglas.value = presentacion.siglas;
                    checkEstado.checked = presentacion.estado;
                    btnGuardarForm.querySelector("span").textContent = "Editar";
                }
            } catch (error) {
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                idPresentacion = null;
                console.error(error);
                alertify.error("error al obtener la presentación")
            }

        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Deseas eliminar esta presentación?",async () => {
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("presentacion/eliminar/" + e.target.dataset.presentacion,null,"DELETE");
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    if (response.session) {
                        return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                    if (response.error) {
                        return alertify.alert("Alerta", response.error);
                    }
                    tablaPresentacionDataTable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    console.error(error);
                    alertify.error('error al eliminar la presentación');
                }
            },() => {})
        }
    }
    formPresentacion.onreset = function(e){
        btnGuardarForm.querySelector("span").textContent = "Guardar";
        checkEstado.checked = 0;
        idPresentacion = null;
    }
    formPresentacion.onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        const url = idPresentacion != null ? "presentacion/editar/" + idPresentacion : 'presentacion/crear';
        try {
            general.cargandoPeticion(btnGuardarForm, general.claseSpinner, true);
            const response = await general.funcfetch(url, datos, "POST");
            general.cargandoPeticion(btnGuardarForm, 'fas fa-save', false);
            if (response.session) {
                return alertify.alert([...alertaSesion], () => { window.location.reload() });
            }
            if (response.success) {
                alertify.success(response.success);
                tablaPresentacionDataTable.draw();
                formPresentacion.reset();
                idPresentacion = null;
            }
        } catch (error) {
            idPresentacion = null;
            console.error(error);
            alertify.error(idPresentacion != null ? "error al editar la presentación" : 'error al agregar la presentación')
        }

    }
}
window.addEventListener("DOMContentLoaded",loadPage);