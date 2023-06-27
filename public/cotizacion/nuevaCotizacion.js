function loadPage(){
    let gen = new General();
    const cbPreCotizacion = document.querySelector("#cbPreCotizacion");
    const cbClientes = document.querySelector("#idModalid_cliente");
    const cbContactos = document.querySelector("#cbContactosCliente");
    const tablaServicios = document.querySelector("#contenidoServicios");
    const tablaServicioProductos = document.querySelector("#listaServiciosProductos");
    const formCotizacion = document.querySelector("#frmCotizacion");
    let serviciosProductos = [];
    function agregarServicio(nroItem,idServicio,nombreServicio,cantidad,precioUni,descuento,total) {
        const tr = document.createElement("tr");
        tr.dataset.servicio = idServicio;
        tr.innerHTML = `
        <td>${nroItem}</td>
        <td>${nombreServicio}</td>
        <td><input type="number" value="${cantidad}" class="form-control form-control-sm cambio-detalle" data-tipo="cantidad-servicio"></td>
        <td><span class="costo-precio">${gen.monedaSoles(precioUni)}</span></td>
        <td><span class="costo-descuento">${gen.monedaSoles(descuento)}</td>
        <td><span class="costo-subtotal">${gen.monedaSoles(total)}</span></td>
        <td class="text-center"><button class="btn btn-sm btn-danger" type="button"><i class="fas fa-trash-alt"></i></button></td>
        `;
        return tr;
    }
    function limpiarCotizacion() {
        serviciosProductos = [];
        $('#idModalid_cliente').val("").trigger("change");
        cbContactos.innerHTML = "";
        $('#limpiar-frm').val("");
        tablaServicios.innerHTML = `
        <tr>
            <td colspan="100%" class="text-center">No se seleccionaron servicios</td>
        </tr>
        `
        tablaServicioProductos.innerHTML = `
        <h5 class="col-12 text-primary text-center">
            Sin productos para mostrar  
        </h5>
        `
        document.querySelector("#txtSubTotal").textContent = "S/ 0.00";
        document.querySelector("#txtDescuento").textContent = "-" + "S/ 0.00";
        document.querySelector("#txtIGV").textContent = "S/ 0.00";
        document.querySelector("#txtTotal").textContent = "S/ 0.00";
    }
    const txtDireccion = document.querySelector("#idModaldireccion");
    $(cbClientes).on("select2:select",async function(e){
        let datos = new FormData();
        datos.append("cliente",$(this).val());
        try {
            const response = await gen.funcfetch("obtener/cliente",datos, "POST");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.cliente && Object.keys(response.cliente).length){
                nuevoCliente = false;
                let template = "<option></option>";
                response.cliente.contactos.forEach(c => {
                    template += `<option value="${c.id}">${c.nombreContacto} - ${c.numeroContacto}</option>`;
                });
                cbContactos.innerHTML = template;
                txtDireccion.value = response.cliente.direccion;
            }
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener la informacion del cliente");
        }
    });
    formCotizacion.addEventListener("submit",function(e){
        e.preventDefault();
        if(!serviciosProductos.length){
            return alertify.error("la cotización debe contener al menos un servicio");
        }
        alertify.alert("Mensaje","Cotización generada con éxito",()=>window.location.reload());
    })
    function agregarServicioProductos(idServicio,nombreServicio,listaProducto) {
        const servicio = document.createElement("div");
        servicio.className = "col-12";
        servicio.dataset.domservicio = idServicio;
        let templateBody = "";
        listaProducto.forEach((p,k) => {
            templateBody += `<tr data-producto="${p.idProducto}" data-servicio="${idServicio}">
            <td>${k+1}</td>
            <td><img class="img-vistas-pequena" src="${gen.urlProductos + p.urlImagen}" alt="Imagen del producto"></td>
            <td>${p.nombreProducto}</td>
            <td><input type="number" step="0.01" value="${p.cantidadUsada}" class="form-control form-control-sm cambio-detalle" data-tipo="cantidad"></td>
            <td>${gen.monedaSoles(p.precioVenta)}</td>
            <td><input type="number" step="0.01" value="0.00" class="form-control form-control-sm cambio-detalle" data-tipo="descuento"></td>
            <td><span class="costo-subtota">${gen.monedaSoles(p.precioVenta * p.cantidadUsada)}</span></td>
            </tr>`
        });
        servicio.innerHTML = `
        <h5 class="text-primary">
            <i class="fas fa-concierge-bell"></i> ${nombreServicio}  
        </h5>
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th style="width:100px;">ITEM</th>
                        <th style="width:200px;">IMG.</th>
                        <th>PRODUCTO</th>
                        <th style="width:100px;">CANT.</th>
                        <th style="width:100px;">P.UNIT</th>
                        <th style="width:100px;">DESC.</th>
                        <th style="width:100px;">P.TOTAL</th>
                    </tr>
                </thead>
                <tbody>${templateBody == "" ? `<tr><td class="text-center" colspan="100%">No se encontraron productos</td></tr>` : templateBody}</tbody>
            </table>
        </div>`
        return servicio;
    }
    tablaServicios.addEventListener("click",function (e) {  
        if (e.target.classList.contains("btn-danger")){
            const tr = e.target.parentElement.parentElement;
            const servicio = tr.dataset.servicio;
            serviciosProductos = serviciosProductos.filter(s => s.idServicio != servicio);
            tr.remove();
            tablaServicioProductos.querySelector(`[data-domservicio="${servicio}"]`).remove();
            if (!serviciosProductos.length) {
                tablaServicios.innerHTML = `<tr>
                    <td colspan="100%" class="text-center">No se seleccionaron servicios</td>
                </tr>`;
                tablaServicioProductos.innerHTML = `
                <h5 class="col-12 text-primary text-center">
                    Sin productos para mostrar  
                </h5>
                `
            }
            calcularServiciosTotales();
        }
    });
    function modificarCantidad(e){
        const tr = e.target.parentElement.parentElement;
        const indexServicio = serviciosProductos.findIndex(s => s.idServicio == tr.dataset.servicio);
        if(indexServicio < 0){
            return alertify.error("servicio no encontrado");
        }
        let valor = e.target.step ? parseFloat(e.target.value) : parseInt(e.target.value);
        if(isNaN(valor)){
            valor = 0;
        }
        const tipo = e.target.dataset.tipo;
        if(tipo == "cantidad" || tipo == "descuento"){
            const txtSubTotal = tr.querySelector(".costo-subtota");
            const indexProducto = serviciosProductos[indexServicio].productosLista.findIndex(p => p.idProducto == tr.dataset.producto);
            if(indexProducto < 0){
                return alertify.error("producto no encontrado");
            }
            if(tipo == "cantidad-servicio"){
                serviciosProductos[indexServicio].cantidad = valor;
                return false;
            }
            const listaPro = serviciosProductos[indexServicio].productosLista[indexProducto];
            if(tipo == "cantidad"){
                listaPro.cantidad = valor;
                listaPro.importe = listaPro.pVenta * valor;
                listaPro.pTotal = (listaPro.pVenta * valor) - listaPro.descuento;
            }else if(tipo == "descuento"){
                listaPro.descuento = valor;
                listaPro.pTotal = listaPro.importe - listaPro.descuento;
                if(listaPro.importe < listaPro.descuento){
                    listaPro.descuento = 0;
                    e.target.value = "0.00";
                }
            }
            if(listaPro.pTotal < 0){
                listaPro.cantidad = 0;
                listaPro.importe = listaPro.pVenta;
                listaPro.descuento = 0;
                listaPro.pTotal = 0;
                tr.querySelector(`[data-tipo="cantidad"]`).value = "0";
                tr.querySelector(`[data-tipo="descuento"]`).value = "0.00";
            }
            txtSubTotal.textContent = gen.monedaSoles(listaPro.pTotal.toFixed(2));
            calcularServiciosTotales();
        }
    }
    function calcularServiciosTotales() {
        let subtotal = 0;
        let descuento = 0;
        let total = 0;
        serviciosProductos.forEach(cp => {
            let dsubtotal = 0;
            let ddescuento = 0;
            let dtotal = 0;
            cp.productosLista.forEach(p => {
                dsubtotal+= p.importe;
                ddescuento += p.descuento;
                dtotal += p.importe - p.descuento;
            });
            cp.pUni = dsubtotal;
            cp.descuento = ddescuento;
            cp.pTotal = dtotal;
            const tr = tablaServicios.querySelector(`[data-servicio="${cp.idServicio}"]`);
            tr.querySelector(".costo-precio").textContent = gen.monedaSoles(cp.pUni);
            tr.querySelector(".costo-descuento").textContent = "-" + gen.monedaSoles(cp.descuento);
            tr.querySelector(".costo-subtotal").textContent = gen.monedaSoles(cp.pTotal);
            subtotal += cp.pUni;
            descuento += cp.descuento;
            total += cp.pTotal;
        });
        document.querySelector("#txtSubTotal").textContent = gen.monedaSoles(subtotal);
        document.querySelector("#txtDescuento").textContent = "-" + gen.monedaSoles(descuento);
        document.querySelector("#txtIGV").textContent = gen.monedaSoles(total * 0.18);
        document.querySelector("#txtTotal").textContent = gen.monedaSoles(total);
    }
    $(cbPreCotizacion).on("select2:select", async function (e) {
        const preCotizacionId = $(this).val();
        if(preCotizacionId == "0"){
            limpiarCotizacion();
            return false;
        }
        try {
            serviciosProductos = [];
            const response = await gen.funcfetch("obtener/precotizacion/" + preCotizacionId, null, "GET");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            for (const key in response.preCotizacion) {
                if (Object.hasOwnProperty.call(response.preCotizacion, key)) {
                    const valor = response.preCotizacion[key];
                    const dom = document.querySelector("#idModal" + key);
                    if(key == "contactos"){
                        let template = "<option></option>";
                        valor.forEach(c => {
                            template += `<option value="${c.id}">${c.nombreContacto} - ${c.numeroContacto}</option>`;
                        });
                        cbContactos.innerHTML = template;
                        continue;
                    }
                    if(key == "servicios"){
                        tablaServicios.innerHTML = valor.length ? "" : `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios<td></tr>`;
                        tablaServicioProductos.innerHTML = valor.length ? "" : `<h5 class="col-12 text-primary text-center">Sin productos para mostrar</h5>`
                        valor.forEach((s,k) => {
                            let total = 0;
                            let productosLista = []; 
                            s.productos.forEach(p => {
                                total += p.precioVenta * p.cantidadUsada;
                                productosLista.push({
                                    idProducto : p.idProducto,
                                    cantidad : p.cantidadUsada,
                                    pVenta : p.precioVenta,
                                    importe : p.precioVenta * p.cantidadUsada,
                                    descuento : 0,
                                    pTotal : p.precioVenta * p.cantidadUsada
                                })
                            });
                            serviciosProductos.push({
                                idServicio : s.id,
                                cantidad : 1,
                                pUni : total,
                                descuento: 0,
                                pTotal : total,
                                productosLista
                            });
                            tablaServicios.append(agregarServicio(k+1,s.id,s.servicio,1,total,0,total));
                            tablaServicioProductos.append(agregarServicioProductos(s.id,s.servicio,s.productos));
                        });
                        for (const cambio of tablaServicioProductos.querySelectorAll(".cambio-detalle")) {
                            cambio.addEventListener("change", modificarCantidad);
                        }
                        calcularServiciosTotales();
                        continue;
                    }
                    if(!dom){
                        continue;
                    }
                    dom.value = valor;
                }
            }
            $('.select2-simple').trigger("change");
            // if (indexProducto < 0) {
            //     if (response.session) {
            //         return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
            //     }
            //     if (response.alerta) {
            //         return alertify.alert("Alerta", response.alerta);
            //     }
            //     if (response.producto && indexProducto < 0) {
            //         let precioVe = isNaN(parseFloat(response.producto.precioVenta)) ? 0.00 : parseFloat(response.producto.precioVenta);
            //         if (!swtichProductoMenor.checked) {
            //             precioVe = isNaN(parseFloat(response.producto.precioVentaPorMayor)) ? 0.00 : parseFloat(response.producto.precioVentaPorMayor);
            //             if (precioVe == 0) {
            //                 $('#productoBuscar').val("").trigger("change");
            //                 return alertify.alert("Mensaje", "El producto <strong>" + response.producto.nombreProducto + "</strong> no cuenta con un precio de venta al por mayor o el valor es igual a S/ 0.00");
            //             }
            //         }
            //         if (!listaProductos.length) {
            //             tablaDetalleVenta.innerHTML = "";
            //         }
            //         let detalleTr = agregarDetalleVenta(tablaDetalleVenta.children.length + 1, response.producto.id, response.producto.urlProductos, response.producto.nombreProducto, precioVe);
            //         tablaDetalleVenta.append(detalleTr);
            //         listaProductos.push({
            //             idProducto: response.producto.id,
            //             precio: precioVe,
            //             descuento: 0,
            //             cantidad: 1,
            //             igv: response.producto.igv,
            //             subtotal: precioVe
            //         });
            //         for (const cambio of detalleTr.querySelectorAll(".cambio-detalle")) {
            //             cambio.addEventListener("change", modificarCantidad);
            //         }
            //     }
            //     alertify.success("producto agregado");
            // } else {
            //     const trDetalle = tablaDetalleVenta.querySelector(`[data-producto="${productoId}"][data-costo="${costo}"]`);
            //     if(!trDetalle){
            //         return alertify.error("no se encontró el producto");
            //     }
            //     listaProductos[indexProducto].cantidad++;
            //     listaProductos[indexProducto].subtotal = (listaProductos[indexProducto].cantidad * listaProductos[indexProducto].precio) - listaProductos[indexProducto].descuento;
            //     trDetalle.querySelector(".cambio-detalle").value = listaProductos[indexProducto].cantidad;
            //     trDetalle.querySelector(".data-precio-importe").textContent = gen.resetearMoneda(listaProductos[indexProducto].subtotal);
            // }
            // sumarTotalDetalle();
            // $(cbBuscarProducto).val("").trigger("change");
        } catch (error) {
            alertify.error("error al obtener la pre - cotizacion");
            console.error(error);
        }
    });
    return false;
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