class General{
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    alertaSesion = ["Alerta","La sesión a caducado, favor inicie sesión nuevamente"];
    /*
    if(response.session){
        return alertify.alert([...alertaSesion],() => {window.location.reload()});
    }
    */ 
    requestJson = {
        'X-CSRF-TOKEN': this.token,
        'X-Requested-With': 'XMLHttpRequest'
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
    resetearMoneda(numero){
        const newNum = isNaN(parseFloat(numero)) ? 0 : parseFloat(numero);
        return newNum.toLocaleString('es-PE',{
            style: 'currency',
            currency: 'PEN',
        })
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
}