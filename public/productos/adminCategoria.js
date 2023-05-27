function loadPage(){
    const general = new General();
    const tablaCategoria = document.querySelector("#tablaCategoria");
    const tablaCategoriaDataTable = $(tablaCategoria).DataTable({
        ajax: {
            url: 'categoria/listar',
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
            data: 'nombreCategoria'
        },{
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
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-categoria="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-categoria="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    const txtCategoria = document.querySelector("#txtCategoria");
    const formCategoria = document.querySelector("#formCategoria");
    const btnGuardarForm = document.querySelector("#btnGuardarForm");
    const checkEstado = document.querySelector("#customSwitch1");
    let idCategoria = null;
    tablaCategoria.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("categoria/listar/" +e.target.dataset.categoria,null, "GET");
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.success){
                    alertify.success("pendiente para editar");
                    const marca = response.success;
                    txtCategoria.value = marca.nombreCategoria;
                    idCategoria = marca.id;
                    checkEstado.checked = marca.estado;
                    btnGuardarForm.querySelector("span").textContent = "Editar";
                }
            } catch (error) {
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                idCategoria = null;
                console.error(error);
                alertify.error("error al obtener la área")
            }

        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Deseas eliminar esta categoría?",async () => {
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("categoria/eliminar/" + e.target.dataset.categoria,null,"DELETE");
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
                    tablaCategoriaDataTable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    console.error(error);
                    alertify.error('error al eliminar la marca');
                }
            },() => {})
        }
    }
    formCategoria.onreset = function(e){
        btnGuardarForm.querySelector("span").textContent = "Guardar";
        checkEstado.checked = 0;
        idCategoria = null;
    }
    formCategoria.onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        const url = idCategoria != null ? "categoria/editar/" + idCategoria : 'categoria/crear';
        try {
            general.cargandoPeticion(btnGuardarForm, general.claseSpinner, true);
            const response = await general.funcfetch(url, datos, "POST");
            general.cargandoPeticion(btnGuardarForm, 'fas fa-save', false);
            if (response.session) {
                return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
            }
            if (response.success) {
                alertify.success(response.success);
                tablaCategoriaDataTable.draw();
                formCategoria.reset();
                idCategoria = null;
            }
        } catch (error) {
            idCategoria = null;
            console.error(error);
            alertify.error(idCategoria != null ? "error al editar la categoria" : 'error al agregar la categoria')
        }

    }
}
window.addEventListener("DOMContentLoaded",loadPage);