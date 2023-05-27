function loadPage(){
    let general = new General();
    const tablaVentas = document.querySelector("#tablaVentas");
    const tablaVentasDataTable = $(tablaVentas).DataTable({
        ajax: {
            url: 'general/listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.productos = $("#productoBuscar").val();
            }
        },
        columns: [
            {
                data: 'nroVenta'
            },
            {
                data: 'comprobante'
            },
            {
                data: 'nroComprobante'
            },
            {
                data: 'fechaVenta'
            },
            {
                data: 'nombreCliente'
            },
            {
                data: 'metodoPago'
            },
            {
                data: 'metodoEnvio'
            },
            {
                data: 'subTotal',
                render: function (dato) {
                    return !dato ? 'Sin subtotal' : general.monedaSoles(dato);
                }
            },
            {
                data: 'igvTotal',
                render: function (dato) {
                    return !dato ? 'Sin I.G.V' : general.monedaSoles(dato);
                }
            },
            {
                data: 'descuentoTotal',
                render: function (dato) {
                    return !dato ? 'Sin descuento' : " - " + general.monedaSoles(dato);
                }
            },
            {
                data: 'envio',
                render: function (dato) {
                    return !dato ? 'Sin costo de envío' : general.monedaSoles(dato);
                }
                
            },
            {
                data: 'total',
                render: function (dato) {
                    return !dato ? 'Sin costo total' : general.monedaSoles(dato);
                }
                
            },
            {
                data: 'id',
                render: function (data) {
                    return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" type="button" data-venta="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <div class="d-flex justify-content-center" style="gap:5px;"><a href="${general.urlVentaComprobante + data}" target="_blank" class="btn btn-sm btn-outline-primary p-1">
                    <small>
                    <i class="fas fa-clipboard-check"></i>
                    Comprobante
                    </small>
                </a>
                <button class="btn btn-sm btn-outline-danger p-1" data-venta="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
                }
            }
        ]
    });
    const formatoListaProductos = function(producto){
        if(!producto.id){
            return producto.text;
        }
        let urlProducto = window.location.origin + "/intranet/storage/productos/" + producto.element.dataset.url;
        let precioVenta = isNaN(parseFloat(producto.element.dataset.venta)) ? "No establecido" : general.monedaSoles(producto.element.dataset.venta);
        let precioVentaMayor = isNaN(parseFloat(producto.element.dataset.ventaMayor)) ? "No establecido" : general.monedaSoles(producto.element.dataset.ventaMayor);
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
        if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1 || (data.element.dataset.codigo && data.element.dataset.codigo.indexOf(params.term) > -1 )) {
          return $.extend({}, data, true);
        }
        return null;
    }
    const swtichProductoMenor = document.querySelector("#idVentaPorMenor");
    const tablaDetalleVenta = document.querySelector("#idModaltablaDetalleVenta tbody");

    $('#productoBuscar').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: "Busque y seleccione un producto",
        templateResult : formatoListaProductos,
        matcher: matchCustom
    }).on("select2:select",async function(e){
        // try {
        //     const indexProducto = listaProductos.findIndex(p => p.idProducto == $(this).val() && p.porMenor === swtichProductoMenor.checked);
        //     if (indexProducto < 0){
        //         const response = await general.funcfetch("administrador/listar/" + $(this).val(), null, "GET");
        //         if(response.session){
        //             return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
        //         }
        //         if (response.alerta) {
        //             return alertify.alert("Alerta", response.alerta);
        //         }
        //         if (response.producto && indexProducto < 0) {
        //             let precioVe = isNaN(parseFloat(response.producto.precioVenta)) ? 0.00 : parseFloat(response.producto.precioVenta);
        //             if (!swtichProductoMenor.checked){
        //                 precioVe = isNaN(parseFloat(response.producto.precioVentaPorMayor)) ? 0.00 : parseFloat(response.producto.precioVentaPorMayor);
        //                 if (precioVe == 0){
        //                     $('#productoBuscar').val("").trigger("change");
        //                     return alertify.alert("Mensaje", "El producto <strong>" + response.producto.nombreProducto + "</strong> no cuenta con un precio de venta al por mayor o el valor es igual a S/ 0.00");
        //                 }
        //             }
        //             if (!listaProductos.length) {
        //                 tablaDetalleVenta.innerHTML = "";
        //             }
        //             let detalleTr = agregarDetalleVenta(tablaDetalleVenta.children.length + 1, response.producto.id, response.producto.urlProductos, response.producto.nombreProducto, precioVe,response.perecederos);
        //             tablaDetalleVenta.append(detalleTr);
        //             listaProductos.push({
        //                 idProducto: response.producto.id,
        //                 precio: precioVe,
        //                 descuento: 0,
        //                 cantidad: 1,
        //                 igv: response.producto.igv,
        //                 subtotal: precioVe,
        //                 vencimientos: response.perecederos[0].valor,
        //                 porMenor: swtichProductoMenor.checked
        //             });
        //             for (const cambio of detalleTr.querySelectorAll(".cambio-detalle")) {
        //                 cambio.addEventListener("change", modificarCantidad);
        //             }
        //             for (const cambioCv of detalleTr.querySelectorAll(".cambio-vencimiento")) {
        //                 cambioCv.addEventListener("change", cambioVencimiento);
        //             }
        //         }
        //     }else{
        //         const textMayor = !swtichProductoMenor.checked ? "pormayor" : "pormenor";
        //         const cantidad = tablaDetalleVenta.querySelector("#detalle-venta-producto-" + $(this).val() + "-" + textMayor);
        //         listaProductos[indexProducto].cantidad++;
        //         listaProductos[indexProducto].subtotal = (listaProductos[indexProducto].cantidad * listaProductos[indexProducto].precio) - listaProductos[indexProducto].descuento;
        //         cantidad.value = isNaN(parseInt(cantidad.value)) ? 1 : parseInt(cantidad.value) + 1;
        //         tablaDetalleVenta.querySelector("#detalle-venta-subtotal-" + $(this).val() + "-" + textMayor).textContent = gen.resetearMoneda(listaProductos[indexProducto].subtotal);
        //     }
        //     sumarTotalDetalle();
        //     $('#productoBuscar').val("").trigger("change");
        // } catch (error) {
        //     alertify.error("error al obtener el producto");
        //     console.error(error);
        // }
    });
    // function cambioVencimiento(e) {
    //     const tr = e.target.parentElement.parentElement;
    //     const valorPorMenor = tr.dataset.pormenor == "true" ? true : false;
    //     const indexProducto = listaProductos.findIndex(p => p.idProducto == tr.dataset.producto && p.porMenor === valorPorMenor);
    //     if(indexProducto < 0){
    //         return alertify.error("producto no encontrado");
    //     }
    //     listaProductos[indexProducto].vencimientos = e.target.value;
    // }
    // function modificarCantidad(e){
    //     const tr = e.target.parentElement.parentElement;
    //     const textMayor = tr.dataset.pormenor == "true" ? "pormenor" : "pormayor";
    //     const valorPorMenor = tr.dataset.pormenor == "true" ? true : false;
    //     const indexProducto = listaProductos.findIndex(p => p.idProducto == tr.dataset.producto && p.porMenor === valorPorMenor);
    //     if(indexProducto < 0){
    //         alertify.error("producto no encontrado");
    //     }
    //     let valor = e.target.step ? parseFloat(e.target.value) : parseInt(e.target.value);
    //     if(isNaN(valor)){
    //         valor = 1;
    //     }
    //     const txtSubTotal = tr.querySelector("#detalle-venta-subtotal-" + tr.dataset.producto + "-" + textMayor);

    //     switch (e.target.dataset.tipo) {
    //         case 'cantidad':
    //             listaProductos[indexProducto].cantidad = valor;
    //             listaProductos[indexProducto].subtotal = listaProductos[indexProducto].precio * valor;
    //         break;
    //         case 'descuento':
    //             listaProductos[indexProducto].descuento = valor;
    //             listaProductos[indexProducto].subtotal = listaProductos[indexProducto].precio * listaProductos[indexProducto].cantidad;
    //             if(listaProductos[indexProducto].subtotal < listaProductos[indexProducto].descuento){
    //                 listaProductos[indexProducto].descuento = 0;
    //                 e.target.value = "0.00";
    //             }
    //         break;
    //     }
    //     if(listaProductos[indexProducto].subtotal < 0){
    //         listaProductos[indexProducto].cantidad = 1;
    //         listaProductos[indexProducto].subtotal = listaProductos[indexProducto].precio;
    //         listaProductos[indexProducto].descuento = 0;
    //         tr.querySelector(`[data-tipo="cantidad"]`).value = "1";
    //         tr.querySelector(`[data-tipo="descuento"]`).value = "0.00";
    //     }
    //     txtSubTotal.textContent = gen.monedaSoles(listaProductos[indexProducto].subtotal.toFixed(2));
    //     sumarTotalDetalle();
    // }
    function agregarDetalleVenta(indice,idProducto,idVentaDetalle,urlImagenProducto,nombreProducto,precio,vencimientos,fechaVencimiento){
        let tr = document.createElement("tr");
        let selectPerecedero = document.createElement("select");
        selectPerecedero.className = "form-control form-control-sm cambio-vencimiento";
        selectPerecedero.name = "producto_perecedero[]";
        selectPerecedero.append(new Option("Ninguno","", false, false));
        vencimientos.forEach(v => {
            selectPerecedero.append(new Option(v.verPerecedero, v.vencimiento, false, v.vencimiento == fechaVencimiento ? true : false));
        });
        tr.dataset.producto = idProducto;
        tr.dataset.ventaDetalle = idVentaDetalle;
        tr.innerHTML = `
        <td>${indice}</td>
        <td><img src="${urlImagenProducto}" class="tdimagen-producto" /></td>
        <td>${nombreProducto}</td>
        <td>${general.resetearMoneda(precio)}</td>
        <td><input type="number" name="producto_cantidad[]" min="1" data-tipo="cantidad" class="form-control form-control-sm cambio-detalle" value="1"/></td>
        <td><input type="number" name="producto_descuento[]" min="0" data-tipo="descuento" step="0.01" class="form-control form-control-sm cambio-detalle" value="0.00"/></td>
        <td>${selectPerecedero.outerHTML}</td>
        <td><span>${general.resetearMoneda(precio)}</span></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i> <span>Elimar</span></button></td>
        `
        return tr;
    }
    let idVenta = null;
    let keysTexts = ["igvTotal", "total", "vuelto", "igvTotal", "subTotal","descuentoTotal"];
    let tablaTdTotal = document.querySelector("#subTotalInfo"); 
    let listaProductos = [];
    tablaVentas.onclick = async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            try{
                const response = await general.funcfetch("general/listar/" + e.target.dataset.venta,null,"GET");
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                idVenta = e.target.dataset.venta;
                tablaDetalleVenta.innerHTML = "";
                listaProductos = [];
                for (const key in response.venta) {
                    if (Object.hasOwnProperty.call(response.venta, key)) {
                        const valor = response.venta[key];
                        const $dom = document.querySelector("#idModal"+key);
                        if (keysTexts.indexOf(key) >= 0) {
                            $dom.textContent = general.monedaSoles(valor);
                            if(key == "total"){
                                tablaTdTotal.textContent = general.monedaSoles(response.venta.subTotal - response.venta.igvTotal);
                            }
                            continue;
                        }
                        if (key == "detalleVentas"){
                            valor.forEach(dv => {
                                listaProductos.push({
                                    idProducto: dv.productoFk,
                                    idDetalleProducto: dv.id,
                                    costoProducto: dv.costo,
                                    descuentoProducto: dv.descuento,
                                    cantidadProducto: dv.cantidad,
                                    subTotalProducto: dv.importe
                                });
                                tablaDetalleVenta.append(agregarDetalleVenta(listaProductos.length, dv.productoFk, dv.id, general.urlProductos + dv.urlImagen, dv.nombreProducto, dv.costo, dv.perecederos, dv.fechaPerecedero));
                            });
                            continue;
                        }
                        if (key == "clientes" && response.venta.clientes){
                            document.querySelector("#idModalTipoDocumentoCliente").value = response.venta.clientes.tipoDocumento;
                            document.querySelector("#idModalnroDocumentoCliente").value = response.venta.clientes.nroDocumento;
                            continue;
                        }
                        if(!$dom){
                            continue;
                        }
                        $dom.value = valor;
                    }
                }
                $("#editarVenta .select2-simple").trigger("change");
                // response.detalleCompra.forEach(cd => {
                //     const precioProd = isNaN(parseFloat(cd.pivot.precio)) ? 0 : parseFloat(cd.pivot.precio);
                //     listaCompras.push({
                //         id: cd.pivot.productoFk,
                //         cantidad: cd.pivot.cantidad,
                //         precio: precioProd
                //     });
                //     renderProducto(cd.pivot.productoFk, cd.pivot.cantidad, cd.nombreProducto, precioProd, general.urlProductos + cd.urlImagen,"actualizar");
                // });
                $("#editarVenta").modal("show");
            }catch(error){
                console.error(error);
            }
        }
        if (e.target.classList.contains("btn-outline-danger")){
            alertify.confirm("Alerta","¿Deseas eliminar esta compra?",async ()=>{
                const response = await general.funcfetch("general/eliminar/" + e.target.dataset.venta, null, "DELETE");
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                tablaVentasDataTable.draw();
                return alertify.success(response.success);
            },()=>{})
        }
    }
    // $('#editarVenta').modal("show");
}
window.addEventListener("DOMContentLoaded",loadPage);