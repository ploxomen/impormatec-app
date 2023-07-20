function loadPage(){
    let gen = new General();
    for (const cambioCantidad of document.querySelectorAll('.cambiar-cantidad')) {
        cambioCantidad.addEventListener("click",gen.aumentarDisminuir);
    }
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",gen.switchs);
    }
    const tablaProducto = document.querySelector("#tablaProductos");
    const tablaProductoDatatable = $(tablaProducto).DataTable({
        ajax: {
            url: 'producto/listar',
            method: 'POST',
            headers: gen.requestJson,
            data: function (d) {
                // d.acciones = 'obtener';
                // d.area = $("#cbArea").val();
                // d.rol = $("#cbRol").val();
            }
        },
        columns: [{
            data: 'id',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data: 'nombreProducto'
        },
        {
            data: 'descripcion'
        },
        {
            data: 'stockMin',
            render : function(data){
                return isNaN(parseInt(data)) ? 0 : parseInt(data);
            }
        },
        {
            data: 'precioVenta',
            render : function(data){
                return gen.resetearMoneda(data)
            }
        },
        {
            data: 'precioCompra',
            render : function(data){
                return gen.resetearMoneda(data)
            }
        },
        {
            data : 'estado',
            render : function(data){
                if(data === 1){
                    return '<span class="badge badge-success">VIGENTE</span>';
                }else if(data === 0){
                    return '<span class="badge badge-danger">DESCONTINUADO</span>';
                }else{
                    return '<span class="text-danget">No establecido</span>';
                }
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-producto="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-producto="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    let idProducto = null;
    const prevImagen = document.querySelector("#imgPrevio");
    document.querySelector("#customFileLang").onchange = function(e){
        let reader = new FileReader();
        reader.onload = function(){
            prevImagen.src = reader.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    const formProducto = document.querySelector("#formProducto");
    formProducto.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            gen.cargandoPeticion(btnModalSave, gen.claseSpinner, true);
            const response = await gen.funcfetch(idProducto ? "producto/editar/" + idProducto : "producto/crear",datos);
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            alertify.success(response.success);
            tablaProductoDatatable.draw();
            $('#agregarProducto').modal("hide");
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar un producto");
        }finally{
            gen.cargandoPeticion(btnModalSave, 'fas fa-save', false);
        }
    });
    const cbAlmacen = document.querySelector("#cbAlmacen");
    const listaAlmacen = document.querySelector("#listaAlmacenes");
    const txtSinAlamacen = document.querySelector("#txtSinAlmacen");
    const modalTitulo = document.querySelector("#tituloProducto");
    const switchProducto = document.querySelector("#switchProductoIntangible");

    $('#agregarProducto').on("hidden.bs.modal",function(e){
        switchProducto.checked = false;
        idProducto = null;
        modalTitulo.textContent = "Crear Producto";
        switchEstado.disabled = true;
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        document.querySelector("#customFileLang").value = "";
        formProducto.reset();
        $('#agregarProducto .select2-simple').val("").trigger("change");
        prevImagen.src = window.origin + "/img/imgprevproduc.png";
        listaAlmacen.innerHTML = "";
        txtSinAlamacen.hidden = false;
        for (const oAlmacen of cbAlmacen.querySelectorAll("option")) {
            oAlmacen.disabled = false;
        }
        for (const div of document.querySelectorAll("#agregarProducto .producto-tangible")) {
            div.hidden = false;
        }
        btnModalSave.querySelector("span").textContent = "Guardar";
    });
    switchProducto.addEventListener("change",productosCambio)
    function productosCambio(e) {
        const valor = e.target.checked;
        listaAlmacen.innerHTML = "";
        txtSinAlamacen.hidden = false;
        for (const div of document.querySelectorAll("#agregarProducto .producto-tangible")) {
            div.hidden = valor;
        }
        for (const oAlmacen of cbAlmacen.querySelectorAll("option")) {
            oAlmacen.disabled = false;
        }
    }
    $(cbAlmacen).on("select2:select",function(e){
        let selectedOption = cbAlmacen.options[cbAlmacen.selectedIndex];
        listaAlmacen.append(agregarAlmacen($(this).val(),selectedOption.text,""));
        if(listaAlmacen.children.length){
            txtSinAlamacen.hidden = true;
        }
        alertify.success("almacen agregado");
        selectedOption.disabled = true;
    })
    function agregarAlmacen(idAlmacen,nombreAlmacen,precioVenta,stock,tipo = "new") {
        const lista = document.createElement("li");
        lista.dataset.tipo = tipo;
        lista.dataset.almacen = idAlmacen;
        let $idAlmacen = idAlmacen ? `<input type="hidden" value="${idAlmacen}" name="idAlmacen[]">` : "";
        lista.innerHTML = 
        `<div class="form-row">
            ${$idAlmacen}
            <div class="col-12 col-md-7 form-group">
                <span>${nombreAlmacen}</span>
            </div>
            <div class="col-12 col-md-2 form-group">
                <input title="Precio de venta del producto por almacen" type="number" name="precioVenta[]" min="0" required class="form-control form-control-sm" value="${precioVenta}" placeholder="Precio venta">
            </div>
            <div class="col-12 col-md-2 form-group">
                <input title="Stock del producto por almacen" type="number" name="stockAlmacen[]" min="1" required class="form-control form-control-sm" value="${stock}" placeholder="Stock">
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
    function habilitarOpcionAlmacen(idAlmacen,disabled) {
        for (const oAlmacen of cbAlmacen.querySelectorAll("option")) {
            if(oAlmacen.value == idAlmacen){
                oAlmacen.disabled = disabled;
            }
        }
    }
    listaAlmacen.onclick = function(e){
        if(e.target.classList.contains("btn-danger")){
            const li = e.target.parentElement.parentElement.parentElement;
            const almacen = li.dataset.almacen;
            if(li.dataset.tipo == "new"){
                li.remove();
                habilitarOpcionAlmacen(almacen,false);
                alertify.success("se a eliminado el produco del almacen");
                $(cbAlmacen).val("").trigger("change");
            }else if(li.dataset.tipo == "old"){
                alertify.confirm("Mensaje","¿Estas seguro de eliminar el producto de este almacen?",async () => {
                    try {
                        gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                        let datos = new FormData();
                        datos.append("idProducto",idProducto);
                        datos.append("idAlmacen",almacen);
                        const response = await gen.funcfetch("producto/almacen/eliminar",datos,"POST");
                        if (response.session) {
                            return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                        }
                        li.remove();
                        alertify.success(response.success);
                        if(!listaAlmacen.children.length){
                            txtSinAlamacen.hidden = false;
                        }
                        habilitarOpcionAlmacen(almacen,false);
                        $(cbAlmacen).val("").trigger("change");
                    }catch(error){
                        console.error(error);
                        alertify.error("error al eliminar el producto del almacen ")
                    }finally{
                        gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    }
                },()=>{})
            }
            if(!listaAlmacen.children.length){
                txtSinAlamacen.hidden = false;
            }
        }
    }
    const switchEstado = document.querySelector("#idModalestado");
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    tablaProducto.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            btnModalSave.querySelector("span").textContent = "Editar";
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("producto/listar/" + e.target.dataset.producto,null,"GET");
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                modalTitulo.textContent = "Editar Producto";
                idProducto = e.target.dataset.producto;
                for (const key in response.producto) {
                    if (Object.hasOwnProperty.call(response.producto, key)) {
                        const valor = response.producto[key];
                        const dom = document.querySelector("#idModal" + key);
                        if(key == "esIntangible" && valor === 1){
                            switchProducto.checked = true;
                            for (const div of document.querySelectorAll("#agregarProducto .producto-tangible")) {
                                div.hidden = true;
                            }
                            continue;
                        }
                        if (key == "listaAlmacen"){
                            valor.forEach(al => {
                                listaAlmacen.append(agregarAlmacen(al.id_almacen,al.nombre,al.precioVenta,al.stock,"old"));
                                habilitarOpcionAlmacen(al.id_almacen,true);
                            });
                            if(listaAlmacen.children.length){
                                txtSinAlamacen.hidden = true;
                            }
                            continue;
                        }
                        if (key == "estado"){
                            switchEstado.parentElement.querySelector("label").textContent = valor === 1 ? "VIGENTE" : "DESCONTINUADO";
                            switchEstado.checked = valor === 1 ? true : false;
                            continue;
                        }
                        if((!dom || !valor) && key != 'urlProductos'){
                            continue;
                        }
                        if(key == "urlProductos"){
                            if (valor){
                                prevImagen.src = valor;
                            }
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#agregarProducto .select2-simple').trigger("change");
                switchEstado.disabled = false;
                $('#agregarProducto').modal("show");
            } catch (error) {
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                console.error(error);
                alertify.error("error al obtener el producto");
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Estás seguro de eliminar este producto?",async ()=>{
                try {
                    gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                    const response = await gen.funcfetch("producto/eliminar/" + e.target.dataset.producto, null,"DELETE");
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    tablaProductoDatatable.draw();
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

