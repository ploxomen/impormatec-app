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
            data: 'almacenes',
            name : 'almacenes.name',
            render : function(data){
                return !data ? '' : `<span class="badge badge-primary">${data.map(d => d.nombre).join("</span><br><span class='badge badge-primary'>")}</span> `;
            }
        },
        {
            data: 'precioVenta',
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipoMoneda)
            }
        },
        {
            data: 'precioCompra',
            render : function(data,type,row){
                return gen.resetearMoneda(data,row.tipoMoneda)
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
    const btnFileProducto = document.querySelector("#subirExcelUtilidades");
    const inputFileUtilidad = document.querySelector("#inputFileExcelUtilidades");
    const $contenidoUtilidades = document.querySelector("#contenidoUtilidades");
    const btnUtilidadesImportar = document.querySelector("#btnImportarUtilidadesGuardar");
    btnFileProducto.onclick = e => inputFileUtilidad.click();
    let contenidoUtilidades = [];
    inputFileUtilidad.addEventListener("change",async function(e){
        const file = e.target.files[0];
        if(!file){
            return false
        }
        let datos = new FormData();
        datos.append("excel_file",file);
        try{
            const response = await gen.funcfetch("producto/importar/utilidades",datos,"POST");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(!response.listaProductos.length){
                const tr = gen.creacionDOM("tr");
                const td = gen.creacionDOM("td",{class:"text-center",colspan:"100%"},{textContent:"No se encontraron productos"});
                tr.append(td);
                $contenidoUtilidades.append(tr);
                contenidoUtilidades = [];
                return;
            }
            response.listaProductos.forEach((producto,keyProducto) => {
                const tr = gen.creacionDOM("tr",{class:!producto.idProducto ? 'table-danger' : ''});
                const tdIndex = gen.creacionDOM("td",{class:"text-center"},{textContent:keyProducto + 1});
                const tdProducto = gen.creacionDOM("td",null,{textContent:producto.nombreProducto});
                const tdPrecioCompra = gen.creacionDOM("td",{class:"text-center"},{textContent:gen.resetearMoneda(Number.parseFloat(producto.precioCompra).toFixed(2),producto.tipoMoneda)});
                const tdUtilidad = gen.creacionDOM("td",{class:"text-center"},{textContent:producto.utilidad});
                const tdPrecioVenta = gen.creacionDOM("td",{class:"text-center"},{textContent:gen.resetearMoneda(Number.parseFloat(producto.precioVenta).toFixed(2),producto.tipoMoneda)});
                tr.append(tdIndex,tdProducto,tdPrecioCompra,tdUtilidad,tdPrecioVenta);
                $contenidoUtilidades.append(tr);
            });
            contenidoUtilidades = response.listaProductos;
            $("#importarUtilidades").modal("show");
        }catch(error){
            console.log(error);
            alertify.error("error al cargar los datos del excel");
        }finally{
            inputFileUtilidad.value = "";
        }
    });
    const txtUtilidad = document.querySelector("#idModalutilidad");
    const txtPrecioVenta = document.querySelector("#idModalprecioVenta");
    const txtPrecioCompra = document.querySelector("#idModalprecioCompra");
    txtUtilidad.addEventListener("input",e => {
        let utilidad = Number.parseFloat(e.target.value);
        if(isNaN(utilidad)){
            utilidad = 0;
        }
        const precioVenta = Number.parseFloat(txtPrecioCompra.value)/(1 - utilidad / 100);
        txtPrecioVenta.value = precioVenta.toFixed(2);
    });
    txtPrecioCompra.addEventListener("input",e => {
        let precioCompra = Number.parseFloat(e.target.value);
        if(isNaN(precioCompra)){
            precioCompra = 0;
        }
        const precioVenta = precioCompra/(1 - Number.parseFloat(txtUtilidad.value) / 100);
        txtPrecioVenta.value = precioVenta.toFixed(2);
    });
    btnUtilidadesImportar.onclick = e => {
        if(!contenidoUtilidades.length){
            return alertify.alert("Alerta","No se encontraron productos para ser importados");
        }
        const productosNoEncontrados = contenidoUtilidades.filter(producto => producto.idProducto === null);
        if(productosNoEncontrados.length === contenidoUtilidades.length){
            return alertify.alert("Alerta","Todos los productos importados no se encuentran registrados en el sistema");
        }
        alertify.confirm("Mensaje",`Al continuar se procederan a actualizar las utilidades los productos ${contenidoUtilidades.length - productosNoEncontrados.length} de ${contenidoUtilidades.length} <br>¿Deseas continuar de todas formas?`,async ()=>{
            let datos = new FormData();
            datos.append("utilidad_importar",JSON.stringify(contenidoUtilidades.filter(producto => producto.idProducto !== null)));
            try{
                const response = await gen.funcfetch("producto/importar/utilidades/actualizar",datos,"POST");
                if(response.session){
                    return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
                }
                tablaProductoDatatable.draw();
                $("#importarUtilidades").modal("hide");
                return alertify.alert("Mensaje",response.success);
            }catch(error){
                console.log(error);
                alertify.error("error al actualizar las utilidades de los productos");
            }finally{
                inputFileUtilidad.value = "";
            }
        },()=>{})
    }
    $('#importarUtilidades').on("hidden.bs.modal",function(e){
        contenidoUtilidades = [];
        $contenidoUtilidades.innerHTML = "";
    });
    let idProducto = null;
    const prevImagen = document.querySelector("#imgPrevio");
    const fileImagen = document.querySelector("#customFileLang");
    fileImagen.onchange = function(e){
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
    const modalTitulo = document.querySelector("#tituloProducto");
    const switchProducto = document.querySelector("#switchProductoIntangible");
    const htmlSinAlmacen = `<tr>
        <td colspan="100%" class="text-center">No se seleccionaron almacenes</td>
    </tr>`;
    let contadorAlmacenes = 0;
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
        contadorAlmacenes = 0;
        listaAlmacen.innerHTML = htmlSinAlmacen;
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
        listaAlmacen.innerHTML = htmlSinAlmacen;  
        for (const div of document.querySelectorAll("#agregarProducto .producto-tangible")) {
            div.hidden = valor;
        }
        for (const oAlmacen of cbAlmacen.querySelectorAll("option")) {
            oAlmacen.disabled = false;
        }
    }
    $(cbAlmacen).on("select2:select",function(e){
        let selectedOption = cbAlmacen.options[cbAlmacen.selectedIndex];
        if(!contadorAlmacenes){
            listaAlmacen.innerHTML = "";
        }
        listaAlmacen.append(agregarAlmacen($(this).val(),selectedOption.text,""));
        alertify.success("almacen agregado");
        contadorAlmacenes++;
        selectedOption.disabled = true;
    })
    function agregarAlmacen(idAlmacen,nombreAlmacen,stock,tipo = "new") {
        const lista = document.createElement("tr");
        lista.dataset.tipo = tipo;
        lista.dataset.almacen = idAlmacen;
        let $idAlmacen = idAlmacen ? `<input type="hidden" value="${idAlmacen}" name="idAlmacen[]">` : "";
        lista.innerHTML = 
        `<td>
            ${$idAlmacen}
            <span>${nombreAlmacen}</span>
        </td>
        <td>
            <input title="Stock del producto por almacen" type="number" name="stockAlmacen[]" min="1" required class="form-control form-control-sm" value="${stock}">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
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
            const li = e.target.parentElement.parentElement;
            const almacen = li.dataset.almacen;
            if(li.dataset.tipo == "new"){
                li.remove();
                habilitarOpcionAlmacen(almacen,false);
                contadorAlmacenes--;
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
                        contadorAlmacenes--;
                        if(!contadorAlmacenes){
                            listaAlmacen.innerHTML = htmlSinAlmacen;
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
            if(!contadorAlmacenes){
                listaAlmacen.innerHTML = htmlSinAlmacen;
            }
        }
    }
    const switchEstado = document.querySelector("#idModalestado");
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    tablaProducto.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
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
                        if(key === "tipoMoneda"){
                            const txtMoneda = valor === "USD" ? document.querySelector("#tipoMonedaDolares") : document.querySelector("#tipoMonedaSoles");
                            txtMoneda.checked = true;
                        }
                        if (key == "listaAlmacen"){
                            contadorAlmacenes = valor.length;
                            if(contadorAlmacenes){
                                listaAlmacen.innerHTML = "";
                            }
                            valor.forEach(al => {
                                listaAlmacen.append(agregarAlmacen(al.id_almacen,al.nombre,al.stock,"old"));
                                habilitarOpcionAlmacen(al.id_almacen,true);
                            });
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
    });
    const btnElimiarImagen = document.querySelector("#btnEliminarImagen");
    btnElimiarImagen.addEventListener("click",function(e){
        e.preventDefault();
        console.log(fileImagen.files.length);
        if(!idProducto && fileImagen.files.length){
            fileImagen.value = "";
            prevImagen.src = window.origin + "/img/imgprevproduc.png";
        }else if(idProducto){
            alertify.confirm("Mensaje","¿Deseas eliminar la imagen asociada a este producto?",async ()=>{
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                try {
                    let response = await gen.funcfetch("producto/eliminar/imagen/" + idProducto, null, "POST");
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    if(response.alerta){
                        return alertify.alert("Mensaje",response.alerta);
                    }
                    fileImagen.value = "";
                    prevImagen.src = window.origin + "/img/imgprevproduc.png";
                    alertify.success(response.success);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la imagen asociada a este producto");
                }finally{
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                }
            },()=>{})
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);

