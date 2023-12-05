class Cotizacion extends General{
    $cbTipoMoneda = document.querySelector("#idModaltipoMoneda");
    $txtConversion = document.querySelector("#idModalconversionMoneda");
    $cbIncluirIGV = document.querySelector("#idModalincluirIGV");

    comboListaAlmacenes(listaAlmacenes,idAlmacen){
        const cb = document.createElement("select");
        cb.className = "form-control form-control-sm";
        let template = "<option></option>";
        listaAlmacenes.forEach(almacen => {
            template += `<option value="${almacen.idAlmacen}" ${almacen.idAlmacen == idAlmacen ? 'selected' : ''}>${almacen.nombreAlmacen}</option>`
        });
        cb.innerHTML = template;
        return cb;
    }
    almacenProductos({index,idProducto,urlImagen,nombreProducto,listaAlmacenes,cantidadUsada,idAlmacen}){
        const tr = document.createElement("tr");
        tr.dataset.producto = idProducto;
        tr.innerHTML =`
            <td>${index}</td>
            <td><img class="img-vistas-pequena" src="${this.urlProductos + "" + urlImagen}" alt="Imagen del producto"></td>
            <td>${nombreProducto}</td>
            <td>${cantidadUsada}</td>
            <td>${this.comboListaAlmacenes(listaAlmacenes,idAlmacen).outerHTML}</td>        
        `
        return tr;
    }
    almacenServicios({id,servicio,productos}){
        if(!productos.length){
            return null;
        }
        const div = document.createElement("div");
        div.dataset.servicio = id;
        div.innerHTML = `<i class="fas fa-concierge-bell"></i><span class="ml-1">${servicio}</span>`;
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
            <th style="min-width: 150px; width: 150px; max-width: 150px;">ALMACEN</th>
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
        div.append(tablaResponsive);
        return div;
    }
    resultadosAlmacenServicio($tablaServiciosProductos){
        let resultado = [];
        for (const li of $tablaServiciosProductos.children) {
            let productos = this.resultadoAlmacenProducto(li.querySelector("tbody"));
            resultado.push({
                idServicio : li.dataset.servicio,
                productos
            });
        }
        return resultado;
    }
    resultadoAlmacenProducto($tablaProducto){
        let productos = [];
        for (const tr of $tablaProducto.children) {
            productos.push({
                idProducto : tr.dataset.producto,
                idAlmacen : tr.querySelector("select").value
            });
        }
        return productos;
    }
    filaProducto({idProducto,idServicio,index,urlImagen,descripcion:nombreProducto,cantidad,precioUnitarioConIgv,precioUnitarioNormal,total,descuento,tipo = "nuevo"}){
        const precioVenta = false ? precioUnitarioConIgv.toFixed(2) : precioUnitarioNormal.toFixed(2)
        const tr = document.createElement("tr");
        tr.dataset.producto = idProducto;
        tr.dataset.servicio = idServicio;
        tr.innerHTML =`
            <td>${index}</td>
            <td><img class="img-vistas-pequena" src="${urlImagen}" alt="Imagen del producto"></td>
            <td>${nombreProducto}</td>
            <td><input type="number" step="0.01" value="${cantidad}" class="form-control form-control-sm cambio-detalle" data-tipo="cantidad"></td>
            <td><input type="number" step="0.01" value="${precioVenta}" class="form-control form-control-sm cambio-detalle" data-tipo="precioVenta"></td>
            <td><input type="number" step="0.01" value="${descuento}" class="form-control form-control-sm cambio-detalle" data-tipo="descuento"></td>
            <td><span class="costo-subtota">${this.resetearMoneda(total,this.$cbTipoMoneda.value)}</span></td>
            <td class="text-center"><button type="button" data-tipo="${tipo}" class="btn btn-sm btn-danger p-2" data-cbproducto="servicioProductoLista${
            idServicio}"><i class="fas fa-trash-alt"></i></button></td>        
        `
        return tr;
    }
    listarDetalleProductosDeServicios(idServicio,nombreServicio,listaProducto,tipo){
        const servicio = document.createElement("div");
        servicio.className = "col-12";
        servicio.dataset.domservicio = idServicio;
        let templateBody = "";
        listaProducto.forEach((producto,k) => {
            producto.index = k + 1;
            producto.urlImagen = this.urlProductos + producto.urlImagen;
            producto.idServicio = idServicio;
            producto.tipo = tipo;
            templateBody += this.filaProducto(producto).outerHTML;
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
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="text-primary">
                <i class="fas fa-concierge-bell"></i> ${nombreServicio}  
            </h5>
            <div class="form-group" style="width:300px;">
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
    agregarServicioProductos({idServicio,descripcion,productosLista}) {
        const servicio = document.createElement("div");
        servicio.className = "col-12";
        servicio.dataset.domservicio = idServicio;
        let templateBody = "";
        console.log(productosLista);
        productosLista.forEach((p,k) => {
            p.index = k + 1;
            templateBody += this.filaProducto(p).outerHTML;
        });
        const cbClonadoProductos = document.querySelector("#cbProductos").cloneNode(true);
        for (const opt of cbClonadoProductos.querySelectorAll("option")) {
            opt.disabled = productosLista.findIndex( p => p.idProducto == opt.value) >= 0 ? true : false;
        }
        cbClonadoProductos.setAttribute("id","servicioProductoLista" + idServicio);
        cbClonadoProductos.hidden = false;
        cbClonadoProductos.className = "form-control cb-servicios-productos";
        cbClonadoProductos.setAttribute("data-tabla-productos",`tablaBodyServiciosProductos${idServicio}`);
        cbClonadoProductos.setAttribute("data-servicio",`${idServicio}`);
        servicio.innerHTML = `
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="text-primary">
                <i class="fas fa-concierge-bell"></i> ${descripcion}  
            </h5>
            <div class="form-group" style="width:300px;">
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
    agregarServicio({numeroItem,idServicio,idProducto,descripcion,cantidad,precioUnitarioConIgv,precioUnitarioNormal,descuento,total,tipo="nuevo"}) {
        const valorTipoMoneda = this.$cbTipoMoneda.value;
        const precioUnitario = false ? precioUnitarioConIgv : precioUnitarioNormal;
        const tipoServicioProducto = !idServicio ? 'producto' : 'servicio'; 
        const tr = document.createElement("tr");
        let txtPrecioUnitario = `<span class="costo-precio">${this.resetearMoneda(precioUnitario,valorTipoMoneda)}</span>`;
        let txtDescuento =  `<span class="costo-descuento">-${this.resetearMoneda(descuento,valorTipoMoneda)}</span>`;
        if(tipoServicioProducto === "producto"){
            txtPrecioUnitario = `<input type="number" step="0.01" value="${Number.parseFloat(precioUnitario).toFixed(2)}" class="form-control form-control-sm cambio-detalle" data-tipo="precio-servicio-producto">`
            txtDescuento = `<input type="number" step="0.01" value="${Number.parseFloat(descuento).toFixed(2)}" class="form-control form-control-sm cambio-detalle" data-tipo="descuento-servicio-producto">`
        }
        tr.dataset[tipoServicioProducto] = tipoServicioProducto === "producto" ? idProducto : idServicio;
        tr.innerHTML = `
        <td>${numeroItem}</td>
        <td>${descripcion}</td>
        <td><input type="number" value="${cantidad}" class="form-control form-control-sm cambio-detalle" data-tipo="${tipoServicioProducto === "producto" ? 'cantidad-servicio-producto' : 'cantidad-servicio'}"></td>
        <td>${txtPrecioUnitario}</td>
        <td>${txtDescuento}</td>
        <td><span class="costo-subtotal">${this.resetearMoneda(total.toFixed(2),valorTipoMoneda)}</span></td>
        <td class="text-center"><button class="btn btn-sm btn-danger" data-tipo="${tipo}" type="button"><i class="fas fa-trash-alt"></i></button></td>
        `;
        return tr;
    }
    convertirPrecioVenta(precioVenta,tipoMoneda){
        const valorTipoDocumento = this.$cbTipoMoneda.value;
        const valorConversor = isNaN(Number.parseFloat(this.$txtConversion.value)) ? 0 : Number.parseFloat(this.$txtConversion.value);
        let precioVentaConvertido = precioVenta;
        if(valorTipoDocumento !== tipoMoneda){
            precioVentaConvertido = valorTipoDocumento === "USD" ? precioVenta / valorConversor :  precioVenta * valorConversor;
        }
        return Number.parseFloat(precioVentaConvertido);
    }
    asignarListaServiciosProductos(servicio,tipo,condicion){
        const incluirIGV = false;
        let productosLista = [];
        let precioUnitarioNormal = 0;
        let precioUnitarioConIgv = 0;
        let importeTotal = 0;
        let descuento = 0;
        let idServicio = servicio.id;
        let idProducto = null;
        if(tipo === "producto"){
            if(servicio.descuento){
                descuento += parseFloat(servicio.descuento);
            }
            precioUnitarioNormal = this.convertirPrecioVenta(servicio.precioVenta,servicio.tipoMoneda);
            precioUnitarioConIgv = precioUnitarioNormal * this.igvTotal;
            importeTotal = incluirIGV ? precioUnitarioConIgv * servicio.cantidad : precioUnitarioNormal * servicio.cantidad;
            idProducto = servicio.id;
            idServicio = null;
        }
        if(servicio.productos){
            servicio.productos.forEach(p => {
                const descuentoProducto = !p.descuentoProducto ? 0 : parseFloat(p.descuentoProducto);
                const precioVentaConvertido = this.convertirPrecioVenta(p.precioVenta,p.tipoMoneda);
                const precioVentaIgvProducto = precioVentaConvertido * this.igvTotal; 
                const importeTotalProducto = incluirIGV ? precioVentaIgvProducto * p.cantidadUsada : precioVentaConvertido * p.cantidadUsada; 
                productosLista.push({
                    idServicio : p.id_servicio||servicio.id,
                    tipo : condicion,
                    tipoPoneada : p.tipoMoneda,
                    idProducto : p.idProducto,
                    descripcion : p.nombreProducto,
                    cantidad : p.cantidadUsada,
                    urlImagen : this.urlProductos + p.urlImagen,
                    precioUnitarioNormal : precioVentaConvertido,
                    precioUnitarioConIgv : precioVentaIgvProducto,
                    importeTotal: importeTotalProducto,
                    descuento : descuentoProducto,
                    total : importeTotalProducto - descuentoProducto
                });
                importeTotal += importeTotalProducto
                descuento += parseFloat(descuentoProducto);
            });
            precioUnitarioNormal += incluirIGV ? importeTotal - (importeTotal * this.igv) : importeTotal;
            precioUnitarioConIgv += !incluirIGV ? importeTotal * this.igvTotal  : importeTotal;
        }
        let total = importeTotal - descuento;
        return {
            idServicio,
            idProducto,
            cantidad : servicio.cantidad,
            descripcion : servicio.servicio||servicio.nombreProducto,
            tipo : condicion,
            precioUnitarioNormal,
            precioUnitarioConIgv,
            importeTotal,
            descuento,
            total,
            productosLista
        };
    }
    async obtenerProducto($cb,serviciosProductos,tablaServicios){
        const valor = $cb.val();
        const incluirIGV = false;
        const idTablaServicio = $cb.attr("data-tabla-productos");
        const idServicio = $cb.attr("data-servicio");
        const tablaServicio = document.querySelector("#listaServiciosProductos #" + idTablaServicio);
        console.log($cb,serviciosProductos,tablaServicios);
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
            const precioUnitarioConvertido = this.convertirPrecioVenta(response.precioVenta,response.tipoMoneda);
            const precioUnitarioConIgv = precioUnitarioConvertido * this.igvTotal;
            const importeTotal = incluirIGV ? precioUnitarioConIgv : precioUnitarioConvertido;
            const nuevoProducto = {
                index : tablaServicio.children.length + 1,
                idProducto : response.id,
                importeTotal,
                total : importeTotal,
                tipo : "nuevo",
                precioUnitarioConIgv,
                precioUnitarioNormal : precioUnitarioConvertido,
                descripcion : response.nombreProducto,
                idServicio,
                cantidad : 1,
                tipoPoneada : response.tipoMoneda,
                descuento : 0,
                urlImagen : this.urlProductos + response.urlImagen
            }
            serviciosProductos[indexServicio].productosLista.push(nuevoProducto);
            let tr = this.filaProducto(nuevoProducto);
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
    modificarMonedaTotal(tipoMoneda,serviciosProductos,tablaServicios){
        serviciosProductos.forEach(servicio => {
            servicio.productosLista.forEach(producto => {
                const costoSubTotal = document.querySelector(`[data-producto="${producto.idProducto}"][data-servicio="${servicio.idServicio}"] .costo-subtota`);
                if(costoSubTotal){
                    costoSubTotal.textContent = this.resetearMoneda(producto.pTotal,tipoMoneda);
                }
            });
        });
        this.calcularServiciosTotales(serviciosProductos,tablaServicios);
    }
    calcularServiciosTotales(listaServicios,tablaServicios){
        console.log(listaServicios);
        let subTotal = 0;
        let descuentoTotal = 0;
        let generalTotal = 0;
        const valorTipoDocumento = this.$cbTipoMoneda.value;
        const incluirIGV = false;
        const incluirIGV2 = +this.$cbIncluirIGV.value;
        listaServicios.forEach(cp => {
            let subTotalDetalle = 0;
            let descuentoTotalDetalle = 0;
            let generalTotalDetalle = 0;
            const tr = tablaServicios.querySelector(`[data-servicio="${cp.idServicio}"]`) || tablaServicios.querySelector(`[data-producto="${cp.idProducto}"]`);
            if(!cp.idServicio && cp.idProducto){
                subTotalDetalle = cp.importeTotal;
                descuentoTotalDetalle = cp.descuento;
                generalTotalDetalle = cp.total;
            }
            cp.productosLista.forEach(p => {
                subTotalDetalle += p.importeTotal;
                descuentoTotalDetalle += p.descuento;
                generalTotalDetalle += p.total;
            });
            cp.total = generalTotalDetalle;
            cp.importeTotal = subTotalDetalle;
            cp.descuento = descuentoTotalDetalle;
            cp.precioUnitarioNormal = subTotalDetalle/cp.cantidad;
            cp.precioUnitarioConIgv = cp.precioUnitarioNormal * this.igvTotal;
            if(cp.idServicio && !cp.idProducto){
                tr.querySelector(".costo-precio").textContent = this.resetearMoneda(!incluirIGV ? cp.precioUnitarioNormal.toFixed(2) : cp.precioUnitarioConIgv.toFixed(2),valorTipoDocumento);
                tr.querySelector(".costo-descuento").textContent = "-" + this.resetearMoneda(cp.descuento,valorTipoDocumento);
            }
            tr.querySelector(".costo-subtotal").textContent = this.resetearMoneda(cp.total.toFixed(2),valorTipoDocumento);
            subTotal += cp.importeTotal;
            descuentoTotal += parseFloat(cp.descuento);
            generalTotal += cp.total;
        });
        const igvTotal = !incluirIGV2 ? 0 : parseFloat(generalTotal * this.igv);
        document.querySelector("#idModalimporteTotal").textContent = this.resetearMoneda(subTotal.toFixed(2),valorTipoDocumento);
        document.querySelector("#idModaldescuentoTotal").textContent = "- " + this.resetearMoneda(descuentoTotal,valorTipoDocumento);
        document.querySelector("#idModaligvTotal").textContent = this.resetearMoneda(igvTotal,valorTipoDocumento);
        document.querySelector("#idModaltotal").textContent = this.resetearMoneda(parseFloat(generalTotal + igvTotal).toFixed(2),valorTipoDocumento);
    }
    ocultarMostrarIGV(serviciosProductos,valor){
        const incluirIGV = false;
        const incluirIGV2 = +valor;
        document.querySelector("#idModaligvTotal").parentElement.hidden = incluirIGV2 === 0 ? true : false;
        serviciosProductos.map(servicio => {
            let importeTotal = 0;
            if(servicio.idProducto && !servicio.idServicio){
                importeTotal = !incluirIGV ? servicio.precioUnitarioNormal * servicio.cantidad : servicio.precioUnitarioConIgv * servicio.cantidad;
                servicio.importeTotal = importeTotal;
                servicio.total = importeTotal - servicio.descuento;
            }else if(!servicio.idProducto && servicio.idServicio){
                importeTotal = 0;
                let descuentoTotal = 0;
                servicio.productosLista.map(producto => {
                    const tr = document.querySelector(`[data-producto="${producto.idProducto}"][data-servicio="${producto.idServicio}"]`);
                    const importeTotalProducto = !incluirIGV ? producto.precioUnitarioNormal * producto.cantidad : producto.precioUnitarioConIgv * producto.cantidad;
                    producto.importeTotal = importeTotalProducto;
                    producto.total = importeTotalProducto - producto.descuento;
                    importeTotal += importeTotalProducto;
                    descuentoTotal += producto.descuento;
                    tr.querySelector("[data-tipo='precioVenta']").value = incluirIGV ? producto.precioUnitarioConIgv.toFixed(2) : producto.precioUnitarioNormal.toFixed(2);
                    tr.querySelector(".costo-subtota").textContent = this.resetearMoneda(producto.total,this.$cbTipoMoneda.value);
                });
                servicio.importeTotal = importeTotal;
                servicio.descuento = descuentoTotal;
                servicio.total = importeTotal - descuentoTotal;
                const valorUnitario = servicio.importeTotal / servicio.cantidad;
                servicio.precioUnitarioNormal = incluirIGV ? valorUnitario - (valorUnitario * this.igv) : valorUnitario;
                servicio.precioUnitarioConIgv = !incluirIGV ? valorUnitario * this.igvTotal  : valorUnitario;
                
            }
        })
    }
    modificarCantidad(e,serviciosProductos,tablaServicios){
        console.log(e,serviciosProductos,tablaServicios);
        const tr = e.parentElement.parentElement;
        const incluirIGV = false;
        const datosDetalle = {
            tipo : !tr.dataset.servicio ? 'producto' : 'servicio',
            idDetalle : tr.dataset.servicio || tr.dataset.producto
        };
        console.log(datosDetalle);
        const indexServicio = serviciosProductos.findIndex(function(detalle){
            if(datosDetalle.tipo === "servicio" && +detalle.idServicio === +datosDetalle.idDetalle && !detalle.idProducto){
                return true;
            }
            if(datosDetalle.tipo === "producto" && +detalle.idProducto === +datosDetalle.idDetalle && !detalle.idServicio){
                return true;
            }
            return false;
        });
        if(indexServicio < 0){
            return alertify.error("servicio o producto no encontrado");
        }
        let valor = e.step ? parseFloat(e.value) : parseInt(e.value);
        if(isNaN(valor)){
            valor = 0;
        }
        const tipo = e.dataset.tipo;
        if(tipo == "cantidad-servicio-producto"){
            serviciosProductos[indexServicio].cantidad = valor;
            serviciosProductos[indexServicio].importeTotal = incluirIGV ? valor * serviciosProductos[indexServicio].precioUnitarioConIgv : valor * serviciosProductos[indexServicio].precioUnitarioNormal;
            serviciosProductos[indexServicio].total = serviciosProductos[indexServicio].importeTotal - serviciosProductos[indexServicio].descuento;
            this.calcularServiciosTotales(serviciosProductos,tablaServicios);
            return false;
        }
        if(tipo == "cantidad-servicio"){
            const valorUnitario = serviciosProductos[indexServicio].importeTotal / valor;
            serviciosProductos[indexServicio].cantidad = valor;
            serviciosProductos[indexServicio].precioUnitarioConIgv = incluirIGV ? valorUnitario : valorUnitario * this.igvTotal;
            serviciosProductos[indexServicio].precioUnitarioNormal = incluirIGV ? valorUnitario - (valorUnitario * this.igv) : valorUnitario;
            this.calcularServiciosTotales(serviciosProductos,tablaServicios);
            return false;
        }
        if(tipo == "precio-servicio-producto"){
            serviciosProductos[indexServicio].precioUnitarioConIgv = valor * this.igvTotal;
            serviciosProductos[indexServicio].precioUnitarioNormal = valor;
            serviciosProductos[indexServicio].importeTotal = incluirIGV ? serviciosProductos[indexServicio].precioUnitarioConIgv * serviciosProductos[indexServicio].cantidad : serviciosProductos[indexServicio].precioUnitarioNormal * serviciosProductos[indexServicio].cantidad;
            serviciosProductos[indexServicio].total = serviciosProductos[indexServicio].importeTotal - serviciosProductos[indexServicio].descuento;
            this.calcularServiciosTotales(serviciosProductos,tablaServicios);
            return false;
        }
        if(tipo == "descuento-servicio-producto"){
            serviciosProductos[indexServicio].descuento = valor;
            serviciosProductos[indexServicio].total = serviciosProductos[indexServicio].importeTotal - serviciosProductos[indexServicio].descuento;
            if(serviciosProductos[indexServicio].total < valor){
                serviciosProductos[indexServicio].descuento = 0;                
                e.value = "0.00";
                serviciosProductos[indexServicio].total = serviciosProductos[indexServicio].importeTotal;
            }
            this.calcularServiciosTotales(serviciosProductos,tablaServicios);
            return false;
        }
        if(tipo == "cantidad" || tipo == "descuento" || tipo == "precioVenta"){
            const txtSubTotal = tr.querySelector(".costo-subtota");
            const indexProducto = serviciosProductos[indexServicio].productosLista.findIndex(p => p.idProducto == tr.dataset.producto);
            if(indexProducto < 0){
                return alertify.error("producto no encontrado");
            }
            const listaPro = serviciosProductos[indexServicio].productosLista[indexProducto];
            if(tipo == "precioVenta"){
                listaPro.precioUnitarioConIgv = valor * this.igvTotal;
                listaPro.precioUnitarioNormal = valor;
                listaPro.importeTotal = incluirIGV ? listaPro.precioUnitarioConIgv * listaPro.cantidad : listaPro.precioUnitarioNormal * listaPro.cantidad;
                listaPro.total = listaPro.importeTotal - listaPro.descuento;
            }
            else if(tipo == "cantidad"){
                listaPro.cantidad = valor;
                listaPro.importeTotal = incluirIGV ? listaPro.precioUnitarioConIgv * valor : listaPro.precioUnitarioNormal * valor;
                listaPro.total = listaPro.importeTotal - listaPro.descuento;
            }else if(tipo == "descuento"){
                listaPro.descuento = valor;
                listaPro.total = listaPro.importeTotal - listaPro.descuento;
                if(listaPro.importeTotal < listaPro.descuento){
                    listaPro.descuento = 0;
                    e.value = "0.00";
                }
            }
            if(listaPro.total < 0){
                listaPro.cantidad = 1;
                listaPro.importeTotal = incluirIGV ? listaPro.precioUnitarioConIgv : listaPro.precioUnitarioNormal;
                listaPro.descuento = 0;
                listaPro.total = 0;
                tr.querySelector(`[data-tipo="cantidad"]`).value = "0";
                tr.querySelector(`[data-tipo="descuento"]`).value = "0.00";
            }
            txtSubTotal.textContent = this.resetearMoneda(listaPro.total.toFixed(2),this.$cbTipoMoneda.value);
            this.calcularServiciosTotales(serviciosProductos,tablaServicios);
        }
    }
    calcularNumeroItem(tabla){
        let numeroFila = 1;
        for (const tr of tabla.children) {
            tr.querySelector("td").textContent = numeroFila;
            numeroFila++;
        }
    }
    async obtenerServicios(cbServicios,idServicoProducto,serviciosProductos,tablaServicios,tablaServicioProductos,tipoProductoServicio = "servicio"){
        try {
            const url = tipoProductoServicio === "producto" ? "obtener/producto/" : "obtener/servicio/";
            const response = await this.funcfetch(url + idServicoProducto, null, "GET");
            if(response.session){
                return alertify.alert([...this.alertaSesion],() => {window.location.reload()});
            }
            let servicioJSON = response.servicio||response.producto;
            servicioJSON.cantidad = 1;
            servicioJSON.descuento = 0;
            if(!serviciosProductos.length){
                tablaServicios.innerHTML = "";
                tablaServicioProductos.innerHTML = "";
            }
            const listaServicioProducto = this.asignarListaServiciosProductos(servicioJSON,tipoProductoServicio,"nuevo");
            console.log(listaServicioProducto);
            // return false;
            // const {total} = listaServicioProducto;
            serviciosProductos.push(listaServicioProducto);
            listaServicioProducto.numeroItem = tablaServicios.children.length + 1;
            tablaServicios.append(this.agregarServicio(listaServicioProducto));
            for (const cambio of tablaServicios.children[tablaServicios.children.length - 1].querySelectorAll(".cambio-detalle")) {
                cambio.addEventListener("change",()=>{
                    this.modificarCantidad(cambio,serviciosProductos,tablaServicios);
                });
            }
            if(tipoProductoServicio === "servicio"){
                let contenidoProducto = this.agregarServicioProductos(listaServicioProducto);
                tablaServicioProductos.append(contenidoProducto);
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
            }
            this.cbServiciosOpt(cbServicios,true,[{id:servicioJSON.id,tipo:tipoProductoServicio}]);
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
            if(arrayServicios.find(detalle => +detalle.id === +cb.value && detalle.tipo === cb.dataset.tipo)){
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
    eliminarServicio({serviciosProductos,cbServicios,idDetalle,tipo,tr,tablaServicioProductos,tablaServicios}){
        serviciosProductos = serviciosProductos.filter(function(detalle){
            if(tipo === "servicio" && detalle.idServicio === idDetalle && !detalle.idProducto){
                return false;
            }
            if(tipo === "producto" && detalle.idProducto === idDetalle && !detalle.idServicio){
                return false;
            }
            return true;
        });
        this.cbServiciosOpt(cbServicios,false,[{id:idDetalle,tipo:tipo}]);
        tr.remove();
        if(tipo === "servicio"){
            tablaServicioProductos.querySelector(`[data-domservicio="${idDetalle}"]`).remove();
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