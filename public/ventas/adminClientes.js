function loadPage(){
    let gen = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",gen.switchs);
    }
    const tablaClientes = document.querySelector("#tablaClientes");
    const tablaClientesDatatable = $(tablaClientes).DataTable({
        ajax: {
            url: 'clientes/listar',
            method: 'POST',
            headers: gen.requestJson
        },
        columns: [{
            data: 'id',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data: 'documento'
        },
        {
            data: 'nroDocumento',
        },
        {
            data: 'nombreCliente',
        },
        {
            data: 'celular',
        },
        {
            data: 'telefono',
        },
        {
            data: 'correo',
        },
        {
            data: 'direccion',
        },
        {
            data : 'estado',
            render : function(data){
                if(data === 1){
                    return '<span class="badge badge-success">Activo</span>';
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
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-cliente="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-cliente="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    let idCliente = null;
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    const formCliente = document.querySelector("#formCliente");
    formCliente.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            gen.cargandoPeticion(btnModalSave, gen.claseSpinner, true);
            const response = await gen.funcfetch(idCliente ? "clientes/editar/" + idCliente : "clientes/crear",datos);
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.session){
                return alertify.alert([...alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            alertify.alert("Mensaje",response.success);
            tablaClientesDatatable.draw();
            $('#agregarCliente').modal("hide");
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar un cliente");
        }finally{
            gen.cargandoPeticion(btnModalSave, 'fas fa-save', false);
        }
    });
    const listaContacto = document.querySelector("#listaContactos");
    const txtSinContacto = document.querySelector("#txtSinContacto");
    document.querySelector("#btnAgregarContacto").onclick = e=>{
        listaContacto.append(agregarContacto(null,"",""));
        if(listaContacto.children.length){
            txtSinContacto.hidden = true;
        }
    }
    listaContacto.onclick = function(e){
        if(e.target.classList.contains("btn-danger")){
            const li = e.target.parentElement.parentElement.parentElement;
            if(li.dataset.tipo == "new"){
                li.remove();
                alertify.success("contacto eliminado");
            }else if(li.dataset.tipo == "old"){
                alertify.confirm("Mensaje","¿Estas seguro de eliminar este contacto de forma permanente?",async () => {
                    try {
                        gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                        const response = await gen.funcfetch("clientes/contacto/eliminar/" + li.dataset.contacto,null,"GET");
                        if (response.session) {
                            return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                        }
                        li.remove();
                        alertify.success(response.success);
                        if(!listaContacto.children.length){
                            txtSinContacto.hidden = false;
                        }
                    }catch(error){
                        console.error(error);
                        alertify.error("error al eliminar el contacto ")
                    }finally{
                        gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    }
                },()=>{})
            }
            if(!listaContacto.children.length){
                txtSinContacto.hidden = false;
            }
        }
    }
    function ocultarElementos(valor) {
        for (const elemento of document.querySelectorAll(".ocultar-editar")) {
            elemento.hidden = valor;
            if(elemento.disabled !== undefined){
                elemento.disabled = valor;
            }
        }
    }
    function agregarContacto(idContacto,nombre,numero) {
        const lista = document.createElement("li");
        lista.dataset.tipo = idContacto ? 'old' : 'new';
        lista.dataset.contacto = idContacto;
        let $idContacto = idContacto ? `<input type="hidden" value="${idContacto}" name="idContacto[]">` : "";
        lista.innerHTML = 
        `<div class="form-row">
            ${$idContacto}
            <div class="col-12 col-md-7 form-group">
                <input type="text" name="contactoNombres[]" required class="form-control form-control-sm" value="${nombre}" placeholder="Nombres">
            </div>
            <div class="col-12 col-md-4 form-group">
                <input type="text" name="contactoNumero[]" required class="form-control form-control-sm" value="${numero}" placeholder="Contacto">
            </div>
            <div class="col-12 text-rigth col-md-1 form-group">
                <button type="button" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
        `
        return lista;
    }
    const modalTitulo = document.querySelector("#titulocliente");
    $('#agregarCliente').on("hidden.bs.modal",function(e){
        idCliente = null;
        modalTitulo.textContent = "Crear cliente";
        switchEstado.disabled = true;
        btnModalSave.querySelector("span").textContent ="Guardar";
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        formCliente.reset();
        listaContacto.innerHTML = "";
        if(!listaContacto.children.length){
            txtSinContacto.hidden = false;
        }
        ocultarElementos(false);
        $('#agregarCliente .select2-simple').trigger("change");
    });
    const switchEstado = document.querySelector("#idModalestado");
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    tablaClientes.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            btnModalSave.querySelector("span").textContent = "Editar";
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("clientes/listar/" + e.target.dataset.cliente,null,"GET");
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                ocultarElementos(true);
                modalTitulo.textContent = "Editar cliente";
                idCliente = e.target.dataset.cliente;
                for (const key in response.cliente) {
                    if (Object.hasOwnProperty.call(response.cliente, key)) {
                        const valor = response.cliente[key];
                        const dom = document.querySelector("#idModal" + key);
                        if (key == "contactos"){
                            valor.forEach(c => {
                                listaContacto.append(agregarContacto(c.id,c.nombreContacto,c.numeroContacto));
                            });
                            if(listaContacto.children.length){
                                txtSinContacto.hidden = true;
                            }
                        }
                        if (key == "estado"){
                            switchEstado.checked = valor === 1 ? true : false;
                            switchEstado.parentElement.querySelector("label").textContent = valor === 1 ? "VIGENTE" : "DESCONTINUADO";
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#agregarCliente .select2-simple').trigger("change");
                switchEstado.disabled = false;
                $('#agregarCliente').modal("show");
            } catch (error) {
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                console.error(error);
                alertify.error("error al obtener el cliente");
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Estás seguro de eliminar este cliente?",async ()=>{
                try {
                    gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                    const response = await gen.funcfetch("clientes/eliminar/" + e.target.dataset.cliente, null,"DELETE");
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    tablaClientesDatatable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    console.error(error);
                    alertify.error("error al eliminar el cliente");
                }
            },()=>{});
            
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);

