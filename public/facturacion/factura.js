function loadPage() {
    let general = new General();
    const tablaFactura = document.querySelector("#tablaFactura");
    let listaComprobante = {
        id : null,
        serie : null,
        correlativo : null
    };
    const frmEliminar = document.querySelector("#eliminarFacturaForm");
    tablaFactura.addEventListener("click",async function(e){
        if(e.target.classList.contains("btn-danger")){
            for (const key in e.target.dataset) {
                if (Object.hasOwnProperty.call(e.target.dataset, key)) {
                    const valor = e.target.dataset[key];
                    const dom = document.querySelector("#eliminarFactura #txt" + key);
                    listaComprobante[key] = valor;
                    if(!dom){
                        continue;
                    }
                    dom.textContent = valor;
                }
            }
            $('#eliminarFactura').modal("show");
            console.log(listaComprobante);
        }
    });
    frmEliminar.addEventListener("submit",function(e){
        e.preventDefault();
        if(!listaComprobante.id){
            return alertify.error("no se definio el comprobante");
        }
        alertify.confirm("Alerta","¿Estas seguro de eliminar el comprobante " + listaComprobante.serie + "-" + listaComprobante.correlativo + "?",async() => {
            try {
                general.banerLoader.hidden = false;
                let datos = new FormData(frmEliminar);
                datos.append("comprobante",JSON.stringify(listaComprobante));
                const response = await general.funcfetch("facturar/eliminar",datos,"POST");
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                if(response.success){
                    return alertify.alert("Mensaje",response.success, () => { 
                        $('#eliminarFactura').modal("hide");
                        window.location.reload() 
                    });
                }
                return alertify.alert("Alerta",response.error);
            } catch (error) {
                console.error(error);
                alertify.alert("Alerta","Ocurrió un error al anular este comprobante, por favor intentelo nuevamebte más tarde");
            }finally{
                general.banerLoader.hidden = true;
            }
        },()=>{})
        
    })
    document.querySelector("#btEliminar").onclick = e => document.querySelector("#btnEnviar").click();
    $('#eliminarFactura').on("hidden.bs.modal",function(e){
        frmEliminar.reset();
        listaComprobante = {
            id : null,
            serie : null,
            correlativo : null
        }
    });
}
window.addEventListener("DOMContentLoaded",loadPage);