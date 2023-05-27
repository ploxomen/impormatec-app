function loadPage(){
    let gen = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change",gen.switchs);
    }
    const tablaPerecedero = document.querySelector("#tablaPerecedero");
    const tablaPerecederoDatatable = $(tablaPerecedero).DataTable({
        ajax: {
            url: 'perecederos/listar',
            method: 'POST',
            headers: gen.requestJson,
        },
        columns: [{
            data: 'id',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data: 'id',
            render: function(data,type,row){
                return !row.codigoBarra ? data.toString().padStart(5,"0") : row.codigoBarra;
            }
        },
        {
            data: 'productos.nombreProducto',
            name : 'productos.nombreProducto',
        },
        {
            data: 'productos.marca.nombreMarca',
            name : 'productos.marca.nombreMarca'
        },
        {
            data : 'productos.categoria.nombreCategoria',
            name : 'productos.categoria.nombreCategoria'
        },
        {
            data: 'productos.presentacion.siglas',
            name: 'productos.presentacion.siglas'
        },
        {
            data: 'vencimiento'
        },
        {
            data: 'cantidad'
        },
        {
            data : 'estado',
            render : function(data,type,row){
                let estado = "";
                switch(data){
                    case 1:
                        estado = '<span class="badge badge-success">Activo</span>';
                    break;
                    case 0:
                        estado = '<span class="badge badge-info">Descontinuado</span>';
                    break;
                }
                let fechaActual = new Date();
                let fechaVencimiento = new Date(row.vencimiento);
                if(fechaVencimiento < fechaActual){
                    estado = '<span class="badge badge-info">Vencido</span>';
                }
                return estado;  
            }
        },
        {
            data: 'id',
            render : function(data){
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-perecedero="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-perecedero="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    let idPerecedero = null;
    
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    const formPerecedero = document.querySelector("#formPerecedero");
    formPerecedero.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            gen.cargandoPeticion(btnModalSave, gen.claseSpinner, true);
            const response = await gen.funcfetch(idPerecedero ? "perecederos/editar/" + idPerecedero : "perecederos/crear",datos);
            if(response.session){
                return alertify.alert([...alertaSesion],() => {window.location.reload()});
            }
            if(response.error){
                return alertify.alert("Alerta",response.error);
            }
            alertify.success(response.success);
            tablaPerecederoDatatable.draw();
            $('#agregarPerecedero').modal("hide");
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar un perecedero");
        }finally{
            gen.cargandoPeticion(btnModalSave, 'fas fa-save', false);
        }
    });
    const modalTitulo = document.querySelector("#tituloPerecedero");
    $('#agregarPerecedero').on("hidden.bs.modal",function(e){
        idPerecedero = null;
        modalTitulo.textContent = "Agregar Perecedero";
        switchEstado.disabled = true;
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        formPerecedero.reset();
        $('#agregarPerecedero .select2-simple').trigger("change");
    });
    const switchEstado = document.querySelector("#idModalestado");
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    tablaPerecedero.addEventListener("click",async function(e){
        if (e.target.classList.contains("btn-outline-info")){
            btnModalSave.querySelector("span").textContent = "Editar";
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("perecederos/listar/" + e.target.dataset.perecedero,null,"GET");
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                modalTitulo.textContent = "Editar Perecedero";
                idPerecedero = e.target.dataset.perecedero;
                for (const key in response.perecedero) {
                    if (Object.hasOwnProperty.call(response.perecedero, key)) {
                        const valor = response.perecedero[key];
                        const dom = document.querySelector("#idModal" + key);
                        if(!dom){
                            continue;
                        }
                        if (key == "estado"){
                            switchEstado.checked = valor === 1 ? true : false;
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#agregarPerecedero .select2-simple').trigger("change");
                switchEstado.disabled = false;
                $('#agregarPerecedero').modal("show");
            } catch (error) {
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                console.error(error);
                alertify.error("error al obtener el perecedero");
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta","¿Estás seguro de eliminar este perecedero?",async ()=>{
                try {
                    gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                    const response = await gen.funcfetch("perecederos/eliminar/" + e.target.dataset.perecedero, null,"DELETE");
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    tablaPerecederoDatatable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    console.error(error);
                    alertify.error("error al eliminar el perecedero");
                }
            },()=>{});
            
        }
    })
}
window.addEventListener("DOMContentLoaded",loadPage);

