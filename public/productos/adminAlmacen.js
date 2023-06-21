function loadPage(){
    const general = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",general.switchs);
    }
    const tablaAlmacen = document.querySelector("#tablaAlmacen");
    const tablaAlmacenDataTable = $(tablaAlmacen).DataTable({
        ajax: {
            url: 'almacenes/listar',
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
            data: 'nombre'
        },
        {
            data: 'descripcion'
        }
        ,
        {
            data: 'direccion'
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
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-almacen="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-almacen="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    const formAlmacen = document.querySelector("#formAlmacen");
    const btnGuardarForm = document.querySelector("#btnGuardarFrm");
    const switchEstado = document.querySelector("#idModalestado");
    const modalTitulo = document.querySelector("#tituloAlmacen");
    let idAlmacen = null;
    btnGuardarForm.onclick = e => document.querySelector("#btnFrmEnviar").click();
    tablaAlmacen.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("almacenes/listar/" + e.target.dataset.almacen,null, "GET");
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if(response.session){
                    return alertify.alert([...alertaSesion],() => {window.location.reload()});
                }
                modalTitulo.textContent = "Editar Almacen";
                idAlmacen = e.target.dataset.almacen;
                for (const key in response.success) {
                    if (Object.hasOwnProperty.call(response.success, key)) {
                        const valor = response.success[key];
                        const dom = document.querySelector("#idModal" + key);
                        if (key == "estado"){
                            switchEstado.parentElement.querySelector("label").textContent = valor === 1 ? "VIGENTE" : "DESCONTINUADO";
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
                $('#agregarAlmacen').modal("show");
            } catch (error) {
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                idAlmacen = null;
                console.error(error);
                alertify.error("error al obtener el almacen")
            }

        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Deseas eliminar este almacen?",async () => {
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("almacenes/eliminar/" + e.target.dataset.almacen,null,"DELETE");
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
                    tablaAlmacenDataTable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    console.error(error);
                    alertify.error('error al eliminar el almacen');
                }
            },() => {})
        }
    }
    formAlmacen.onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        const url = idAlmacen != null ? "almacenes/editar/" + idAlmacen : 'almacenes/crear';
        try {
            general.cargandoPeticion(btnGuardarForm, general.claseSpinner, true);
            const response = await general.funcfetch(url, datos, "POST");
            general.cargandoPeticion(btnGuardarForm, 'fas fa-save', false);
            if (response.session) {
                return alertify.alert([...alertaSesion], () => { window.location.reload() });
            }
            if (response.success) {
                alertify.success(response.success);
                tablaAlmacenDataTable.draw();
                formAlmacen.reset();
                idAlmacen = null;
                $('#agregarAlmacen').modal("hide");
            }
        } catch (error) {
            idAlmacen = null;
            console.error(error);
            alertify.error(idAlmacen != null ? "error al editar el almacen" : 'error al agregar el almacen')
        }
    }
    $('#agregarAlmacen').on("hidden.bs.modal",function(e){
        idAlmacen = null;
        modalTitulo.textContent = "Agregar Almacen";
        switchEstado.disabled = true;
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        formAlmacen.reset();
    });
}
window.addEventListener("DOMContentLoaded",loadPage);