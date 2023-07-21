class Cotizacion extends General{
    comboListaAlmacenes(listaAlmacenes){
        const cb = document.createElement("select");
        cb.className = "form-control form-control-sm";
        let template = "<option></option>";
        listaAlmacenes.forEach(almacen => {
            template += `<option value="${almacen.idAlmacen}">${almacen.nombreAlmacen}</option>`
        });
        cb.innerHTML = template;
        return cb;
    }
    almacenProductos({index,idProducto,urlImagen,nombreProducto,listaAlmacenes,cantidadUsada}){
        const tr = document.createElement("tr");
        tr.dataset.producto = idProducto;
        tr.innerHTML =`
            <td>${index}</td>
            <td><img class="img-vistas-pequena" src="${urlImagen}" alt="Imagen del producto"></td>
            <td>${nombreProducto}</td>
            <td>${cantidadUsada}</td>
            <td>${this.comboListaAlmacenes(listaAlmacenes).outerHTML}</td>        
        `
        return tr;
    }
    almacenServicios({id,servicio,productos}){
        const li = document.createElement("li");
        li.dataset.servicio = id;
        li.innerHTML = `<i class="fas fa-concierge-bell"></i><span class="ml-1">${servicio}</span>`;
        const tablaResponsive = document.createElement("div");
        tablaResponsive.className = "table-responsive";
        const tablaProductos = document.createElement("table");
        tablaProductos.className = "table table-sm table-bordered";
        tablaProductos.innerHTML = `<thead>
        <tr>
            <th>ITEM</th>
            <th>IMAGEN</th>
            <th>DESCRIPCION</th>
            <th>CANT.</th>
            <th>ALMACEN</th>
        </tr>
        </thead>`;
        const tbodyProductos = document.createElement("tbody");
        if(!productos.length){
            tbodyProductos.innerHTML = `
            <tr>
                <td class="text-center" colspan="100%">No se encontraron productos</td>
            </tr>
            `
        }
        productos.forEach((producto,key) => {
            producto.index = key + 1;
            tbodyProductos.append(this.almacenProductos(producto));
        });
        tablaProductos.append(tbodyProductos);
        tablaResponsive.append(tablaProductos);
        li.append(tablaResponsive);
        return li;
    }
    resultadosAlmacenServicio($tablaServiciosProductos){
        let resultado = [];
        for (const li of $tablaServiciosProductos.children) {
            let productos = [];
            for (const tr of li.querySelector("tbody").children) {
                productos.push({
                    idProducto : tr.dataset.producto,
                    idAlmacen : tr.querySelector("select").value
                });
            }
            resultado.push({
                idServicio : li.dataset.servicio,
                productos
            });
        }
        return resultado;
    }
    filaProducto({idProducto,idServicio,index,urlImagen,nombreProducto,cantidadUsada,precioVenta,precioTotal,tipo = "nuevo"}){
        const tr = document.createElement("tr");
        tr.dataset.producto = idProducto;
        tr.dataset.servicio = idServicio;
        tr.innerHTML =`
            <td>${index}</td>
            <td><img class="img-vistas-pequena" src="${urlImagen }" alt="Imagen del producto"></td>
            <td>${nombreProducto}</td>
            <td><input type="number" step="0.01" value="${cantidadUsada}" class="form-control form-control-sm cambio-detalle" data-tipo="cantidad"></td>
            <td><input type="number" step="0.01" value="${precioVenta}" class="form-control form-control-sm cambio-detalle" data-tipo="precioVenta"></td>
            <td><input type="number" step="0.01" value="0.00" class="form-control form-control-sm cambio-detalle" data-tipo="descuento"></td>
            <td><span class="costo-subtota">${this.resetearMoneda(precioTotal)}</span></td>
            <td class="text-center"><button type="button" data-tipo="${tipo}" class="btn btn-sm btn-danger p-2" data-cbproducto="servicioProductoLista${
            idServicio}"><i class="fas fa-trash-alt"></i></button></td>        
        `
        return tr;
    }
    agregarServicioProductos(idServicio,nombreServicio,listaProducto,tipo) {
        const servicio = document.createElement("div");
        servicio.className = "col-12";
        servicio.dataset.domservicio = idServicio;
        let templateBody = "";
        listaProducto.forEach((p,k) => {
            p.index = k + 1;
            p.precioTotal = p.precioVenta * p.cantidadUsada;
            p.urlImagen = this.urlProductos + p.urlImagen;
            p.idServicio = idServicio;
            p.tipo = tipo;
            templateBody += this.filaProducto(p).outerHTML;
        });
        const cbClonadoProductos = document.querySelector("#cbProductos").cloneNode(true);
        for (const opt of cbClonadoProductos.querySelectorAll("option")) {
            opt.disabled = listaProducto.findIndex( p => p.idProducto == opt.value) >= 0 ? true : false;
        }
        cbClonadoProductos.setAttribute("id","servicioProductoLista" + idServicio);
        cbClonadoProductos.hidden = false;
        cbClonadoProductos.className = "form-control cb-servicios-productos";
        cbClonadoProductos.setAttribute("data-tabla-productos",`tablaBodyServiciosProductos${idServicio}`);
        cbClonadoProductos.setAttribute("data-servicio",`${idServicio}`);
        servicio.innerHTML = `
        <div class="d-flex justify-content-between">
            <h5 class="text-primary">
                <i class="fas fa-concierge-bell"></i> ${nombreServicio}  
            </h5>
            <div class="form-group">
                <label>Productos</label>
                ${cbClonadoProductos.outerHTML}
            </div>
        </div>
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
                        <th style="width:50px;">ELIMINAR</th>
                    </tr>
                </thead>
                <tbody
                id="tablaBodyServiciosProductos${idServicio}">${templateBody == "" ? `<tr><td class="text-center" colspan="100%">No se encontraron productos</td></tr>` : templateBody}</tbody>
            </table>
        </div>`
        return servicio;
    }
    agregarServicio(nroItem,idServicio,nombreServicio,cantidad,precioUni,descuento,total,tipo="nuevo") {
        const tr = document.createElement("tr");
        tr.dataset.servicio = idServicio;
        tr.innerHTML = `
        <td>${nroItem}</td>
        <td>${nombreServicio}</td>
        <td><input type="number" value="${cantidad}" class="form-control form-control-sm cambio-detalle" data-tipo="cantidad-servicio"></td>
        <td><span class="costo-precio">${this.monedaSoles(precioUni)}</span></td>
        <td><span class="costo-descuento">${this.monedaSoles(descuento)}</td>
        <td><span class="costo-subtotal">${this.monedaSoles(total)}</span></td>
        <td class="text-center"><button class="btn btn-sm btn-danger" data-tipo="${tipo}" type="button"><i class="fas fa-trash-alt"></i></button></td>
        `;
        return tr;
    }
    asignarListaServiciosProductos(servicio){
        let productosLista = []; 
        servicio.productos.forEach(p => {
            const descuento = !p.descuentoProducto ? 0  : p.descuentoProducto;
            productosLista.push({
                idProducto : p.idProducto,
                cantidad : p.cantidadUsada,
                pVenta : p.precioVenta,
                importe : p.precioVenta * p.cantidadUsada,
                descuento : descuento,
                pTotal : p.precioVenta * p.cantidadUsada - descuento
            })
        });
        return {
            idServicio : servicio.id_servicio,
            cantidad : servicio.cantidad,
            pUni : servicio.costo,
            descuento: servicio.descuento,
            pTotal : servicio.total,
            productosLista
        };
    }
    async obtenerProducto($cb,serviciosProductos,tablaServicios){
        const valor = $cb.val();
        const idTablaServicio = $cb.attr("data-tabla-productos");
        const idServicio = $cb.attr("data-servicio");
        const tablaServicio = document.querySelector("#listaServiciosProductos #" + idTablaServicio); 
        try {
            let response = await this.funcfetch("obtener/producto/" + valor,null,"GET");
            const indexServicio = serviciosProductos.findIndex(s => s.idServicio == idServicio);
            if(indexServicio < 0){
                return alertify.error("no se encontro el servicio");
            }
            if(!response.producto){
                return alertify.error("no se encontro el producto");
            }
            response = response.producto;
            if(!serviciosProductos[indexServicio].productosLista.length){
                tablaServicio.innerHTML = "";
            }
            serviciosProductos[indexServicio].productosLista.push({
                idProducto : response.id,
                cantidad : 1,
                pVenta : response.precioVenta,
                importe : response.precioVenta,
                descuento : 0,
                pTotal : response.precioVenta
            });
            response.index = tablaServicio.children.length + 1;
            response.precioTotal = response.precioVenta;
            response.idServicio = idServicio;
            response.idProducto = response.id;
            response.cantidadUsada = 1;
            response.urlImagen = this.urlProductos + response.urlImagen;
            let tr = this.filaProducto(response);
            tablaServicio.append(tr);
            for (const cambio of tablaServicio.children[tablaServicio.children.length - 1].querySelectorAll(".cambio-detalle")) {
                cambio.addEventListener("change", (e) => {
                    this.modificarCantidad(cambio,serviciosProductos,tablaServicios);
                });
            }
            $cb[0].querySelector('option[value="' + valor + '"]').disabled = true;
            $cb.val("").trigger("change");
            this.calcularServiciosTotales(serviciosProductos,tablaServicios);
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener el producto");
        }
    }
    calcularServiciosTotales(serviciosProductos,tablaServicios){
        let subtotal = 0;
        let descuento = 0;
        let total = 0;
        console.log(serviciosProductos)
        serviciosProductos.forEach(cp => {
            let dsubtotal = 0;
            let ddescuento = 0;
            cp.productosLista.forEach(p => {
                dsubtotal+= parseFloat(p.importe);
                ddescuento += parseFloat(p.descuento);
            });
            cp.descuento = ddescuento;
            cp.pTotal = dsubtotal - ddescuento;
            cp.pUni = dsubtotal / cp.cantidad;
            const tr = tablaServicios.querySelector(`[data-servicio="${cp.idServicio}"]`);
            tr.querySelector(".costo-precio").textContent = this.monedaSoles(cp.pUni);
            tr.querySelector(".costo-descuento").textContent = "-" + this.monedaSoles(cp.descuento);
            tr.querySelector(".costo-subtotal").textContent = this.monedaSoles(cp.pTotal);
            subtotal += dsubtotal;
            descuento += cp.descuento;
            total += cp.pTotal;
        });
        document.querySelector("#idModalimporteTotal").textContent = this.monedaSoles(subtotal);
        document.querySelector("#idModaldescuentoTotal").textContent = "-" + this.monedaSoles(descuento);
        document.querySelector("#idModaligvTotal").textContent = this.monedaSoles(total * 0.18);
        document.querySelector("#idModaltotal").textContent = this.monedaSoles(total);
    }
    modificarCantidad(e,serviciosProductos,tablaServicios){
        const tr = e.parentElement.parentElement;
        const indexServicio = serviciosProductos.findIndex(s => s.idServicio == tr.dataset.servicio);
        if(indexServicio < 0){
            return alertify.error("servicio no encontrado");
        }
        let valor = e.step ? parseFloat(e.value) : parseInt(e.value);
        if(isNaN(valor)){
            valor = 0;
        }
        const tipo = e.dataset.tipo;
        if(tipo == "cantidad-servicio"){
            serviciosProductos[indexServicio].cantidad = valor;
            this.calcularServiciosTotales(serviciosProductos,tablaServicios);
            return false
        }
        if(tipo == "cantidad" || tipo == "descuento" || tipo == "precioVenta"){
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
            if(tipo == "precioVenta"){
                listaPro.pVenta = valor;
                listaPro.importe = listaPro.cantidad * valor;
                listaPro.pTotal = listaPro.importe - listaPro.descuento;
            }
            else if(tipo == "cantidad"){
                listaPro.cantidad = valor;
                listaPro.importe = listaPro.pVenta * valor;
                listaPro.pTotal = (listaPro.pVenta * valor) - listaPro.descuento;
            }else if(tipo == "descuento"){
                listaPro.descuento = valor;
                listaPro.pTotal = listaPro.importe - listaPro.descuento;
                if(listaPro.importe < listaPro.descuento){
                    listaPro.descuento = 0;
                    e.value = "0.00";
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
            txtSubTotal.textContent = this.monedaSoles(listaPro.pTotal.toFixed(2));
            this.calcularServiciosTotales(serviciosProductos,tablaServicios);
        }
    }
    async obtenerServicios(cbServicios,idServico,serviciosProductos,tablaServicios,tablaServicioProductos){
        try {
            const response = await this.funcfetch("obtener/servicio/" + idServico, null, "GET");
            if(response.session){
                return alertify.alert([...this.alertaSesion],() => {window.location.reload()});
            }
            let servicioJSON = response.servicio;
            servicioJSON.id_servicio = idServico;
            let total = 0;
            servicioJSON.productos.forEach(p => {
                total += p.precioVenta * p.cantidadUsada;
            });
            servicioJSON.cantidad = 1;
            servicioJSON.costo = total;
            servicioJSON.descuento = 0;
            servicioJSON.total = total;
            if(!serviciosProductos.length){
                tablaServicios.innerHTML = "";
                tablaServicioProductos.innerHTML = "";
            }
            serviciosProductos.push(this.asignarListaServiciosProductos(servicioJSON));
            console.log(serviciosProductos);
            tablaServicios.append(this.agregarServicio(tablaServicios.children.length + 1,servicioJSON.id,servicioJSON.servicio,1,total,0,total));
            let contenidoProducto = this.agregarServicioProductos(servicioJSON.id,servicioJSON.servicio,servicioJSON.productos);
            tablaServicioProductos.append(contenidoProducto);
            for (const cambio of tablaServicios.children[tablaServicios.children.length - 1].querySelectorAll(".cambio-detalle")) {
                cambio.addEventListener("change",()=>{
                    this.modificarCantidad(cambio,serviciosProductos,tablaServicios);
                });
            }
            for (const cambio of tablaServicioProductos.children[tablaServicioProductos.children.length - 1].querySelectorAll(".cambio-detalle")) {
                cambio.addEventListener("change", ()=>{
                    this.modificarCantidad(cambio,serviciosProductos,tablaServicios);
                });
            }
            const cbNuevoProducto = $(contenidoProducto.querySelector(".cb-servicios-productos"));
            $(cbNuevoProducto).select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: "Seleccionar un producto",
            }).on("select2:select",()=>{
                this.obtenerProducto(cbNuevoProducto,serviciosProductos,tablaServicios);
            });
            this.cbServiciosOpt(cbServicios,true,[servicioJSON.id]);
            $(cbServicios).val("").trigger("change");
            this.calcularServiciosTotales(serviciosProductos,tablaServicios);
            alertify.success("servicio agregado correctamente");
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener el servicio");
        }
    }
    templateOpcionContacto({id,nombreContacto,numeroContacto}){
        const opcion = document.createElement("option");
        opcion.value = !id ? "" : id;
        if(id) opcion.textContent = nombreContacto + " - " + numeroContacto;
        return opcion;
    }
    cbServiciosOpt(cbServicios,disabled,arrayServicios) {
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
    limpiarCotizacion(serviciosProductos,cbContactos,tablaServicios,tablaServicioProductos,cbServicios,checkIncluirCotizacion) {
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
        this.cbServiciosOpt(cbServicios,false,null);
        checkIncluirCotizacion.disabled = true;
        checkIncluirCotizacion.checked = false;
        document.querySelector("#idModalimporteTotal").textContent = "S/ 0.00";
        document.querySelector("#idModaldescuentoTotal").textContent = "-" + "S/ 0.00";
        document.querySelector("#idModaligvTotal").textContent = "S/ 0.00";
        document.querySelector("#idModaltotal").textContent = "S/ 0.00";
    }
    renderPdfCargados({valorDocumento,contenedorArchivoPdf,nombreDocumento,idDocumento}){
        
        const contenedor = document.createElement("div");
        contenedor.className = "contenido rounded-pill bg-light p-2";
        contenedor.innerHTML = `<span>${nombreDocumento}</span><button type="button" ${idDocumento ? 'data-documento="' + idDocumento + '"' : ''} class="mr-1 btn btn-sm"><i class="fas fa-trash-alt"></i></button>`;
        if(valorDocumento){
            let dataTransfer = new DataTransfer();
            dataTransfer.items.add(valorDocumento);
            const archivo = document.createElement("input");
            archivo.type = "file";
            archivo.name = "archivoPdf[]";
            archivo.hidden = true;
            archivo.files = dataTransfer.files;
            contenedor.append(archivo);
        }
        contenedorArchivoPdf.append(contenedor);
    }
    eliminarServicio({serviciosProductos,cbServicios,servicio,tr,tablaServicioProductos,tablaServicios}){
        serviciosProductos = serviciosProductos.filter(s => s.idServicio != servicio);
        console.log(serviciosProductos);
        this.cbServiciosOpt(cbServicios,false,[+servicio]);
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
        return serviciosProductos;
    }
    eliminarProducto({serviciosProductos,tr,producto,indexServicio,cbProducto}){
        serviciosProductos[indexServicio].productosLista = serviciosProductos[indexServicio].productosLista.filter(p => p.idProducto != producto);
        $("#" + cbProducto )[0].querySelector('option[value="' + producto + '"]').disabled = false;
        tr.remove();
        return serviciosProductos[indexServicio].productosLista;
    }
}