class PreCotizacion {
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
                <button data-toggle="tooltip" data-placement="top" title="Número de columnas" class="btn btn-sm btn-light" id="idModalSeccion$${idSeccion}Columna">
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
                <input hidden type="file" multiple name="imagenSeccion" accept="image/*" id="idModalSeccionImagenes${idSeccion}" data-seccion="${idSeccion}" data-contenido="#idModalContenidoImagenes${idSeccion}">
                <button data-toggle="tooltip" data-file="#idModalSeccionAgregarImagen${idSeccion}" data-placement="top" title="Agregar una imagen" class="btn btn-sm agregar-imagen btn-light" type="button">
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
}