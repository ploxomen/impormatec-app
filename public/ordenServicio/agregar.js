function loadPage(){
    let gen = new General();
    let cbClientes = document.querySelector("#cbClientes");
    let tablaServicios = document.querySelector("#contenidoServicios");
    let tablaServiciosAdicionales = document.querySelector("#tablaServiciosAdicionales");

    let listaServicios = [];
    let listaServiciosAdicionales = [];
    $(cbClientes).on("select2:select", async function (e) {
        const idClientes = $(this).val();
        listaServicios = [];
        if(idClientes == "ninguno"){
            calcularServiciosTotales();
            return false;
        }
        try {
            const response = await gen.funcfetch("clientes/" + idClientes, null, "GET");
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            listaServicios = response.servicios;
            let template = "";
            response.servicios.forEach((servicio,key) => {
                template += `<tr>
                <td>${key + 1}</td>
                <td>${servicio.nroCotizacion}</td>
                <td>${servicio.servicio}</td>
                <td>${servicio.cantidad}</td>
                <td>${gen.resetearMoneda(servicio.importe)}</td>
                <td>${gen.resetearMoneda(servicio.descuento)}</td>
                <td>${gen.resetearMoneda(servicio.total)}</td>
                <td class="text-center"><button class="btn btn-sm btn-danger" type="button"><i class="fas fa-trash-alt"></i></button></td>
                </tr>`
            });
            tablaServicios.innerHTML = !template ? `<tr><td colspan="100%" class="text-center">No se seleccionaron servicios</td></tr>` : template;
            calcularServiciosTotales();
            
        } catch (error) {
            alertify.error("error al obtener las cotizaciones aprobadas");
            console.error(error);
        }
    });
    const btnAgregarServiciosAdicionales = document.querySelector("#btnAgregarServiciosAdicionales");
    btnAgregarServiciosAdicionales.onclick = function(e){
        const index = tablaServiciosAdicionales.dataset.tipo == "vacio" ? 1 : tablaServiciosAdicionales.children.length + 1;
        let datos = {
            index,
            descripcion : "",
            precioUnitario : "",
            cantidad : 1,
            total : 0
        }
        if(index === 1){
            tablaServiciosAdicionales.dataset.tipo = "lleno";
            tablaServiciosAdicionales.innerHTML = "";
        }
        tablaServiciosAdicionales.append(generarServiciosAdicionales(datos));
    }
    function generarServiciosAdicionales({index,descripcion,precioUnitario,cantidad,total}){
        const tr = document.createElement("tr");
        tr.innerHTML = `
        <td>${index}</td>
        <td><input type="text" required class="form-control form-control-sm descripcion-servicios" value="${descripcion}"></td>
        <td><input type="number" step="0.01" required min="0.00" class="form-control form-control-sm punitari-servicios" value="${precioUnitario}"></td>
        <td><input type="number" min="1" required class="form-control form-control-sm cantidad-servicios" value="${cantidad}"></td>
        <td>${gen.resetearMoneda(total)}</td>
        <td class="text-center"><button class="btn btn-sm btn-danger" type="button"><i class="fas fa-trash-alt"></i></button></td>
        `
        return tr;
    }
    function calcularServiciosTotales() {
        let descuento = 0;
        let total = 0;
        listaServicios.forEach(cp => {
            descuento += parseFloat(cp.descuento);
            total += parseFloat(cp.total);
        });
        document.querySelector("#txtSubTotal").textContent = gen.monedaSoles(total - total * 0.18);
        document.querySelector("#txtDescuento").textContent = "-" + gen.monedaSoles(descuento);
        document.querySelector("#txtIGV").textContent = gen.monedaSoles(total * 0.18);
        document.querySelector("#txtTotal").textContent = gen.monedaSoles(total - descuento);
    }
}
window.addEventListener("DOMContentLoaded",loadPage);