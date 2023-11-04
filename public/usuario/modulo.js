function loadPage(){
    let general = new General();
    let tablaModulo = document.querySelector("#tablaModulo");
    const tablaModuloDatatable = $(tablaModulo).DataTable({
        ajax: {
            url: 'modulo/accion',
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
            data: 'titulo',
            render : function(data,type,row){
                return `<div class="d-flex align-items-center" style="gap: 5px;">
                <span class="material-icons">${row.grupos.icono}</span> 
                <span class="grupo-buscar">${row.grupos.grupo}</span>
                <b class="text-info mx-1"><i class="fas fa-chevron-right"></i></b>
                <span class="material-icons">${row.icono}</span>
                <span class="titulo-buscar">${data}</span>
            </div>`
            }
        },
        {
            data: 'descripcion',
            render : function (data) {
                return !data ? "No definido" : data;
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;">
                <button class="btn btn-sm btn-outline-info p-1" data-modulo="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                </div>`
            }
        },
        ]
    });
    let idModulo = null;
    tablaModulo.querySelector("tbody").addEventListener("click",async function(e){
        if(e.target.classList.contains("btn-outline-info")){
            let data = new FormData();
            data.append("accion","obtenerRoles");
            data.append("modulo",e.target.dataset.modulo);
            general.cargandoPeticion(e.target, general.claseSpinner, true);
            try {
                const response = await general.funcfetch("modulo/accion",data);
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.roles){
                    idModulo = e.target.dataset.modulo;
                    response.roles.forEach(r => {
                        document.querySelector(`#listaRoles [data-rol="${r.rolFk}"]`).checked = true;
                    });
                    $('#modalRol').modal("show");
                }
            } catch (error) {
                idModulo = null;
                console.error(error);
                alertify.error("error al obtener los roles")
            }finally{
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
            }
        }
    })
    $('#modalRol').on("hidden.bs.modal",function(e){
        for (const selcionador of document.querySelectorAll("#listaRoles input")) {
            selcionador.checked = false;
        }
        idModulo = null;
    });
    const btnGuardarRol = document.querySelector("#btnGuardarFrm");
    btnGuardarRol.addEventListener("click",function(e){
        const claseSelecionada = document.querySelectorAll("#listaRoles input:checked");
        if(!claseSelecionada.length){
            return alertify.alert("Mensaje","Debe selecionar al menos un rol");
        }
        let datos = new FormData();
        datos.append("accion","asignarRol");
        datos.append("modulo",idModulo);
        for (const checkbox of claseSelecionada) {
            datos.append("roles[]",checkbox.dataset.rol);
        }
        const msjRol = "<p>Se procederá a actualizar este módulo con un total de <b>" + claseSelecionada.length + " role(s)</b><br>¿Deseas continuar?</p>";
        alertify.confirm("Mensaje",msjRol,async () => {
            general.cargandoPeticion(btnGuardarRol, general.claseSpinner, true);
            try {
                const response = await general.funcfetch("modulo/accion", datos);
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                if (response.success) {
                    alertify.success(response.success);
                    $('#modalRol').modal("hide");
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al actualizar los roles");
            }finally{
                general.cargandoPeticion(btnGuardarRol, 'fas fa-save', false);
            }
        },() => {})
    });
}
window.addEventListener("DOMContentLoaded",loadPage);