class PreCotizacion extends General {
    $contenidoSecciones = document.querySelector("#contenidoSecciones")
    generarSecciones({index,idSeccion,titulo,columnas,listaImagenes}) {
        const div = document.createElement("div");
        let templateImagenes = "";
        listaImagenes.forEach(img => {
            img.urlImagen = `${window.origin}/intranet/storage/preCotizacionImgSeccion/${img.url_imagen}`
            templateImagenes += this.generarDomSeccionImg(img).outerHTML;
        });
        if(templateImagenes === ""){     
            templateImagenes = `
            <div class="text-center contenido-vacio-img col-12">
                <span>No se agregaron imágenes para esta sección</span>
            </div>
            `
        }
        div.className = "form-group p-2";
        div.innerHTML = `
        <div class="justify-content-between align-items-center d-flex" style="gap: 5px;">
            <h6 class="mb-0 nombre-seccion">
                <i class="fas fa-caret-right"></i>
                Sección N° ${index}
            </h6>
            <div class="align-items-center d-flex" style="gap: 5px;">
                <button data-toggle="tooltip" type="button" data-placement="top" title="Número de columnas" class="btn btn-sm btn-light" id="idModalSeccion${idSeccion}columna">
                    <i class="fas fa-columns"></i>
                    <span>${columnas}</span>
                </button>
                <button class="btn btn-sm editar-seccion btn-info" data-toggle="tooltip" data-placement="top" title="Editar sección" data-seccion="${idSeccion}" type="button">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button class="btn btn-sm eliminar-seccion btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar sección" data-seccion="${idSeccion}" type="button">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
        <div class="form-group">
            <label for="idModalSeccionTitulo${idSeccion}">Título de la sección</label>
            <input required readonly id="idModalSeccionTitulo${idSeccion}" type="text" class="form-control form-control-sm" value="${titulo}">
        </div>
        <div class="form-group">
            <div class="d-flex justify-content-between flex-wrap" style="gap:5px;">
                <h6 class="text-primary mb-0">
                    <i class="fas fa-caret-right"></i>
                    Imágenes de la sección
                </h6>
                <input hidden type="file" multiple accept="image/*" id="idModalSeccionImagenes${idSeccion}" data-seccion="${idSeccion}" data-contenido="#idModalContenidoImagenes${idSeccion}">
                <button data-toggle="tooltip" data-file="#idModalSeccionImagenes${idSeccion}" data-placement="top" title="Agregar una imagen" class="btn btn-sm agregar-imagen btn-light" type="button">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div id="idModalContenidoImagenes${idSeccion}" class="form-group row">
        ${templateImagenes}
        </div>
        `
        return div;
    }
    generarDomSeccionImg({idImagen,idSeccion,urlImagen,descripcion}) {
        const div = document.createElement("div");
        div.className = "col-12 col-xl-6 form-group contenido-img";
        div.innerHTML = `
        <div class="form-group">
            <img src="${urlImagen}" loading="lazy" alt="Imagen ${descripcion}" class="img-guias">
        </div>
        <textarea class="form-control contenido-descripcion form-control-sm" rows="2" data-seccion="${idSeccion}" data-imagen="${idImagen}">${descripcion}</textarea>  
        <button class="btn btn-sm eliminar-img btn-danger" data-seccion="${idSeccion}" data-img="${idImagen}" type="button">
            <i class="fas fa-trash-alt"></i>
        </button>
        `
        return div;
    }
    sinSecciones(){
        const div = document.createElement("div");
        div.className = 'text-center contenido-vacio';
        div.innerHTML = `<span>No se agregaron secciones</span>`
        return div;
    }
    url = `${window.origin}/intranet/cotizaciones/precotizacion`
    async agregarEditarSeccion(datosFormulario,idSeccion,idPreCotizacion){
        try {
            let datos = new FormData(datosFormulario);
            datos.append("preCotizacion",idPreCotizacion);
            if(idSeccion){
                datos.append("seccion",idSeccion);
            }
            const urlSeccion = !idSeccion ? "/seccion/agregar" : "/seccion/editar" ;
            const response = await this.funcfetch(this.url + urlSeccion,datos,"POST");
            if(response.session){
                return alertify.alert([...this.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(!idSeccion){
                if(this.$contenidoSecciones.querySelector(".contenido-vacio")){
                    this.$contenidoSecciones.querySelector(".contenido-vacio").remove();
                }
                response.index = this.$contenidoSecciones.children.length + 1;
                const $contenidoNuevo = this.generarSecciones(response);
                this.$contenidoSecciones.append($contenidoNuevo);
                $contenidoNuevo.querySelector("input[type='file']").addEventListener("change",(e) => {
                    this.cargarImagen(e.target.files,idPreCotizacion,response.idSeccion);
                });
                for (const tol of $contenidoNuevo.querySelectorAll('[data-toggle="tooltip"]')) {
                    $(tol).tooltip();
                }
            }else{
                document.querySelector(`#contenidoSecciones #idModalSeccionTitulo${response.idSeccion}`).value = response.titulo;
                document.querySelector(`#contenidoSecciones #idModalSeccion${response.idSeccion}columna span`).textContent = response.columna;
            }
            $("#agregarSeccion").modal("hide");
            return alertify.success(response.success);
        } catch (error) {
            console.error(error);
            alertify.error("error al generar una nueva seccion")
        }
    }
    async cargarPreInformeEditar({idVisita,cbServicios,listaServicios,txtNoServicios,btnEliminarDocumento,documentoFormatoVisita}){
        try {
            const response = await this.funcfetch(`informe/${idVisita}`,null, "GET");
            if(response.session){
                return alertify.alert([...this.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            const informePreCotizacion = response.success;
            informePreCotizacion.secciones.forEach((seccion,key) => {
                seccion.index = key + 1;
                let $contenidoNuevo = this.generarSecciones(seccion);
                this.$contenidoSecciones.append($contenidoNuevo);
            });
            if(!this.$contenidoSecciones.innerHTML){
                this.$contenidoSecciones.innerHTML = this.sinSecciones().outerHTML;
            }
            informePreCotizacion.servicios.forEach(servicio => {
                $(cbServicios).val(servicio.id_servicios).trigger("change");
                this.seleccionarServicios(cbServicios,listaServicios,txtNoServicios);
            });
            for (const input of this.$contenidoSecciones.querySelectorAll("input[type='file']")) {
                input.addEventListener("change",(e) => {
                    this.cargarImagen(e.target.files,idVisita,e.target.dataset.seccion);
                });
            }
            for (const textarea of this.$contenidoSecciones.querySelectorAll("textarea.contenido-descripcion")) {
                textarea.addEventListener("change",(e) => {
                    this.cambioValorUnico({valor: e.target.value,idPreCotizacion:idVisita,idSeccion:e.target.dataset.seccion,idImagen:e.target.dataset.imagen});
                });
            }
            tinymce.activeEditor.setContent(!informePreCotizacion.html_primera_visita ? this.$contenidoPreInforme : informePreCotizacion.html_primera_visita);
            if(informePreCotizacion.formato_visita_pdf){
                btnEliminarDocumento.hidden = false;
                const documentoSubido = document.createElement("a");
                documentoSubido.className = "rounded-pill bg-light p-2";
                documentoSubido.id = "documento-mostrar-formato-visita";
                documentoSubido.href = window.origin + "/intranet/storage/formatoVisitas/" + informePreCotizacion.formato_visita_pdf;
                documentoSubido.target = "_blank";
                documentoSubido.textContent = informePreCotizacion.formato_visita_nombre;
                documentoFormatoVisita.insertAdjacentHTML("afterend",documentoSubido.outerHTML);
            }
            $('#modalPrimeraVisita').modal("show");
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener los datos de la pre - cotizacion")
        }
    }
    async cargarImagen(files,idPreCotizacion,idSeccion) {
        if(!files.length){
            return
        }
        try {
            const imagenes = files;
            const pattern = /image-*/;
            for (let i = 0; i < imagenes.length; i++) {
                if(!files[i].type.match(pattern)){
                    alertify.alert("Mensaje", "El archivo " + files[i].name +" no es se cargo correctamente debido a que no es una imagen");
                    continue;
                }
                let datos = new FormData();   
                datos.append("preCotizacion",idPreCotizacion);
                datos.append("seccion",idSeccion);
                datos.append("imagen",files[i]);
                let response = await this.funcfetch(this.url + "/seccion/imagen/agregar",datos,"POST");
                if(response.session){
                    return alertify.alert([...this.alertaSesion],() => {window.location.reload()});
                }
                if(response.alerta){
                    return alertify.alert("Alerta",response.alerta);
                }
                const $img = this.generarDomSeccionImg(response);
                const $contenidoImg = document.querySelector(`#idModalContenidoImagenes${idSeccion}`);
                if($contenidoImg.querySelector(".contenido-vacio-img")){
                    $contenidoImg.querySelector(".contenido-vacio-img").remove();
                }
                $contenidoImg.append($img);
                $img.querySelector("textarea.contenido-descripcion").addEventListener("change",(e) => {
                    this.cambioValorUnico({valor : e.target.value,idPreCotizacion,idSeccion,idImagen:response.idImagen});
                });
            }
            alertify.success(imagenes.length > 1 ? imagenes.length + ' imagenes cargadas correctamente' : '1 imagen cargada correctamente');
        } catch (error) {
            console.error(error);
            alertify.error("error al cargar las imagenes");
        }
    }
    async cambioValorUnico({valor,idPreCotizacion,idSeccion,idImagen}) {
        let datos = new FormData();
        datos.append("valor",valor);
        datos.append("preCotizacion",idPreCotizacion);
        datos.append("seccion",idSeccion);
        datos.append("imagen",idImagen);
        try {
            const response = await this.funcfetch(this.url + "/actualizar/datos",datos,"POST");
            if(response.session){
                return alertify.alert([...this.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al actulizar los datos");
        }
    }
    
}