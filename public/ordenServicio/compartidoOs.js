class OrdenServicio extends General {
    eliminarServicio = (event,listaServicio,tablaServicios) => {
        const valor = event.target.dataset.cotizacionServicio;
        const tipo = event.target.dataset.tipo;
        let lista = listaServicio.filter(function(detalle){
            if(+detalle.idCotizacionServicio === +valor && detalle.tipoServicioProducto === tipo){
                return false;
            }
            return true;
        });
        if(!lista.length){
            tablaServicios.innerHTML = `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios y/o productos</td></tr>`;
        }else{
            event.target.parentElement.parentElement.remove();
        }
        return lista;
    }
    obtenerOrdenServicio(listaOrdenServicios){
        let template = "";
        listaOrdenServicios.forEach(ordenServicio => {
            template = `<option value="${ordenServicio.id}">${ordenServicio.nroOs}</option>`;
        });
        return template;
    }
    generarServiciosAdicionales = ({
        index,
        idAdicional,
        descripcion,
        precioUnitario,
        cantidad,
        total,
        tipoMoneda
    }) => {
        const tr = document.createElement("tr");
        let $adicional = "";
        if(idAdicional){
            $adicional = document.createElement("input");
            $adicional.name = "idAdicional[]";
            $adicional.setAttribute("value",idAdicional);
            $adicional.hidden = true;
            $adicional = $adicional.outerHTML;
        }
        
        tr.innerHTML = `
        <td>${index}${$adicional}</td>
        <td><input type="text" name="descripcion[]" required class="form-control form-control-sm descripcion-servicios" value="${descripcion}"></td>
        <td><input type="number" name="precio[]" step="0.01" required min="0.00" class="form-control form-control-sm punitari-servicios" value="${precioUnitario}"></td>
        <td><input type="number" name="cantidad[]" min="1" required class="form-control form-control-sm cantidad-servicios" value="${cantidad}"></td>
        <td>${this.resetearMoneda(total,tipoMoneda)}</td>
        <td class="text-center"><button class="btn btn-sm btn-danger" ${idAdicional ? 'data-adicional="' + idAdicional + '"' : ''} type="button"><i class="fas fa-trash-alt"></i></button></td>
        `;
        return tr;
    }
    agregarDetallServicios = ({idOsCotizacion,index,nroCotizacion,servicio,cantidad,importe,descuento,total,idCotizacionServicio,tipoServicioProducto,tipoMoneda}) => {
        let tr = document.createElement("tr");
        if(idOsCotizacion){
            tr.dataset.ordenServicioCotizacion = idOsCotizacion;
        }
        tr.innerHTML = `
        <tr>
            <td>${index}</td>
            <td>${nroCotizacion}</td>
            <td>${servicio}</td>
            <td>${cantidad}</td>
            <td>${this.resetearMoneda(importe,tipoMoneda)}</td>
            <td>${this.resetearMoneda(descuento,tipoMoneda)}</td>
            <td>${this.resetearMoneda(total,tipoMoneda)}</td>
            <td class="text-center"><button class="btn btn-sm btn-danger" type="button" data-cotizacion-servicio="${idCotizacionServicio}" data-tipo="${tipoServicioProducto}"><i class="fas fa-trash-alt"></i></button></td>
        </tr>
        `
        return tr;
    }
    calcularServiciosTotales = (listaServicios,tablaServiciosAdicionales,tipoMoneda) => {
        let descuento = 0;
        let total = 0;
        listaServicios.forEach((cp) => {
            descuento += parseFloat(cp.descuento);
            total += parseFloat(cp.total);
        });
        document.querySelector("#txtSubTotal").textContent = this.resetearMoneda(
            total - total * 0.18,tipoMoneda
        );
        document.querySelector("#txtDescuento").textContent =
            "-" + this.resetearMoneda(descuento,tipoMoneda);
        document.querySelector("#txtIGV").textContent = this.resetearMoneda(
            total * 0.18,tipoMoneda
        );
        let totalDetalle = 0;
        for (const tr of tablaServiciosAdicionales.children) {
            const cantidad = tr.querySelector(".cantidad-servicios");
            const precio = tr.querySelector(".punitari-servicios");
            if (!cantidad || !precio) {
                continue;
            }
            const subTotal = cantidad.value * precio.value;
            tr.children[tr.children.length - 2].textContent = this.resetearMoneda(subTotal,tipoMoneda);
            totalDetalle += subTotal;
        }
        document.querySelector("#txtCostoAdicional").textContent = this.resetearMoneda(totalDetalle,tipoMoneda);
        document.querySelector("#txtTotal").textContent = this.resetearMoneda(total - descuento + totalDetalle,tipoMoneda);
    }
    calcularMonto = ({e,listaServicios,tablaServiciosAdicionales,tipoMoneda}) => {
        const step = !e.target.step ? 1 : parseFloat(e.target.step);
        const value = !e.target.step
            ? parseInt(e.target.value)
            : parseFloat(e.target.value);
        if (isNaN(value)) {
            e.target.value = step;
        }
        this.calcularServiciosTotales(listaServicios,tablaServiciosAdicionales,tipoMoneda);
    }
    agregarServiciosAdicionales = (tablaServiciosAdicionales,listaServicios,tipoMoneda) => {
        const index =
            tablaServiciosAdicionales.dataset.tipo == "vacio"
                ? 1
                : tablaServiciosAdicionales.children.length + 1;
        let datos = {
            index,
            idAdicional : null,
            descripcion: "",
            precioUnitario: "",
            cantidad: 1,
            total: 0,
            tipoMoneda
        };
        if (index === 1) {
            tablaServiciosAdicionales.dataset.tipo = "lleno";
            tablaServiciosAdicionales.innerHTML = "";
        }
        const trDetalle = this.generarServiciosAdicionales(datos);
        tablaServiciosAdicionales.append(trDetalle);
        for (const input of trDetalle.querySelectorAll(
            ".punitari-servicios, .cantidad-servicios"
        )) {
            input.addEventListener("change", e => {this.calcularMonto({e,listaServicios,tablaServiciosAdicionales,tipoMoneda})});
        }
        return true;
    }
}