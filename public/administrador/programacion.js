function loadPage() {
    let general = new General();
    $('[data-toggle="tooltip"]').tooltip();
    const tablaProgramacion = document.querySelector("#tablaProgramacion");
    const tablaProgramacionDatatable = $(tablaProgramacion).DataTable({
        ajax: {
            url: 'programacion/listar',
            method: 'GET',
            headers: general.requestJson,
            data: function (d) {
                d.reponsable = $("#cbResponsables").val();
                d.fechaHrInicio = $("#txtFechaInicio").val();
                d.fechaHrFin = $("#txtFechaFin").val();
            }
        },
        columns: [{
            data: 'id',
            render: function(data,type,row, meta){
                return meta.row + 1;
            }
        },
        {
            data: 'nombres',
            render : function(data,type,row){
                return data + " " + row.apellidos;
            }
        },
        {
            data: 'fecha_hr_inicio'
        },
        {
            data: 'fecha_hr_fin'
        },
        {
            data: 'tipo'
        },
        {
            data: 'tarea'
        },
        {
            data: 'id',
            render : function(data,type,row){
                if(row.tipo === "VISITA"){
                    return '';
                }
                return `
                <div class="d-flex justify-content-center" style="gap:5px;">
                    <button class="btn btn-sm btn-outline-info p-1" data-programacion="${data}">
                        <small>
                            <i class="fas fa-pencil-alt"></i>
                            <span>Editar</span>
                        </small>
                    </button>
                    <button class="btn btn-sm btn-outline-danger p-1" data-programacion="${data}">
                        <small>
                            <i class="fas fa-trash-alt"></i>
                            <span>Eliminar</span>
                        </small>
                    </button>
                </div>`
            }
        },
        ]
    });
    const btnAgregarActividadLista = document.querySelector("#btnAgregarActividad");
    const $listaActividades = document.querySelector("#listaActividades");
    let numeroActividades = 0;
    btnAgregarActividadLista.addEventListener("click",function(e){
        e.preventDefault();
        numeroActividades++;
        const li = general.creacionDOM("li");
        li.innerHTML = `
        <div class="form-row">
            <div class="form-group col-12 col-md-6">
                <label for="echaInicioActividad${numeroActividades}">Fecha Hr. Inicio</label>
                <input type="datetime-local" class="form-control form-control-sm" required id="fechaInicioActividad${numeroActividades}" name="fechaHrInicio[]"/>
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="fechaFinActividad${numeroActividades}">Fecha Hr. Fin</label>
                <input type="datetime-local" class="form-control form-control-sm" required id="fechaFinActividad${numeroActividades}" name="fechaHrFin[]"/>
            </div>
            <div class="form-group col-12">
                <label for="tareaActividad${numeroActividades}">Descripción de la tarea</label>
                <textarea class="form-control form-control-sm" row="3" required id="tareaActividad${numeroActividades}" name="tarea[]"></textarea>
            </div>
        </div>
        `
        $listaActividades.append(li);
    });
    btnAgregarActividadLista.click();
    const btnFrmAgregarProgramacion = document.querySelector("#btnGuardarFrm");
    const btnFrmEditarProgramacion = document.querySelector("#btnGuardarFrmEditar");

    btnFrmAgregarProgramacion.onclick = e => document.querySelector("#btnFrmEnviar").click();
    btnFrmEditarProgramacion.onclick = e => document.querySelector("#btnFrmEnviarEditar").click();

    let idEditarProgramacion = null;
    tablaProgramacion.addEventListener("click",async function(e){
        if(e.target.classList.contains("btn-outline-info")){
            try {
                general.cargandoPeticion(e.target, general.claseSpinner, true);
                const response = await general.funcfetch("programacion/" + e.target.dataset.programacion,null,"GET");
                if(!response.programacion){
                    throw Error("No se devolvio el objeto de programacion");
                }
                idEditarProgramacion = response.programacion.id;
                for (const key in response.programacion) {
                    if (Object.hasOwnProperty.call(response.programacion, key)) {
                        const valor = response.programacion[key];
                        const dom = document.querySelector("#editarProgramacion #editarModal" + key);
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('#editarModalid_usuario').trigger("change");
                $('#editarProgramacion').modal("show");
            } catch (error) {
                alertify.error("error al agregar nuevas actividades");
            }finally{
                general.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
            }
        }
        if(e.target.classList.contains("btn-outline-danger")){
            alertify.confirm("Alerta","¿Deseas eliminar esta actividad?",async ()=>{
                try {
                    const response = await general.funcfetch("programacion/" + e.target.dataset.programacion,null,"DELETE");
                    if(response.session){
                        return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
                    }
                    if(response.success){
                        tablaProgramacionDatatable.draw();
                    }
                } catch (error) {
                    alertify.error("error al eliminar la actividad");
                }
            },()=>{})
            
        }
    });
    document.querySelector("#btnAplicarFiltros").onclick = e => tablaProgramacionDatatable.draw();
    const frmAgregarProgramacion = document.querySelector("#formAgregarProgramacion");
    const frmEditarProgramacion = document.querySelector("#formEditarProgramacion");
    frmAgregarProgramacion.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            general.cargandoPeticion(btnFrmAgregarProgramacion, general.claseSpinner, true);
            const response = await general.funcfetch("programacion/agregar",datos,"POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.success){
                alertify.success(response.success);
                $('#agregarProgramacion').modal("hide");
                tablaProgramacionDatatable.draw();
            }
        } catch (error) {
            alertify.error("error al agregar nuevas actividades");
        }finally{
            general.cargandoPeticion(btnFrmAgregarProgramacion, 'fas fa-save', false);
        }
    });
    frmEditarProgramacion.addEventListener("submit",async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        try {
            general.cargandoPeticion(btnFrmEditarProgramacion, general.claseSpinner, true);
            const response = await general.funcfetch("programacion/" + idEditarProgramacion,datos,"POST");
            if(response.session){
                return alertify.alert([...general.alertaSesion],() => {window.location.reload()});
            }
            if(response.alerta){
                return alertify.alert("Alerta",response.alerta);
            }
            if(response.success){
                alertify.success(response.success);
                $('#editarProgramacion').modal("hide");
                tablaProgramacionDatatable.draw();
            }
        } catch (error) {
            alertify.error("error al agregar editar la actividad");
        }finally{
            general.cargandoPeticion(btnFrmEditarProgramacion, 'fas fa-save', false);
        }
    });
    $('#agregarProgramacion').on("hidden.bs.modal",function(e){
        numeroActividades = 0;
        $('#cbResponsablesAgregar').val("").trigger("change");
        $listaActividades.innerHTML = "";
        btnAgregarActividadLista.click();
    });
    $('#editarProgramacion').on("hidden.bs.modal",function(e){
        $('#editarModalid_usuario').val("").trigger("change");
        frmEditarProgramacion.reset();
        idEditarProgramacion = null;
    });

}
window.addEventListener("DOMContentLoaded",loadPage);