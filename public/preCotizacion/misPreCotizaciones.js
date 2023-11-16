function loadPage() {
    let general = new General();
    const preCotizacion = new PreCotizacion();
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
            render : function(data,type,row){
                let pdfInformeVisita = (row.estado > 1 && row.formato_visita_pdf) && `
                <a class="dropdown-item" href="${window.origin + '/intranet/storage/formatoVisitas/' + row.formato_visita_pdf}" target="_blank">
                    <i class="far fa-file-pdf text-danger mr-1"></i>
                    <span class="text-secondary">Ver informe de visitas</span>
                </a>`;
                let pdfInforme = row.estado > 1 && `
                <a class="dropdown-item" href="reporte/${data}" target="_blank">
                    <i class="far fa-file-pdf text-danger mr-1"></i>
                    <span class="text-secondary">Ver informe</span>
                </a>`;
                return `
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item editar-precotizacion" href="javascript:void(0)" data-precotizacion="${data}">
                            <i class="fas fa-pencil-alt text-primary mr-1"></i>
                            <span class="text-secondary">Editar precotizacion</span>
                        </a>
                        <a class="dropdown-item editar-informe" href="javascript:void(0)" data-precotizacion="${data}">
                            <i class="fas fa-pencil-alt text-primary mr-1"></i>
                            <span class="text-secondary">Editar informe</span>
                        </a>
                        ${!pdfInforme ? '' : pdfInforme}
                        ${!pdfInformeVisita ? '' : pdfInformeVisita}
                        <a class="dropdown-item eliminar-cotizacion" href="javascript:void(0)">
                            <i class="fas fa-trash-alt text-danger mr-1"></i>
                            <span class="text-secondary">Eliminar precotización</span>
                        </a>
                    </div>
                </div>`
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
    let idSeccion = null;
    let borrarDatosInformeModal = true;
    const txtSinImagenes = document.querySelector("#txtSinImagenes");
    const frmServicios = document.querySelector("#contenidoServicios");
    const txtNoServicios = document.querySelector("#txtNoServi");
    const listaServicios = document.querySelector("#contenidoListaServicios");
    const cbServicios = $('#cbServicio');
    const documentoFormatoVisita = document.querySelector("#documentoVisitas");
    const btnEliminarDocumento = document.querySelector("#btnFormatoVisitasEliminar");
    btnEliminarDocumento.onclick = e => {
        alertify.confirm("Alerta","¿Deseas eliminar el reporte de visita?",async () => {
            try {
                const response = await general.funcfetch(preCotizacion.url + `/eliminar/formato-visita/${idReportePreCotizacion}`,null, "DELETE");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.alerta){
                    return alertify.alert("Alerta",response.alerta);
                }
                documentoFormatoVisita.value = "";
                if(document.querySelector("#documento-mostrar-formato-visita")){
                    document.querySelector("#documento-mostrar-formato-visita").remove();
                }
                alertify.success(response.success);
            } catch (error) {
                console.error(error);
                alertify.error("error al eliminar el formato de visita");
            }
        },() => {});
    };
    preCotizacion.$contenidoSecciones.addEventListener("click",async function(e){
        $dom = e.target;
        if($dom.classList.contains("agregar-imagen")){
            document.querySelector($dom.dataset.file).click();
        }
        if($dom.classList.contains("editar-seccion")){
            try {
                let datos = new FormData();
                datos.append("preCotizacion",idReportePreCotizacion);
                datos.append("seccion",$dom.dataset.seccion);
                const response = await general.funcfetch(preCotizacion.url + "/seccion/obtener",datos,"POST");
                if(response.session){
                    return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                }
                if(response.alerta){
                    return alertify.alert("Alerta",response.alerta);
                }
                for (const key in response.success) {
                    if (Object.hasOwnProperty.call(response.success, key)) {
                        const element = response.success[key];
                        const $domValor = document.querySelector("#agregarSeccion #idModalSeccion" + key);
                        if(!$domValor){
                            continue;
                        }
                        $domValor.value = element;
                    }
                }
                idSeccion = response.success.id;
                borrarDatosInformeModal = false;
                $('#modalPrimeraVisita').modal("hide");
                setTimeout(e => {
                    $('#agregarSeccion').modal('show');
                },300);
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener la seccion")
            }
        }
        if($dom.classList.contains("eliminar-img")){
            alertify.confirm("Mensaje","Al continuar se eliminará la imagen. <br> ¿Desea continuar de todas formas?",async () => {
                let datos = new FormData();
                datos.append("preCotizacion",idReportePreCotizacion);
                datos.append("seccion", $dom.dataset.seccion);
                datos.append("img",$dom.dataset.img);
                try {
                    const response = await general.funcfetch(preCotizacion.url + "/seccion/imagen/eliminar",datos,"POST");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                    $contenedor = $dom.parentElement;
                    $contenedor.remove();
                    $contenedorSecciones = document.querySelector(`#idModalContenidoImagenes${$dom.dataset.seccion}`);
                    if($contenedorSecciones.children.length === 0){
                        $contenedorSecciones.innerHTML = `
                        <div class="text-center contenido-vacio-img col-12">
                            <span>No se agregaron imágenes para esta sección</span>
                        </div>
                        `
                    }
                    return alertify.success(response.success);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la imagen")
                }
            },()=>{})
        }
        if($dom.classList.contains("eliminar-seccion")){
            alertify.confirm("Mensaje","Al continuar se eliminará la sección y las imágenes relacionadas a ella. <br> ¿Desea continuar de todas formas?",async () => {
                let datos = new FormData();
                datos.append("preCotizacion",idReportePreCotizacion);
                datos.append("seccion",$dom.dataset.seccion);
                try {
                    const response = await general.funcfetch(preCotizacion.url + "/seccion/eliminar",datos,"POST");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                    $contenedor = $dom.parentElement.parentElement.parentElement;
                    $contenedor.remove();
                    $contenedorSecciones = Array.from((preCotizacion.$contenidoSecciones).children);
                    if($contenedorSecciones.length === 0){
                        preCotizacion.$contenidoSecciones.innerHTML = `
                        <div class="text-center contenido-vacio">
                            <span>No se agregaron secciones</span>
                        </div>
                        `
                    }else{
                        $contenedorSecciones.forEach((seccion,kseccion) => {
                            if(seccion.querySelector(".nombre-seccion")){
                                seccion.querySelector(".nombre-seccion").textContent = `Sección N° ${kseccion + 1}`
                            }
                        })
                    }
                    return alertify.success(response.success);
                } catch (error) {
                    console.error(error);
                    alertify.error("error al eliminar la seccion")
                }
            },()=>{})
        }
    });
    tablaPreCotizacion.addEventListener("click",async function(e){
        if (e.target.classList.contains("editar-informe")){
            btnModalSave.querySelector("span").textContent = "Editar reporte";
            idReportePreCotizacion = e.target.dataset.precotizacion;
            preCotizacion.cargarPreInformeEditar({idVisita : idReportePreCotizacion,cbServicios,listaServicios,txtNoServicios,btnEliminarDocumento,documentoFormatoVisita});
        }
        if (e.target.classList.contains("editar-precotizacion")) {
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
        if (e.target.classList.contains("eliminar-precotizacion")) {
            alertify.confirm("Alerta","¿Estás seguro de eliminar esta precotizacion?",async ()=>{
                try {
                    general.cargandoPeticion(e.target, general.claseSpinner, true);
                    const response = await general.funcfetch("producto/eliminar/" + e.target.dataset.producto, null,"DELETE");
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                    }
                    return alertify.success(response.success);
                } catch (error) {
                    general.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    console.error(error);
                    alertify.error("error al eliminar la precotizacion");
                }
            },()=>{});
        }
    })
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
    document.querySelector("#btnAgregarSeccion").onclick = e => {
        borrarDatosInformeModal = false;
        idSeccion = null;
        $('#modalPrimeraVisita').modal("hide");
        setTimeout(e => {
            $('#agregarSeccion').modal('show');
        },300);
    }
    document.querySelector("#btnGuardarFrmSeccion").onclick = e => document.querySelector("#btnSeccionAgregar").click();
    const $frmSeccion = document.querySelector("#frmSeccionNueva");
    $frmSeccion.addEventListener("submit",function(e){
        e.preventDefault();
        preCotizacion.agregarEditarSeccion(this,idSeccion,idReportePreCotizacion);
    });
    const btnSaveModal = document.querySelector("#btnGenerarReporte");
    btnSaveModal.onclick = e => document.querySelector("#btnFrom").click();
    frmServicios.addEventListener("submit",async function(e){
        e.preventDefault();
        const contenido = tinymce.activeEditor.getContent();
        if(!contenido){
            return alertify.error("por favor redacte el informe");
        }
        const data = new FormData(this);
        data.append("html",contenido);
        data.append("preCotizacion",idReportePreCotizacion);
        if(documentoFormatoVisita.value){
            data.append("formatoVisitaPdf",documentoFormatoVisita.files[0]);
        }
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
        if(!borrarDatosInformeModal){
            return false;
        }
        if(document.querySelector("#documento-mostrar-formato-visita")){
            document.querySelector("#documento-mostrar-formato-visita").remove();
        }
        preCotizacion.$contenidoSecciones.innerHTML = "";
        txtNoServicios.hidden = false;
        btnEliminarDocumento.hidden = true;
        documentoFormatoVisita.value = "";
        if(document.querySelector("#documento-mostrar-formato-visita")){
            document.querySelector("#documento-mostrar-formato-visita").remove();
        }
        for (const c of listaServicios.querySelectorAll(".contenido")) {
            c.remove();
        }
        for (const cb of cbServicios[0].options) {
            cb.disabled = false;
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
    $('#agregarSeccion').on('hidden.bs.modal', function (event) {
        borrarDatosInformeModal = true;
        setTimeout(e => {
            $frmSeccion.reset();
            $('#modalPrimeraVisita').modal('show');
        },300);
    });
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
                tablaPreCotizacionDatatable.draw();
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