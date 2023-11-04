window.onload = loadPage;
function loadPage(){
    let gen = new General();
    const tablaUsuarios = document.querySelector("#tablaUsuarios");
    const tablaUsuariosData = $(tablaUsuarios).DataTable({
        ajax: {
            url: 'usuarios/accion',
            method: 'POST',
            headers: gen.requestJson,
            data: function (d) {
                d.acciones = 'obtener';
                d.area = $("#cbArea").val();
                d.rol = $("#cbRol").val();
            }
        },
        columns: [{
            data: 'id',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data: 'apellidosNombres'
        },{
            data: 'celular'
        },
        {
            data: 'correo'
        },
        {
            data : 'estado',
            render : function(data){
                switch (data) {
                    case 1:
                        return '<span class="text-success">Activo</span>'    
                    break;
                    case 2:
                        return '<span class="text-danger">Por activar</span>'    
                    break;
                    default:
                        return '<span class="text-danget">No establecido</span>'    
                    break;
                }
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-usuario="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-primary p-1" data-usuario="${data}">
                    <small>
                    <i class="fas fa-unlock-alt"></i>
                    Restaurar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-usuario="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    let idUsuario = null;
    const btnGuardar = document.querySelector("#btnGuardarFrm");
    btnGuardar.onclick = e => document.querySelector("#btnFrmEnviar").click();
    const frmUsuario = document.querySelector("#frmUsuario");
    frmUsuario.addEventListener("submit",async function(e){
        e.preventDefault();
        gen.cargandoPeticion(btnGuardar, gen.claseSpinner,true);
        let datos = new FormData(this);
        datos.append("acciones", !idUsuario ? "agregar" : "editar");
        if (idUsuario){
            datos.append("idUsuario",idUsuario);
        }
        try {
            const response = await gen.funcfetch("usuarios/accion",datos);
            gen.cargandoPeticion(btnGuardar,'fas fa-save',false);
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.error("error al guardar el usuario");
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            alertify.alert("Mensaje",response.success);
            $('#usurioModal').modal("hide");
            tablaUsuariosData.draw();
        } catch (error) {
            gen.cargandoPeticion(btnGuardar,'fas fa-save',false);
            alertify.error("error al guardar el usuario");
        }
    });
    const modalTitulo = document.querySelector("#tituloUsuario");
    $('#usurioModal').on('hidden.bs.modal', function (event) {
        frmUsuario.reset();
        modalTitulo.textContent = "Crear Usuario";
        $('#usurioModal .select2').val("").trigger("change");
        boxContrasena.querySelector("input").disabled = false;
        boxContrasena.hidden = false;
        idUsuario = null;
        btnGuardar.querySelector("span").textContent = "Guardar";
    });
    $('#usuarioRestaurar').on('hidden.bs.modal', function (event) {
        idUsuario = null;
        document.querySelector("#id_password_temp").value = document.querySelector("#id_password_temp").dataset.value;
    });
    const boxContrasena = document.querySelector("#boxContrasena");
    tablaUsuarios.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            btnGuardar.querySelector("span").textContent = "Editar";
            let datos = new FormData();
            datos.append("acciones","mostrarEditar");
            datos.append("idUsuario",e.target.dataset.usuario);
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("usuarios/accion",datos);
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                modalTitulo.textContent = "Editar Usuario";
                idUsuario = e.target.dataset.usuario;
                for (const key in response.success) {
                    if (Object.hasOwnProperty.call(response.success, key)) {
                        const valor = response.success[key];
                        const dom = document.querySelector("#idValorModal" + key);
                        if(!dom || !valor){
                            continue;
                        }
                        if (key == "roles"){
                            $(dom).val(valor.map(va => va.id)).trigger("change");
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#idValorModalareaFk').trigger("change");
                boxContrasena.querySelector("input").disabled = true;
                boxContrasena.hidden = true;
                $('#usurioModal').modal("show");
            } catch (error) {
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                console.error(error);
                alertify.error("error al obtener el usuario");
            }
        }
        if (e.target.classList.contains("btn-outline-primary")) {
            idUsuario = e.target.dataset.usuario;
            $('#usuarioRestaurar').modal("show");
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Estás seguro de eliminar a este usuario?",async ()=>{
                let datos = new FormData();
                datos.append("acciones", "eliminar");
                datos.append("idUsuario", e.target.dataset.usuario);
                try {
                    gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                    const response = await gen.funcfetch("usuarios/accion", datos);
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    tablaUsuariosData.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    console.error(error);
                    alertify.error("error al eliminar el usuario");
                }
            },()=>{});
        }
    }
    const btnGuardarRestaura = document.querySelector("#btnGuardarFrmRest"); 
    btnGuardarRestaura.onclick = e => document.querySelector("#btnSubmitRest").click();
    document.querySelector("#formRestaurar").addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append("usuario",idUsuario);
        try {
            gen.cargandoPeticion(btnGuardarRestaura, gen.claseSpinner, true);
            const response = await gen.funcfetch("usuarios/password", datos);
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
            }
            if(response.success){
                alertify.success(response.success);
                $('#usuarioRestaurar').modal("hide");
                tablaUsuariosData.draw();
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al restaurar la contraseña del usuario");
        }finally{
            gen.cargandoPeticion(btnGuardarRestaura, 'fas fa-save', false);
        }
    })
    $('#cbArea, #cbRol').on("change",function(e){
        tablaUsuariosData.draw();
    });
}
