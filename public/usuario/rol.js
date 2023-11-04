function loadPage(){
    const general = new General();
    const tablaRol = document.querySelector("#tablaRol");
    const tablaRolDataTable = $(tablaRol).DataTable({
        ajax: {
            url: 'rol/accion',
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
            data: 'nombreRol'
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;">
                <button class="btn btn-sm btn-outline-info p-1" data-rol="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-primary p-1" data-rol="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Modulos
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-rol="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    const txtRol = document.querySelector("#txtRol");
    const formRol = document.querySelector("#formRol");
    const txtInfoSeleccion = document.querySelector("#textoInfoSelecionado");
    const btnGuardarForm = document.querySelector("#btnGuardarForm");
    const btnGuardarRol = document.querySelector("#btnGuardarFrm");
    let idRol = null;
    tablaRol.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            let data = new FormData();
            data.append("accion","mostarEditar");
            data.append("rol",e.target.dataset.rol);
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("rol/accion",data);
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.success){
                    alertify.success("pendiente para editar");
                    const rol = response.success;
                    txtRol.value = rol.nombreRol;
                    idRol = rol.id;
                    btnGuardarForm.querySelector("i").className = "fas fa-pencil-alt";
                    btnGuardarForm.querySelector("span").textContent = "Editar";
                }
            } catch (error) {
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                idRol = null;
                console.error(error);
                alertify.error("error al obtener el rol para editar")
            }
        }
        if (e.target.classList.contains("btn-outline-primary")){
            let data = new FormData();
            data.append("accion","verModulos");
            data.append("rol",e.target.dataset.rol);
            general.cargandoPeticion(e.target, general.claseSpinner, true);
            try {
                const response = await general.funcfetch("rol/accion",data);
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.modulos){
                    idRol = e.target.dataset.rol;
                    response.modulos.forEach(m => {
                        document.querySelector(`#modulosBuscar [data-modulo="${m.moduloFk}"]`).checked = true;
                    });
                    txtInfoSeleccion.textContent = response.modulos.length;
                    btnGuardarRol.children[1].textContent = "Editar";
                    $('#modalRol').modal("show");
                }
            } catch (error) {
                idRol = null;
                console.error(error);
                alertify.error("error al obtener los modulos");
            }finally{
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Deseas eliminar este rol?",async () => {
                let data = new FormData();
                data.append("accion", "eliminar");
                data.append("rol", e.target.dataset.rol);
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("rol/accion",data);
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
                    tablaRolDataTable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', true);
                    console.error(error);
                    alertify.error('error al eliminar el rol');
                }
            },() => {})
        }
    }
    formRol.onreset = function(e){
        btnGuardarForm.querySelector("span").textContent = "Siguiente";
        idRol = null;
    }
    const txtBuscador = document.querySelector("#buscarModulo");
    txtBuscador.addEventListener("input", buscarModulos);
    txtBuscador.addEventListener("search", buscarModulos);
    const tablaModulosBuscar = document.querySelector("#modulosBuscar");
    function buscarModulos(event) {
        event.preventDefault();
        for (const buscar of tablaModulosBuscar.children) {
            const childrens = buscar.querySelectorAll(".grupo-buscar, .titulo-buscar, .descripcion-buscar");
            let bus = -1;
            for (const c of childrens) {
                bus = c.textContent.toLocaleLowerCase().indexOf(event.target.value);
                buscar.classList.toggle("d-none", bus < 0);
                if (bus >= 0){
                    break;
                }
            }
        }
        general.seleccionarCheckbox(txtCheckebox, selecionarTodo);
    }
    const selecionarTodo = document.querySelector("#selecionarTodoCheckbox");
    $('#modalRol').on("hidden.bs.modal",function(e){
        selecionarTodo.checked = false;
        for (const selcionador of document.querySelectorAll(txtCheckebox)) {
            selcionador.parentElement.parentElement.parentElement.classList.remove("d-none");
            selcionador.checked = false;
        }
        txtInfoSeleccion.textContent = 0;
        btnGuardarRol.children[1].textContent = "Guardar";
        idRol = null;
    });
    const txtCheckebox = "#modulosBuscar .custom-control-input";
    for (const selcionador of document.querySelectorAll(txtCheckebox)) {
        selcionador.addEventListener("change", function (e) {
            txtInfoSeleccion.textContent = general.seleccionarCheckbox(txtCheckebox, selecionarTodo);
        });
    }
    btnGuardarRol.addEventListener("click",function(e){
        const claseSelecionada = document.querySelectorAll(txtCheckebox+":checked");
        if(!claseSelecionada.length){
            return alertify.alert("Mensaje","Debe selecionar al menos un módulo");
        }
        let datos = new FormData();
        datos.append("accion",!idRol ? "nuevoRol" : "editarModuloRol");
        datos.append("rol",!idRol ? txtRol.value : idRol);
        for (const checkbox of claseSelecionada) {
            datos.append("modulo[]",checkbox.dataset.modulo);
        }
        const msjRol = !idRol ? "<p>Se procederá a crear el rol <b>" + txtRol.value + "</b> con un total de <b>" + claseSelecionada.length + " modulos</b><br>¿Deseas continuar?</p>" : "¿Estas seguro de actualizar los módulos?";
        alertify.confirm("Mensaje",msjRol,async () => {
            general.cargandoPeticion(btnGuardarRol, general.claseSpinner, true);
            try {
                const response = await general.funcfetch("rol/accion", datos);
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                if (response.success) {
                    alertify.success(response.success);
                    tablaRolDataTable.draw();
                    formRol.reset();
                    $('#modalRol').modal("hide");
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al crear un nuevo rol");
            }finally{
                general.cargandoPeticion(btnGuardarRol, 'fas fa-save', false);
            }
        },() => {})
    });
    selecionarTodo.addEventListener("change",function(e){
        for (const selcionador of document.querySelectorAll(txtCheckebox)) {
            if (selcionador.parentElement.parentElement.parentElement.classList.contains("d-none")){
                continue;
            }
            selcionador.checked = e.target.checked;
        }
        txtInfoSeleccion.textContent = general.seleccionarCheckbox(txtCheckebox, selecionarTodo);
    });
    formRol.onsubmit = async function(e){
        e.preventDefault();
        if(idRol){
            let datos = new FormData(this);
            datos.append("accion","editarRol");
            datos.append("rolId", idRol);
            general.cargandoPeticion(btnGuardarForm, general.claseSpinner, true);
            try {
                const response = await general.funcfetch("rol/accion", datos);
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                if (response.success) {
                    alertify.success(response.success);
                    tablaRolDataTable.draw();
                    formRol.reset();
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al editar el nombre del rol");
            }finally{
                idRol = null;
                general.cargandoPeticion(btnGuardarForm, 'fas fa-hand-point-right', false);
                btnGuardarRol.children[1].textContent = "Siguiente";
                // btnGuardarRol.querySelector("i").className = "fas fa-hand-point-right";
            }
        }else{
            $('#modalRol').modal("show");
        }
    }
}
window.addEventListener("DOMContentLoaded",loadPage);