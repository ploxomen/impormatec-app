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
            data: 'codigoBarra'
        },
        {
            data: 'nombreProducto'
        },
        {
            data: 'marca.nombreMarca',
            name : 'marca.nombreMarca'
        },
        {
            data : 'categoria.nombreCategoria',
            name : 'categoria.nombreCategoria'
        },
        {
            data: 'cantidad'
        },
        {
            data: 'cantidadMin',
            render : function(data){
                return isNaN(parseInt(data)) ? 0 : parseInt(data);
            }
        },
        {
            data : 'presentacion.nombrePresentacion',
            name : 'presentacion.nombrePresentacion'
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
    const modalTitulo = document.querySelector("#tituloProducto");
    $('#agregarProducto').on("hidden.bs.modal",function(e){
        idProducto = null;
        modalTitulo.textContent = "Crear Producto";
        switchEstado.disabled = true;
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        switchIgv.checked = true;
        switchIgv.parentElement.querySelector("label").textContent = "CON IGV";
        document.querySelector("#customFileLang").value = "";
        formProducto.reset();
        $('#agregarProducto .select2-simple').trigger("change");
        prevImagen.src = window.origin + "/asset/img/imgprevproduc.png";
    });
    const switchEstado = document.querySelector("#idModalestado");
    const switchIgv = document.querySelector("#idModaligv");
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
                        if (key == "estado"){
                            switchEstado.checked = valor === 1 ? true : false;
                            continue;
                        }
                        if (key == "igv"){
                            switchIgv.checked = valor === 1 ? true : false;
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

