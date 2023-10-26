function loadPage(){
    let partes = window.location.href.split('/');
    let cajaChicaId = partes[partes.length - 1];
    let gen = new General();
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    const formCajaChica = document.querySelector("#formCajaChica");
    const tablaGastos = document.querySelector("#tablaGastos");
    const tablaGastosDataTable = $(tablaGastos).DataTable({
        ajax: {
            url: `listar-gastos/${cajaChicaId}`,
            method: 'GET',
            headers: gen.requestJson
        },
        columns: [
        {
            data: 'nroGastoDetalle'
        },
        {
            data: 'fechaGasto'
        },
        {
            data: 'nroOrdenServicio'
        }
        ,
        {
            data: 'proveedor'
        },
        {
            data: 'area_costo'
        },
        {
            data: 'descripcion_producto'
        },
        {
            data: 'monto_total_cambio',
            render:function(data,tye,row){
                return gen.resetearMoneda(data,row.tipo_moneda);
            }
        },
        {
            data: 'igv',
            render:function(data,tye,row){
                return gen.resetearMoneda(data,row.tipo_moneda);
            }
        },
        {
            data: 'idDetalle',
            render : function(data,type,row){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-detalle="${data}" data-caja="${row.idCaja}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-detalle="${data}" data-caja="${row.idCaja}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                    Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    let idDetalleGasto = null;
    formCajaChica.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            gen.cargandoPeticion(btnModalSave, gen.claseSpinner, true);
            const response = await gen.funcfetch(idDetalleGasto ? `actualizar-gasto/${cajaChicaId}/${idDetalleGasto}` : `agregar-gasto/${cajaChicaId}`,datos);
            if(response.session){
                return alertify.alert([...gen.alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Error",response.error);
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            $('#agragarGastos').modal("hide");
            alertify.alert("Mensaje",response.success);
            for (const key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                    const valor = response[key];
                    const dom = document.querySelector("#idGeneral" + key);
                    if(!dom){
                        continue;
                    }
                    dom.textContent = gen.resetearMoneda(valor,response.tipoMoneda);
                }
            }
            tablaGastosDataTable.draw();
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar gastos a la caja chica");
        }finally{
            gen.cargandoPeticion(btnModalSave, 'fas fa-save', false);
        }
    });
    tablaGastos.addEventListener("click",async function(e){
        if(e.target.classList.contains("btn-outline-info")){
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch(`listar-gasto/${e.target.dataset.detalle}/${e.target.dataset.caja}`,null,"GET");
                if(response.session){
                    return alertify.error(response.session);
                }
                if(response.alerta){
                    return alertify.alert("Alerta",response.alerta);
                }
                idDetalleGasto = response.gasto.id;
                for (const key in response.gasto) {
                    if (Object.hasOwnProperty.call(response.gasto, key)) {
                        const valor = response.gasto[key];
                        const dom = document.querySelector("#agragarGastos #idModal" + key);
                        if(key === 'id_os' && !valor){
                            dom.value = 'NINGUNO';
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#agragarGastos .select2-simple').trigger("change");
                $('#agragarGastos').modal("show");
            } catch (error) {
                alertify.error("error al visualizar el gasto");
            }finally{
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
            }
        }
        if(e.target.classList.contains("btn-outline-danger")){
            alertify.confirm("Alerta","¿Deseas eliminar este gasto?",async ()=>{
                try {
                    const response = await gen.funcfetch(`eliminar/${e.target.dataset.detalle}/${e.target.dataset.caja}`,null,"DELETE");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.alerta){
                        return alertify.alert("Alerta",response.alerta);
                    }
                    for (const key in response) {
                        if (Object.hasOwnProperty.call(response, key)) {
                            const valor = response[key];
                            const dom = document.querySelector("#idGeneral" + key);
                            if(!dom){
                                continue;
                            }
                            dom.textContent = gen.resetearMoneda(valor,response.tipoMoneda);
                        }
                    }
                    alertify.success(response.success);
                    tablaGastosDataTable.draw();
                    
                } catch (error) {
                    alertify.error("error al eliminar el gasto");
                }
            },()=>{})
            
        }
    });
    document.querySelector("#idModalmonto_total").oninput = e => {
        let valor = Number.parseFloat(e.target.value);
        if(isNaN(valor)){
            valor = 0;
        }
        document.querySelector("#idModaligv").value = (valor * 0.18).toFixed(2);
    }
    $('#agragarGastos').on("hidden.bs.modal",function(e){
        formCajaChica.reset();
        $('#agragarGastos .select2-simple').trigger("change");
        idDetalleGasto = null;
    });
}
window.addEventListener("DOMContentLoaded",loadPage);