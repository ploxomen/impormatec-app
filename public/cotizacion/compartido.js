class Cotizacion {
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
    almacenProductos({index,idProducto,urlImagen,nombreProducto,listaAlmacenes,cantidadProducto}){
        const tr = document.createElement("tr");
        tr.dataset.producto = idProducto;
        tr.innerHTML =`
            <td>${index}</td>
            <td><img class="img-vistas-pequena" src="${urlImagen}" alt="Imagen del producto"></td>
            <td>${nombreProducto}</td>
            <td>${cantidadProducto}</td>
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
}