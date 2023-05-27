function loadPage(){
    let gen = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change", gen.switchs);
    }
    const formatoListaProductos = function (producto) {
        if (!producto.id) {
            return producto.text;
        }
        let urlProducto = window.location.origin + "/intranet/storage/productos/" + producto.element.dataset.url;
        let precioVenta = isNaN(parseFloat(producto.element.dataset.venta)) ? "No establecido" : gen.monedaSoles(producto.element.dataset.venta);
        let precioVentaMayor = isNaN(parseFloat(producto.element.dataset.ventaMayor)) ? "No establecido" : gen.monedaSoles(producto.element.dataset.ventaMayor);
        let $producto = $(
            `<div class="d-flex" style="gap:5px;">
                <div>
                    <img src="${urlProducto}" width="60px" height="60px" class="select2-img">
                </div>
                <div>
                    <p class="mb-0" style="font-size: 0.8rem;">
                        <span>${producto.text}</span><br>
                        <span><b>Precio Venta:</b> ${precioVenta}</span><br>
                        <span><b>Precio Venta Mayor:</b> ${precioVentaMayor}</span>
                    </p>
                </div>
                
            </div>`
        );
        return $producto;
    }
    function matchCustom(params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if (typeof data.text === 'undefined') {
            return null;
        }
        if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1 || (data.element.dataset.codigo && data.element.dataset.codigo.indexOf(params.term) > -1)) {
            return $.extend({}, data, true);
        }
        return null;
    }
    let listaProductos = [];
    let swtichProductoMenor = document.querySelector("#idVentaPorMenor");
    let cbBuscarProducto = document.querySelector("#productoBuscar");
    let tablaDetalleVenta = document.querySelector("#tablaDetalleVenta tbody");
    let envioVenta = document.querySelector("#idVentaEnvio");
    envioVenta.addEventListener("change",function(e){
        let envio = isNaN(parseFloat(this.value)) ? 0.00 : parseFloat(this.value);
        if (envio < 0){
            envio = 0;
        }
        this.value = envio.toFixed(2);
        document.querySelector("#tDetalleEnvio").textContent = gen.monedaSoles(envio);
        sumarTotalDetalle();
    });
    $(cbBuscarProducto).select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: "Busque y seleccione un producto",
        templateResult: formatoListaProductos,
        matcher: matchCustom
    }).on("select2:select", async function (e) {
        try {
            const productoId = $(this).val();
            const opcionCb = cbBuscarProducto.options[cbBuscarProducto.selectedIndex];
            const costo = swtichProductoMenor.checked ? parseFloat(opcionCb.dataset.venta) : parseFloat(opcionCb.dataset.ventaMayor);
            console.log(listaProductos, costo);
            const indexProducto = listaProductos.findIndex(p => p.idProducto == productoId && p.precio == costo);
            if (indexProducto < 0) {
                const response = await gen.funcfetch("listar/producto/" + productoId, null, "GET");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                if (response.alerta) {
                    return alertify.alert("Alerta", response.alerta);
                }
                if (response.producto && indexProducto < 0) {
                    let precioVe = isNaN(parseFloat(response.producto.precioVenta)) ? 0.00 : parseFloat(response.producto.precioVenta);
                    if (!swtichProductoMenor.checked) {
                        precioVe = isNaN(parseFloat(response.producto.precioVentaPorMayor)) ? 0.00 : parseFloat(response.producto.precioVentaPorMayor);
                        if (precioVe == 0) {
                            $('#productoBuscar').val("").trigger("change");
                            return alertify.alert("Mensaje", "El producto <strong>" + response.producto.nombreProducto + "</strong> no cuenta con un precio de venta al por mayor o el valor es igual a S/ 0.00");
                        }
                    }
                    if (!listaProductos.length) {
                        tablaDetalleVenta.innerHTML = "";
                    }
                    let detalleTr = agregarDetalleVenta(tablaDetalleVenta.children.length + 1, response.producto.id, response.producto.urlProductos, response.producto.nombreProducto, precioVe);
                    tablaDetalleVenta.append(detalleTr);
                    listaProductos.push({
                        idProducto: response.producto.id,
                        precio: precioVe,
                        descuento: 0,
                        cantidad: 1,
                        igv: response.producto.igv,
                        subtotal: precioVe
                    });
                    for (const cambio of detalleTr.querySelectorAll(".cambio-detalle")) {
                        cambio.addEventListener("change", modificarCantidad);
                    }
                }
                alertify.success("producto agregado");
            } else {
                const trDetalle = tablaDetalleVenta.querySelector(`[data-producto="${productoId}"][data-costo="${costo}"]`);
                if(!trDetalle){
                    return alertify.error("no se encontró el producto");
                }
                listaProductos[indexProducto].cantidad++;
                listaProductos[indexProducto].subtotal = (listaProductos[indexProducto].cantidad * listaProductos[indexProducto].precio) - listaProductos[indexProducto].descuento;
                trDetalle.querySelector(".cambio-detalle").value = listaProductos[indexProducto].cantidad;
                trDetalle.querySelector(".data-precio-importe").textContent = gen.resetearMoneda(listaProductos[indexProducto].subtotal);
            }
            sumarTotalDetalle();
            $(cbBuscarProducto).val("").trigger("change");
        } catch (error) {
            alertify.error("error al obtener el producto");
            console.error(error);
        }
    });
    function agregarDetalleVenta(indice, idProducto, urlImagenProducto, nombreProducto, precio){
        let tr = document.createElement("tr");
        tr.dataset.producto = idProducto;
        tr.dataset.costo = precio;
        tr.innerHTML = `
        <td>${indice}</td>
        <td><img src="${urlImagenProducto}" class="tdimagen-producto" /></td>
        <td>${nombreProducto}</td>
        <td>${gen.resetearMoneda(precio)}</td>
        <td><input type="number" min="1" data-tipo="cantidad" class="form-control form-control-sm cambio-detalle" value="1"/></td>
        <td><input type="number" min="0" data-tipo="descuento" step="0.01" class="form-control form-control-sm cambio-detalle" value="0.00"/></td>
        <td><span class="data-precio-importe">${gen.resetearMoneda(precio)}</span></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i> <span>Elimar</span></button></td>
        `
        return tr;
    }
    function sumarTotalDetalle() {
        let igv = 0, total = 0, descuento = 0, envio = isNaN(parseFloat(envioVenta.value)) ? 0.00 : parseFloat(envioVenta.value);
        listaProductos.forEach(dv => {
            descuento += dv.descuento;
            total += dv.subtotal;
            igv += !dv.igv ? 0 : (dv.subtotal * 0.18);
        });
        totales = (total + envio) - descuento;
        document.querySelector("#tDetalleSubTotal").textContent = gen.resetearMoneda((total - igv).toFixed(2));
        document.querySelector("#tDetalleIgv").textContent = gen.resetearMoneda(igv.toFixed(2));
        document.querySelector("#tDetalleDescuento").textContent = "- " + gen.resetearMoneda(descuento.toFixed(2));
        document.querySelector("#tDetalleTotal").textContent = gen.resetearMoneda(totales.toFixed(2));
    }
    function modificarCantidad(e) {
        const tr = e.target.parentElement.parentElement;
        const costo = parseFloat(tr.dataset.costo);
        const indexProducto = listaProductos.findIndex(p => p.idProducto == tr.dataset.producto && p.precio == costo);
        if (indexProducto < 0) {
            alertify.error("producto no encontrado");
        }
        let valor = e.target.step ? parseFloat(e.target.value) : parseInt(e.target.value);
        if (isNaN(valor)) {
            valor = 1;
        }
        const txtSubTotal = tr.querySelector(".data-precio-importe");

        switch (e.target.dataset.tipo) {
            case 'cantidad':
                listaProductos[indexProducto].cantidad = valor;
                listaProductos[indexProducto].subtotal = listaProductos[indexProducto].precio * valor;
                break;
            case 'descuento':
                listaProductos[indexProducto].descuento = valor;
                listaProductos[indexProducto].subtotal = listaProductos[indexProducto].precio * listaProductos[indexProducto].cantidad;
                if (listaProductos[indexProducto].subtotal < listaProductos[indexProducto].descuento) {
                    listaProductos[indexProducto].descuento = 0;
                    e.target.value = "0.00";
                }
                break;
        }
        if (listaProductos[indexProducto].subtotal < 0) {
            listaProductos[indexProducto].cantidad = 1;
            listaProductos[indexProducto].subtotal = listaProductos[indexProducto].precio;
            listaProductos[indexProducto].descuento = 0;
            tr.querySelector(`[data-tipo="cantidad"]`).value = "1";
            tr.querySelector(`[data-tipo="descuento"]`).value = "0.00";
        }
        txtSubTotal.textContent = gen.monedaSoles(listaProductos[indexProducto].subtotal.toFixed(2));
        sumarTotalDetalle();
    }
    tablaDetalleVenta.addEventListener("click", function (e) {
        if (e.target.classList.contains("btn-outline-danger")) {
            const tr = e.target.parentElement.parentElement;
            const producto = tr.dataset.producto;
            const costo = tr.dataset.costo
            listaProductos = listaProductos.filter(p => {
                if (p.idProducto === parseInt(producto)) {
                    if (p.precio != costo) {
                        return p;
                    }
                    return
                }
                return p;
            });
            tr.remove();
            if (!listaProductos.length) {
                tablaDetalleVenta.innerHTML = `<tr>
                    <td colspan="100%" class="text-center">No se seleccionó ningún producto</td>
                </tr>`;
            }
            sumarTotalDetalle();
        }
    });
    document.querySelector("#generarCotizacion").addEventListener("submit", async function (e) {
        e.preventDefault();
        if (!listaProductos.length) {
            return alertify.alert("Mensaje", "Para registrar una cotización debe haber al menos un producto");
        }
        let datos = new FormData(this);
        datos.append("lisProductos", JSON.stringify(listaProductos));
        try {
            let response = await gen.funcfetch("registrar", datos);
            if (response.session) {
                return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
            }
            if (response.alerta) {
                return alertify.alert("Alerta", response.alerta);
            }
            if (response.fueraStock) {
                let alertasFueraStock = "";
                alertasFueraStock.forEach(fe => {
                    alertasFueraStock += `
                    <div class="alert alert-warning" role="alert">
                        <div class="row">
                            <div class="form-control col-12">
                                <b>Producto: </b>
                                <span>${fe.producto}</span>
                            </div>
                            <div class="form-control col-12 col-md-6">
                                <b>Cantidad máxima: </b>
                                <span>${fe.cantidadMaxima}</span>
                            </div>
                            <div class="form-control col-12 col-md-6">
                                <b>Cantidad cotizada: </b>
                                <span>${fe.cantidad}</span>
                            </div>
                        </div>
                    </div>
                    `
                });
                return alertify.alert("Alerta", "Los siguientes productos superan la cantidad máxima, por favor incremente la cantidad máxima de los productos o disminuya la cantidad en la cotización " + alertasFueraStock);
            }
            if (response.success) {
                return alertify.confirm("Mensaje", "Cotización registrada correctamente. <br><strong>¿Deseas ver el comprobante?</strong>", () => {
                    window.open(gen.urlCotizacionComprobante + response.success, "_blank");
                    window.location.reload();
                }, () => { window.location.reload() });
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al generar una nueva cotización");
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);