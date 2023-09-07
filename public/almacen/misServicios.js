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
                return `
                <div class="d-flex justify-content-center" style="gap:5px;">
                    <button class="btn btn-sm btn-outline-info p-1" data-servicio="${data}">
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
                    </button>
                </div>`
            }
        },
        ]
    });
    let idServicio = null;
    let serviciosSeleccionados = 0;
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
    const cbProducto = document.querySelector("#cbProductos");
    const tablaProducto = document.querySelector("#detalleProductos");
    $('#agregarServicio').on("hidden.bs.modal",function(e){
        serviciosSeleccionados = 0;
        idServicio = null;
        modalTitulo.textContent = "Crear Servicio";
        switchEstado.disabled = true;
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        formServicio.reset();
        tablaProducto.innerHTML = `
        <tr>
            <td class="text-center" colspan="4">No se seleccionaron productos</td>
        </tr>`;
        for (const oAlmacen of cbProducto.querySelectorAll("option")) {
            oAlmacen.disabled = false;
        }
        $(cbProducto).val("").trigger("change");
        btnModalSave.querySelector("span").textContent = "Guardar";
    });
    function agregarProducto(idProducto,nombreProducto,cantidadProducto,urlProducto,tipo = "new") {
        const tr = document.createElement("tr");
        tr.dataset.tipo = tipo;
        tr.dataset.producto = idProducto;
        let $idProducto = idProducto ? `<input type="hidden" value="${idProducto}" name="idProducto[]">` : "";
        tr.innerHTML = 
        `
        <td><img class="img-vistas-pequeña" src="${window.origin + "/intranet/storage/productos/" + urlProducto}"></td>
        <td>${$idProducto}${nombreProducto}</td>
        <td><input title="Cantidad a utilizar" type="number" name="cantidadProducto[]" min="1" required class="form-control form-control-sm" value="${cantidadProducto}" placeholder="Cantidad"></td>
        <td>
            <div class="col-12 text-rigth col-md-1 form-group">
                <button type="button" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </td>
        `
        return tr;
    }
    $(cbProducto).on("select2:select",function(e){
        let selectedOption = cbProducto.options[cbProducto.selectedIndex];
        if(!serviciosSeleccionados){
            tablaProducto.innerHTML = "";
        }
        tablaProducto.append(agregarProducto($(this).val(),selectedOption.text,"",selectedOption.dataset.url));
        alertify.success("producto agregado");
        selectedOption.disabled = true;
        serviciosSeleccionados++;
    })
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    tablaServicio.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("servicio/listar/" + e.target.dataset.servicio,null,"GET");
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                modalTitulo.textContent = "Editar Servicio";
                idServicio = e.target.dataset.servicio;
                for (const key in response.servicio) {
                    if (Object.hasOwnProperty.call(response.servicio, key)) {
                        const valor = response.servicio[key];
                        const dom = document.querySelector("#idModal" + key);
                        if (key == "listaProductos"){
                            serviciosSeleccionados = valor.length;
                            if(serviciosSeleccionados){
                                tablaProducto.innerHTML = ``;
                            }
                            valor.forEach(pro => {
                                tablaProducto.append(agregarProducto(pro.idProducto,pro.nombreProducto,pro.cantidadUsada,pro.urlImagen,"old"));
                                cambioOpcionCbProducto(pro.idProducto,true);
                            });
                            continue;
                        }
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
    function cambioOpcionCbProducto(idProducto,disabled) {
        for (const oAlmacen of cbProducto.querySelectorAll("option")) {
            if(oAlmacen.value == idProducto){
                oAlmacen.disabled = disabled;
            }
        }
    }
    tablaProducto.onclick = function(e){
        if(e.target.classList.contains("btn-danger")){
            const li = e.target.parentElement.parentElement.parentElement;
            const idProducto = li.dataset.producto;
            if(li.dataset.tipo == "new"){
                li.remove();
                cambioOpcionCbProducto(idProducto,false);
                alertify.success("se a eliminado el produco del servicio");
                $(cbProducto).val("").trigger("change");
            }else if(li.dataset.tipo == "old"){
                alertify.confirm("Mensaje","¿Estas seguro de eliminar el producto de este sercivio?",async () => {
                    try {
                        gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                        let datos = new FormData();
                        datos.append("idServicio",idServicio);
                        datos.append("idProducto",idProducto);
                        const response = await gen.funcfetch("servicio/producto/eliminar",datos,"POST");
                        if (response.session) {
                            return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                        }
                        li.remove();
                        alertify.success(response.success);
                        if(!tablaProducto.children.length){
                            tablaProducto.innerHTML = `
                            <tr>
                                <td class="text-center" colspan="4">No se seleccionaron productos</td>
                            </tr>
                            `;
                        }
                        cambioOpcionCbProducto(idProducto,false);
                        $(cbProducto).val("").trigger("change");
                    }catch(error){
                        console.error(error);
                        alertify.error("error al eliminar el producto del servicio")
                    }finally{
                        gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    }
                },()=>{})
            }
            if(!tablaProducto.children.length){
                tablaProducto.innerHTML = `
                <tr>
                    <td class="text-center" colspan="4">No se seleccionaron productos</td>
                </tr>`;
            }
        }
    }
}
window.addEventListener("DOMContentLoaded",loadPage);

