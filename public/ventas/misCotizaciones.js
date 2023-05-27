function loadPage() {
    let general = new General();
    const tablaVentas = document.querySelector("#tablaVentas");
    const tablaVentasDataTable = $(tablaVentas).DataTable({
        ajax: {
            url: 'listar',
            method: 'POST',
            headers: general.requestJson,
            data: function (d) {
                d.productos = $("#productoBuscar").val();
            }
        },
        columns: [
            {
                data: 'nroCotizacion'
            },
            {
                data: 'fechaCotizacion'
            },
            {
                data: 'cotizador'
            },
            {
                data: 'cliente',
                render: function(data){
                    return !data ? 'No establecido' : data;
                }
            },
            {
                data: 'metodoEnvio'
            },
            {
                data: 'tipoPago'
            },
            {
                data: 'importe',
                render: function (dato) {
                    return !dato ? 'Sin subtotal' : general.monedaSoles(dato);
                }
            },
            {
                data: 'igv',
                render: function (dato) {
                    return !dato ? 'Sin I.G.V' : general.monedaSoles(dato);
                }
            },
            {
                data: 'descuento',
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
                    return `
                <div class="d-flex justify-content-center" style="gap:5px;"><a href="${general.urlCotizacionComprobante + data}" target="_blank" class="btn btn-sm btn-outline-primary p-1">
                    <small>
                    <i class="fas fa-clipboard-check"></i>
                    Comprobante
                    </small>
                </a>
                <button class="btn btn-sm btn-outline-info p-1" data-venta="${data}">
                    <small>    
                    <i class="fas fa-pencil"></i>
                        Editar
                    </small>
                </button>
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
    tablaVentas.onclick = async function (e) {
        // if (e.target.classList.contains("btn-outline-info")) {
        //     try {
        //         const response = await general.funcfetch("general/listar/" + e.target.dataset.venta, null, "GET");
        //         if (response.session) {
        //             return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
        //         }
        //         idVenta = e.target.dataset.venta;
        //         tablaDetalleVenta.innerHTML = "";
        //         listaProductos = [];
        //         for (const key in response.venta) {
        //             if (Object.hasOwnProperty.call(response.venta, key)) {
        //                 const valor = response.venta[key];
        //                 const $dom = document.querySelector("#idModal" + key);
        //                 if (keysTexts.indexOf(key) >= 0) {
        //                     $dom.textContent = general.monedaSoles(valor);
        //                     if (key == "total") {
        //                         tablaTdTotal.textContent = general.monedaSoles(response.venta.subTotal - response.venta.igvTotal);
        //                     }
        //                     continue;
        //                 }
        //                 if (key == "detalleVentas") {
        //                     valor.forEach(dv => {
        //                         listaProductos.push({
        //                             idProducto: dv.productoFk,
        //                             idDetalleProducto: dv.id,
        //                             costoProducto: dv.costo,
        //                             descuentoProducto: dv.descuento,
        //                             cantidadProducto: dv.cantidad,
        //                             subTotalProducto: dv.importe
        //                         });
        //                         tablaDetalleVenta.append(agregarDetalleVenta(listaProductos.length, dv.productoFk, dv.id, general.urlProductos + dv.urlImagen, dv.nombreProducto, dv.costo, dv.perecederos, dv.fechaPerecedero));
        //                     });
        //                     continue;
        //                 }
        //                 if (key == "clientes" && response.venta.clientes) {
        //                     document.querySelector("#idModalTipoDocumentoCliente").value = response.venta.clientes.tipoDocumento;
        //                     document.querySelector("#idModalnroDocumentoCliente").value = response.venta.clientes.nroDocumento;
        //                     continue;
        //                 }
        //                 if (!$dom) {
        //                     continue;
        //                 }
        //                 $dom.value = valor;
        //             }
        //         }
        //         $("#editarVenta .select2-simple").trigger("change");
        //         // response.detalleCompra.forEach(cd => {
        //         //     const precioProd = isNaN(parseFloat(cd.pivot.precio)) ? 0 : parseFloat(cd.pivot.precio);
        //         //     listaCompras.push({
        //         //         id: cd.pivot.productoFk,
        //         //         cantidad: cd.pivot.cantidad,
        //         //         precio: precioProd
        //         //     });
        //         //     renderProducto(cd.pivot.productoFk, cd.pivot.cantidad, cd.nombreProducto, precioProd, general.urlProductos + cd.urlImagen,"actualizar");
        //         // });
        //         $("#editarVenta").modal("show");
        //     } catch (error) {
        //         console.error(error);
        //     }
        // }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta", "¿Deseas eliminar esta cotización?", async () => {
                const response = await general.funcfetch("eliminar/" + e.target.dataset.venta, null, "DELETE");
                if (response.session) {
                    return alertify.alert([...general.alertaSesion], () => { window.location.reload() });
                }
                tablaVentasDataTable.draw();
                return alertify.success(response.success);
            }, () => { })
        }
    }
    // $('#editarVenta').modal("show");
}
window.addEventListener("DOMContentLoaded", loadPage);