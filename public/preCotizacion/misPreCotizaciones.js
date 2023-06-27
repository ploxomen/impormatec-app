function loadPage() {
    let general = new General();
    let estadosPreCotizacion = ["Pragramado","Informado","Cotizado"]
    const tablaPreCotizacion = document.querySelector("#tablaPreCotizaciones");
    const cbTecnico = $("#cbTecnicoResponsable");
    const cbTecnicoOtros = $("#cbOtrosTecnicos");
    const cbCliente = $('#cbCliente');
    const cbContactoCliente = document.querySelector("#cbContactoCliente");
    const tablaPreCotizacionDatatable = $(tablaPreCotizacion).DataTable({
        ajax: {
            url: 'lista-precotizacion',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                // d.acciones = 'obtener';
                // d.area = $("#cbArea").val();
                // d.rol = $("#cbRol").val();
            }
        },
        columns: [
        {
            data: 'nroPreCotizacion'
        },
        {
            data: 'nombreCliente'
        },
        {
            data: 'id',
            render : function(data,type,row){
                return row.nombreTecnico + " " + row.aspellidosTecnico;
            }
        },
        {
            data: 'fechaHrProgramada'
        },
        {
            data : 'estado',
            render : function(data){
                return estadosPreCotizacion[+data-1];
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;">
                <button class="btn btn-sm btn-outline-success p-1" data-precotizacion="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                        Editar precotización
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-info p-1" data-precotizacion="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                        Editar Informe
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-precotizacion="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar precotización
                    </small>
                </button></div>`
            }
        },
        ]
    });
    tinymce.init({
        selector: '#sumernotePreCotizacion',
        language: 'es',
        plugins: 'anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        image_title: true,
        branding: false,
        height: "500px",
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: (cb, value, meta) => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.addEventListener('load', () => {
                const id = 'blobid' + (new Date()).getTime();
                const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                const base64 = reader.result.split(',')[1];
                const blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), { title: file.name });
            });
            reader.readAsDataURL(file);
            });
            input.click();
        },
    });
    let idReportePreCotizacion = null;
    const btnModalSave = document.querySelector("#btnGenerarReporte");
    const tituloModalReporte = document.querySelector("#tituloReporteCotizcion");
    const renderImg = document.querySelector("#renderImg");
    const txtSinImagenes = document.querySelector("#txtSinImagenes");
    const frmServicios = document.querySelector("#contenidoServicios");
    const txtNoServicios = document.querySelector("#txtNoServi");
    const listaServicios = document.querySelector("#contenidoListaServicios");
    const cbServicios = $('#cbServicio');
    function domImagen(idDetalle = null,urlImagen,nombreImagen,descripcion) {
        const contenido = document.createElement("div");
        contenido.className = "form-group col-12 col-xl-6 d-flex align-items-center";
        contenido.style = "gap:5px;"
        let img = new Image();
        img.src = urlImagen;
        img.classList.add('img-guias');
        img.title = nombreImagen;
        let txtDescripcion = document.createElement("textarea");
        txtDescripcion.name = "descripcionImagen[]";
        txtDescripcion.className = "form-control form-control-sm txtdescripcion";
        txtDescripcion.placeholder = "Añadir descripción";
        txtDescripcion.value = descripcion;
        const btnDelete = document.createElement('button');
        btnDelete.classList.add('img-btn-delete','btn','btn-sm','btn-light');
        btnDelete.innerHTML = `<i class="fas fa-trash-alt"></i>`;
        btnDelete.dataset.file = nombreImagen;
        btnDelete.type = "button";
        if(idDetalle){
            btnDelete.dataset.detalle = idDetalle;
            const inputId = document.createElement("input");
            inputId.type = "hidden";
            inputId.name = "idImagenDetalle[]";
            inputId.value = idDetalle;
            contenido.append(inputId);
        }
        contenido.append(img,txtDescripcion,btnDelete);
        renderImg.append(contenido);
    }
    const imgCopia = document.querySelector("#imgCopia");
    const btnImg = document.querySelector("#btnImagen");
    const imgOriginal = document.querySelector("#imgsOriginal");
    btnImg.onclick = e => imgCopia.click();
    function coinsidenciaImg(files,name){
        for (let i = 0; i < files.length; i++) {
            if(files[i].name == name){
                return 1;
            }
        }
        return 0;
    }
    imgCopia.addEventListener("change",async function(e){
        if(!this.files.length){
            return
        }
        const pattern = /image-*/;
        let datos = new FormData();   
        for (let i = 0; i < this.files.length; i++) {
            if(!this.files[i].type.match(pattern)){
                return alertify.alert("Mensaje", "El archivo " + this.files[i].name +" no es una imagen");
            }
            datos.append('imagenes[]', this.files[i]);
        }
        datos.append("idPreCotizacion",idReportePreCotizacion);
        let response = await general.funcfetch("agregar/imagen",datos,"POST");
        if(response.listaImagenes){
            if(renderImg.children.length === 1){
                txtSinImagenes.hidden = true;
            }
            response.listaImagenes.forEach(img => {
                domImagen(img.id,general.urlImagenesPreCotizacion + img.url_imagen,img.nombre_original_imagen,"");
            });
            return alertify.success("la imagen se agrego correctamente");
        }
    })
    function deleteImg(nameImg,li){
        const newDataTrnasfer = new DataTransfer();
        const filesParent = imgOriginal;
        let deleteDom = false;
        for (let i = 0; i < filesParent.files.length; i++) {
            if(filesParent.files[i].name != nameImg){
                newDataTrnasfer.items.add(filesParent.files[i]);
            }else{
                deleteDom = true;
            }
        }
        filesParent.files = newDataTrnasfer.files;
        if(deleteDom){
            li.remove();
            alertify.success("la imagen se a eliminado correctamente del reporte");
        }
        if(renderImg.children.length === 1){
            txtSinImagenes.hidden = false;
        }
    }
    tablaPreCotizacion.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            btnModalSave.querySelector("span").textContent = "Editar reporte";
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("lista/" + e.target.dataset.precotizacion,null,"GET");
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                tituloModalReporte.textContent = "Editar Reporte de Pre - Cotización";
                idReportePreCotizacion = e.target.dataset.precotizacion;
                const preCotizacionResponse = response.precotizacion;
                tinymce.activeEditor.setContent(!preCotizacionResponse.html_primera_visita ? "" : preCotizacionResponse.html_primera_visita);
                if(preCotizacionResponse.listaImagenes.length){
                    txtSinImagenes.hidden = true;
                }
                preCotizacionResponse.listaImagenes.forEach(img => {
                    domImagen(img.id,general.urlImagenesPreCotizacion + img.url_imagen,img.nombre_original_imagen,img.descripcion);
                });
                preCotizacionResponse.listaServicios.forEach(ser => {
                    cbServicios.val(ser.id_servicios).trigger("change");
                });
                $('#modalPrimeraVisita').modal("show");
            } catch (error) {
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                console.error(error);
                alertify.error("error al obtener el reporte de pre - cotización");
            }
        }
        if (e.target.classList.contains("btn-outline-success")) {
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("listar-pre/" + e.target.dataset.precotizacion,null,"GET");
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                idReportePreCotizacion = response.preCotizacion.id;
                cbCliente.val(response.preCotizacion.id_cliente).trigger("change");
                nuevoCliente = false;
                for (const key in response.preCotizacion) {
                    if (Object.hasOwnProperty.call(response.preCotizacion, key)) {
                        const valor = response.preCotizacion[key];
                        const dom = document.querySelector("#idModal" + key);
                        if(key == "contactos"){
                            let template = "<option></option>";
                            valor.forEach(c => {
                                template += `<option value="${c.id}">${c.nombreContacto} - ${c.numeroContacto}</option>`;
                            });
                            cbContactoCliente.innerHTML = template;
                            $(cbContactoCliente).val(response.preCotizacion.contactosAsignados.map(v => v.id_cliente_contacto)).trigger("change");
                            continue;
                        }
                        if(key == "tecnicos"){
                            let tecnicoPrincipal = valor.find(tec => tec.responsable === 1);
                            let tecnicosOtros = valor.filter(tec => tec.responsable !== 1).map( v => v.id_tecnico);
                            cbTecnico.val(tecnicoPrincipal.id_tecnico).trigger("change");
                            cbTecnicoOtros.val(tecnicosOtros).trigger("change");
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#modalPreCotizacion').modal("show");
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener el reporte de pre - cotización");
            }finally{
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Estás seguro de eliminar este producto?",async ()=>{
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("producto/eliminar/" + e.target.dataset.producto, null,"DELETE");
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                    }
                    tablaProductoDatatable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    console.error(error);
                    alertify.error("error al eliminar el usuario");
                }
            },()=>{});
        }
    })
    
    renderImg.onclick = function(e){
        if(e.target.classList.contains("img-btn-delete")){
            const btnEliminarImagen = e.target;
            const li = btnEliminarImagen.parentElement;
            if(!btnEliminarImagen.dataset.detalle){
                deleteImg(btnEliminarImagen.dataset.file,li);
            }else{
                alertify.confirm("Mensaje","¿Estas seguro de eliminar la imagen de este reporte?",async () => {
                    try {
                        general.cargandoPeticion(btnEliminarImagen, general.claseSpinner, true);
                        let datos = new FormData();
                        datos.append("idPreCotizacion",idReportePreCotizacion);
                        datos.append("idImagen",btnEliminarImagen.dataset.detalle);
                        const response = await general.funcfetch("eliminar/imagen",datos,"POST");
                        if (response.session) {
                            return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                        }
                        li.remove();
                        alertify.success(response.success);
                        if(renderImg.children.length === 1){
                            txtSinImagenes.hidden = false;
                        }
                    }catch(error){
                        console.error(error);
                        alertify.error("error al eliminar la imagen del reporte")
                    }finally{
                        general.cargandoPeticion(btnEliminarImagen, 'fas fa-trash-alt', false);
                    }
                },()=>{})
            }
        }
    }
    
    cbServicios.on("change.select2",function(e){
        general.seleccionarServicios(cbServicios,listaServicios,txtNoServicios)
    });
    listaServicios.onclick = function(e){
        if(e.target.classList.contains("btn-sm")){
            for (const cb of cbServicios[0].options) {
                if(cb.value == e.target.dataset.valor){
                    cb.disabled = false;
                }
            }
            e.target.parentElement.remove();
            if(!listaServicios.children.length){
                txtNoServicios.hidden = false;
            }
        }
    }
    const btnSaveModal = document.querySelector("#btnGenerarReporte");
    btnSaveModal.onclick = e => document.querySelector("#btnFrom").click();
    frmServicios.addEventListener("submit",async function(e){
        e.preventDefault();
        const contenido = tinymce.activeEditor.getContent();
        if(!contenido){
            alertify.error("por favor redacte el informe");
        }
        const data = new FormData(this);
        data.append("html",contenido);
        data.append("preCotizacion",idReportePreCotizacion);
        general.cargandoPeticion(btnSaveModal, general.claseSpinner, true);
        try {
            const response = await general.funcfetch("actualizar",data, "POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.success){
                idReportePreCotizacion = null;
                tinymce.activeEditor.setContent("");
                $('#modalPrimeraVisita').modal("hide");
                return alertify.success(response.success);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al actualizar el reporte");
        }finally{
            general.cargandoPeticion(btnSaveModal, 'far fa-save', false);
        }
    })
    $('#modalPrimeraVisita').on('hidden.bs.modal', function (event) {
        tinymce.activeEditor.setContent("");
        txtNoServicios.hidden = false;
        txtSinImagenes.hidden = false;
        for (const c of listaServicios.querySelectorAll(".contenido")) {
            c.remove();
        }
        for (const cb of cbServicios[0].options) {
            cb.disabled = false;
        }
        for (const red of renderImg.querySelectorAll(".d-flex")) {
            red.remove();
        }
    });
    const formPreCotizacion = document.querySelector("#contenidoPreCotizacion");
    let nuevoCliente = false;
    $('#modalPreCotizacion').on('hidden.bs.modal', function (event) {
        idReportePreCotizacion = null;
        cbTecnico.val("").trigger("change");
        cbTecnicoOtros.val("").trigger("change");
        formPreCotizacion.reset();
        nuevoCliente = false;
        $(cbContactoCliente).select2("destroy");
        let opcionesClientes = {
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione los contactos',
            tags: false
        }
        habilitarTextoClienteNuevo(true);
        $(cbContactoCliente).select2(opcionesClientes);
    });
    //EDITAR PRE - COTIZACIOON
    cbCliente.on("select2:select",async function(e){
        let datos = new FormData();
        datos.append("cliente",$(this).val());
        habilitarTextoClienteNuevo(true);
        try {
            const response = await general.funcfetch("obtener/clientes",datos, "POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            $(cbContactoCliente).select2("destroy");
            let opcionesClientes = {
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Seleccione los contactos',
                tags: false
            }
            if(response.cliente && Object.keys(response.cliente).length){
                nuevoCliente = false;
                for (const key in response.cliente) {
                    if (Object.hasOwnProperty.call(response.cliente, key)) {
                        const valor = response.cliente[key];
                        const dom = document.querySelector("#idModal" + key);
                        if(key == "contactos"){
                            let template = "<option></option>";
                            valor.forEach(c => {
                                template += `<option value="${c.id}">${c.nombreContacto} - ${c.numeroContacto}</option>`;
                            });
                            cbContactoCliente.innerHTML = template;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#modalPreCotizacion .select2-simple').trigger("change");
            }else if(response.cliente && !Object.keys(response.cliente).length){
                nuevoCliente = true;
                opcionesClientes.tags = true;
                habilitarTextoClienteNuevo(false);
                limpiarFormularioCliente();
            }
            $(cbContactoCliente).select2(opcionesClientes);
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener la informacion del cliente");
        }
    });
    cbTecnico.on("change.select2",function(e){
        for (const cb of cbTecnicoOtros[0].options) {
            cb.disabled = false;
        }
        const optionCb = cbTecnicoOtros[0].options[e.target.selectedIndex];
        optionCb.disabled = true;
        if(cbTecnicoOtros[0].selectedIndex == e.target.selectedIndex){
            optionCb.selected = false;
            cbTecnicoOtros.trigger("change");
        }
    });
    cbTecnicoOtros.on("change.select2",function(e){
        const valores = $(this).val();
        for (const cb of cbTecnico[0].querySelectorAll("option")) {
            cb.disabled = valores && valores.indexOf(cb.value) >= 0 ? true : false;
        }
    });
    function limpiarFormularioCliente(){
        for (const txt of document.querySelectorAll(".limpiar-frm")) {
            txt.value = "";
        }
        cbContactoCliente.innerHTML = "<option></option>";
        $('#modalPreCotizacion .select2-simple').trigger("change");
    }
    function habilitarTextoClienteNuevo(condicion){
        for (const txt of document.querySelectorAll(".text-muted")) {
            txt.hidden = condicion;
        }
    }
    const btnPreCoti = document.querySelector("#btnEditarPreCotizacion");
    btnPreCoti.onclick = e => document.querySelector("#btnFrmEnviar").click();
    formPreCotizacion.addEventListener("submit",async function(e){
        e.preventDefault();
        general.cargandoPeticion(btnPreCoti, general.claseSpinner, true);
        let datos = new FormData(this);
        datos.append("idPreCotizacion",idReportePreCotizacion);
        datos.append("nuevo",nuevoCliente);
        try {
            const response = await general.funcfetch("editar",datos, "POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.success){
                if(response.idCliente){
                    cbCliente.append(`<option value=${response.idCliente}>${response.nombreCliente}</option>`);
                }
                $('#modalPreCotizacion').modal('hide');
                return alertify.success(response.success);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al editar una pre - cotización");
        }finally{
            general.cargandoPeticion(btnPreCoti, 'fas fa-pencil-alt', false);
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);