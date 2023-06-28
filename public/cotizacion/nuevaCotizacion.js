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
        cbServiciosOpt(false,null);
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
    const btnAgregarCoti = document.querySelector("#btnAgregarCotizacion");
    formCotizacion.addEventListener("submit",async function(e){
        e.preventDefault();
        if(!serviciosProductos.length){
            return alertify.error("la cotización debe contener al menos un servicio");
        }
        let datos = new FormData(this);
        if(!datos.has('id_cliente')){
            datos.append('id_cliente',$(cbClientes).val());
        }
        datos.append("servicios",JSON.stringify(serviciosProductos));
        gen.cargandoPeticion(btnAgregarCoti, gen.claseSpinner, true);
        try {
            const response = await gen.funcfetch("agregar",datos,"POST");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            return alertify.alert("Mensaje",response.success,() => window.location.reload());
        } catch (error) {
            console.error(error);
            alertify.error("error al generar una cotización");
        }finally{
            gen.cargandoPeticion(btnAgregarCoti, 'fas fa-plus', false);
        }
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
            cbServiciosOpt(false,[+servicio]);
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
            alertify.success("servicio eliminado correctamente")
            calcularServiciosTotales();
        }
    });
    function modificarCantidad(e){
        console.log(e);
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
        if(tipo == "cantidad-servicio"){
            serviciosProductos[indexServicio].cantidad = valor;
            return false
        }
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
    let cbServicios = document.querySelector("#cbServicios");
    function cbServiciosOpt(disabled,arrayServicios) {
        for (const cb of cbServicios.querySelectorAll("option")) {
            if(isNaN(+cb.value)){
                continue;
            }
            if(!arrayServicios){
                cb.disabled = false;
                continue;
            }
            if(arrayServicios.indexOf(+cb.value) >= 0){
                cb.disabled = disabled;
            }
        }
    }
    $(cbServicios).on("select2:select", async function (e) {
        const idServico = $(this).val();
        try {
            const response = await gen.funcfetch("obtener/servicio/" + idServico, null, "GET");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            const servicioJSON = response.servicio;
            let total = 0;
            let productosLista = []; 
            servicioJSON.productos.forEach(p => {
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
            if(!serviciosProductos.length){
                tablaServicios.innerHTML = "";
                tablaServicioProductos.innerHTML = "";
            }
            serviciosProductos.push({
                idServicio : servicioJSON.id,
                cantidad : 1,
                pUni : total,
                descuento: 0,
                pTotal : total,
                productosLista
            });
            tablaServicios.append(agregarServicio(tablaServicios.children.length + 1,servicioJSON.id,servicioJSON.servicio,1,total,0,total));
            tablaServicioProductos.append(agregarServicioProductos(servicioJSON.id,servicioJSON.servicio,servicioJSON.productos));
            for (const cambio of formCotizacion.querySelectorAll(".cambio-detalle")) {
                cambio.removeEventListener("change", modificarCantidad);
                cambio.addEventListener("change", modificarCantidad);
            }
            cbServiciosOpt(true,[servicioJSON.id]);
            $(cbServicios).val("").trigger("change");
            calcularServiciosTotales();
            alertify.success("servicio agregado correctamente");
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener el servicio");
        }
    });
    $(cbPreCotizacion).on("select2:select", async function (e) {
        const preCotizacionId = $(this).val();
        $(cbClientes).prop("disabled",false);
        if(preCotizacionId == "ninguno"){
            limpiarCotizacion();
            return false;
        }
        try {
            $(cbClientes).prop("disabled",true);
            cbServiciosOpt(false,null);
            serviciosProductos = [];
            const response = await gen.funcfetch("obtener/precotizacion/" + preCotizacionId, null, "GET");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            let serviciosCb = [];
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
                            serviciosCb.push(s.id);
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
                        for (const cambio of formCotizacion.querySelectorAll(".cambio-detalle")) {
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
            cbServiciosOpt(true,serviciosCb);
            $(cbServicios).val("").trigger("change");
            $('.select2-simple').trigger("change");
        } catch (error) {
            alertify.error("error al obtener la pre - cotizacion");
            console.error(error);
        }
    });
}
window.addEventListener("DOMContentLoaded",loadPage);