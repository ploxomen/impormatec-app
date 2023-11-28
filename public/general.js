class General{
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    idPais = 165
    alertaSesion = ["Alerta","La sesión a caducado, favor inicie sesión nuevamente"];
    urlDescargarDocumentos = window.origin + "/descargar";
    requestJson = {
        'X-CSRF-TOKEN': this.token,
        'X-Requested-With': 'XMLHttpRequest'
    }
    creacionDOM(etiqueta,atributos,adicionales){
        const dom = document.createElement(etiqueta);
        for (const key in atributos) {
            if (Object.hasOwnProperty.call(atributos, key)) {
                const valor = atributos[key];
                dom.setAttribute(key,valor);
            }
        }
        for (const key in adicionales) {
            if (Object.hasOwnProperty.call(adicionales, key)) {
                const valor = adicionales[key];
                dom[key] = valor;
            }
        }
        return dom;
    }
    claseSpinner = "fas fa-spinner fa-spin";
    funcfetch(url,dato,metodo = "POST"){
        return fetch(url,{
            headers: {
                'X-CSRF-TOKEN' : this.token,
                'X-Requested-With' : 'XMLHttpRequest'
            },
            method: metodo,
            body: dato
        }).then(response => response.json())
    }
    validarVacio(dato) {
        return !dato ? "Sin Registro" : dato;
    }
    $contenidoPreInforme = `
    <ol>
        <li>
            <p>
            <strong>Tipo de sistema</strong>
            <br><br>
            </p>
        </li>
        <li>
            <p>
            <strong>Marca, cantidad y modelo de equipos</strong>
            <br><br>
            </p>
        </li>
        <li>
            <p>
            <strong>Descripción del estado actual</strong>
            <br><br>
            </p>
        </li>
        <li>
            <p>
            <strong>Detalle de accesorios, partes o repuestos por cambio[indicar el tipo de materiales de ser necesario].</strong>
            <br><br>
            </p>
        </li>
        <li>
            <p>
            <strong>Conclusiones</strong>
            <br><br>
            </p>
        </li>
        <li>
            <p>
            <strong>Recomendaciones</strong>
            <br><br>
            </p>
        </li>
    </ol>
    `
    banerLoader = document.querySelector("#banerCargando")
    seleccionarCheckbox(claseSeleccionar,$selecionarTodo){
        let cantidadClase = 0;
        const claseSeleccionar2 = document.querySelectorAll(claseSeleccionar);
        for (const selecion of claseSeleccionar2) {
            if (selecion.parentElement.parentElement.parentElement.classList.contains("d-none")) {
                continue;
            }
            cantidadClase++;
        }
        const claseSelecionada = document.querySelectorAll(claseSeleccionar+":checked");
        $selecionarTodo.checked = cantidadClase > 0 && cantidadClase == claseSelecionada.length ? true : false;
        return claseSelecionada.length;
    }
    cargandoPeticion($boton,claseIcono,deshabilitar){
        const btn = $boton.querySelector("i");
        if(deshabilitar){
            $boton.setAttribute("disabled","disabled");
        }else{
            $boton.removeAttribute("disabled");
        }
        btn.className = claseIcono;
    }
    aumentarDisminuir(e){
        const $numero = document.querySelector(e.target.dataset.number);
        const cantidad = isNaN(parseFloat($numero.step)) ? 1 : parseFloat($numero.step);
        let valor = isNaN(parseFloat($numero.value)) ? 0 : parseFloat($numero.value);
        const fixed = cantidad === 1 ? 0 : cantidad.toString().split(".")[1].length;
        const minimo = isNaN(parseFloat($numero.min)) ? Number.NEGATIVE_INFINITY : parseFloat($numero.min);
        const maximo = isNaN(parseFloat($numero.max)) ? Number.POSITIVE_INFINITY : parseFloat($numero.max);
        if(e.target.dataset.accion == "aumentar" && valor <= maximo){
            $numero.value =  (valor + cantidad).toFixed(fixed);
        }else if(e.target.dataset.accion == "disminuir" && valor > minimo){
            $numero.value =  (valor - cantidad).toFixed(fixed);
        }
    }
    urlVentaComprobante = window.origin + "/intranet/ventas/comprobante/";
    urlCotizacionComprobante = window.origin + "/intranet/cotizaciones/comprobante/";
    urlProductos = window.location.origin + "/intranet/storage/productos/"
    resetearMoneda(numero,tipo){
        const newNum = isNaN(parseFloat(numero)) ? 0 : parseFloat(numero);
        return newNum.toLocaleString(tipo === 'USD' ? 'en-US' : 'es-PE',{
            style: 'currency',
            currency: tipo,
        })
    }
    abrirPesatana(url){
        const a = document.createElement("a");
        a.href = url;
        a.target = "_blank";
        document.body.append(a);
        a.click();
        document.body.removeChild(a);
    }
    switchs(e){
        const label = e.target.parentElement.querySelector("label");
        label.textContent = e.target.checked ? e.target.dataset.selected : e.target.dataset.noselected;
    }
    monedaSoles(numero){
        return parseFloat(numero).toLocaleString("es-PE",{
            style: "currency",
            currency: "PEN"
        })
    }
    obtenerNombresMes(numeroMes) {
        const mes = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
        return mes[numeroMes - 1];
    }
    contenidoHistorialSeguimiento({descripcion,fechaCreadaFormato,porcentaje,nombreUsuario}){
        const div = document.createElement("div");
        div.className = "form-group border p-2";
        div.innerHTML = `
            <div class="d-flex justify-content-between flex-wrap" style="gap:5px;">
                <div class="d-inline-block">
                    <b>Reponsable: </b>
                    <span>${nombreUsuario}</span>
                </div>
                <div class="d-inline-block">
                    <b>Fecha Hr.: </b>
                    <span>${fechaCreadaFormato}</span>
                </div>
                <div class="d-inline-block">
                    <b>Porcentaje (%): </b>
                    <span>${porcentaje}</span>
                </div>
            </div>
            <p><b>Descripción:</b><br>${descripcion}</p>
        `
        return div;
    }
    contenidoHistorialSeguimientoEditar({id,descripcion,fechaCreadaFormato,porcentaje,nombreUsuario}){
        const div = document.createElement("div");
        div.className = "form-group border p-2";
        div.innerHTML = `
            <div class="form-row">
                <div class="form-group text-center col-12">
                    <button type="button" class="btn btn-sm btn-light" data-seguimiento="${id}">
                        <i class="fas fa-trash-alt"></i>
                        <span>Eliminar seguimiento</span>
                    </button>
                </div>
                <div class="form-group col-12">
                    <input type="hidden" value="${id}" name="seguimiento[]" />
                    <label for="txtEditarResponsable${id}">Responsable</label>
                    <input type="text" id="txtEditarResponsable${id}" required disabled class="form-control form-control-sm" value="${nombreUsuario}"/>
                </div>
                <div class="form-group col-12 col-md-8">
                    <label for="id="txtEditarFechaHr${id}"">Fecha Hr.</label>
                    <input type="text" required disabled class="form-control form-control-sm" value="${fechaCreadaFormato}" id="txtEditarFechaHr${id}"/>
                </div>
                <div class="form-group col-12 col-md-4">
                    <label for="txtEditarPorcentaje${id}">Porcentaje</label>
                    <input type="number" required name="porcentaje[]" min="1" max="100" step="0.01"  class="form-control form-control-sm" value="${porcentaje}" id="txtEditarPorcentaje${id}"/>
                </div>
                <div class="form-group col-12">
                    <label for="txtEditarDescripcion${id}">Descripción</label>
                    <textarea class="form-control form-control-sm" required name="descripcion[]" maxlength="500" id="txtEditarDescripcion${id}" rows="4">${descripcion}</textarea>
                </div>
            </div>
        `
        return div;
    }
    enviarNotificacionWhatsApp(numero,texto){
        let whatsappLink = 'https://api.whatsapp.com/send?phone=' + encodeURIComponent(numero) + '&text=' + encodeURIComponent(texto);
        window.open(whatsappLink);
    }
    seleccionarServicios(cbJq,frmServicios,txtNoServicios){
        if(cbJq.val() == ""){
            return
        }
        const cb = cbJq[0];
        const optionCb = cb.options[cb.selectedIndex];
        if(!optionCb){
            return 
        }
        const div = document.createElement("div");
        div.className = "contenido rounded-pill bg-light p-2";
        div.innerHTML = `
        <input type="hidden" value="${cbJq.val()}" name="servicios[]">
        <span>${optionCb.textContent}</span>
        <button type="button" class="btn btn-sm p-1" data-valor="${cbJq.val()}"><i class="fas fa-trash-alt"></i></button>
        `
        frmServicios.append(div);
        if(frmServicios.children.length >= 1){
            txtNoServicios.hidden = true;
        }
        optionCb.disabled = true;
        cbJq.val("").trigger("change");
    }
    urlImagenesPreCotizacion = window.location.origin + "/intranet/storage/imgCotizacionPre/";
    
}