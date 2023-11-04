function loadPage() {
    let gen = new General();
    for (const swhitchOn of document.querySelectorAll(".change-switch")) {
        swhitchOn.addEventListener("change", gen.switchs);
    }
    for (const cambioCantidad of document.querySelectorAll('.cambiar-cantidad')) {
        cambioCantidad.addEventListener("click", gen.aumentarDisminuir);
    }
    const tablaComprobantes = document.querySelector("#tablaComprobantes");
    const tablaComprobantesDatatable = $(tablaComprobantes).DataTable({
        ajax: {
            url: 'comprobantes/listar',
            method: 'POST',
            headers: gen.requestJson
        },
        columns: [{
            data: 'id',
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        },
        {
            data: 'comprobante',
            render: gen.validarVacio
        },
        {
            data: 'serie',
            render: gen.validarVacio
        },
        {
            data: 'inicio',
            render: gen.validarVacio
        },
        {
            data: 'fin',
            render: gen.validarVacio
        },
        {
            data: 'disponibles',
            render: gen.validarVacio
        },
        {
            data: 'utilizados',
            render: gen.validarVacio
        },
        {
            data: 'estado',
            render: function (data) {
                if (data === 1) {
                    return '<span class="badge badge-success">Activo</span>';
                } else if (data === 0) {
                    return '<span class="badge badge-danger">Descontinuado</span>';
                } else {
                    return '<span class="text-danget">No establecido</span>';
                }
            }
        },
        {
            data: 'id',
            render: function (data) {
                return `<div class="d-flex justify-content-center" style="gap:5px;"><button class="btn btn-sm btn-outline-info p-1" data-comprobante="${data}">
                    <small>
                    <i class="fas fa-pencil-alt"></i>
                    Editar
                    </small>
                </button>
                <button class="btn btn-sm btn-outline-danger p-1" data-comprobante="${data}">
                    <small>    
                    <i class="fas fa-trash-alt"></i>
                        Eliminar
                    </small>
                </button></div>`
            }
        },
        ]
    });
    const txtInicioComprobante = document.querySelector("#idModalinicio");
    const txtFinComprobante = document.querySelector("#idModalfin");
    const txtDisponibleComprobante = document.querySelector("#idModaldisponibles");
    const txtUtilizadoComprobante = document.querySelector("#idModalutilizados");
    function restaurarComprobantes(){
        if (isNaN(+txtInicioComprobante.value)) {
            txtInicioComprobante.value = 1;
        }
        if (isNaN(+txtFinComprobante.value)) {
            txtFinComprobante.value = 2;
        }
        if (isNaN(+txtUtilizadoComprobante.value)) {
            txtUtilizadoComprobante.value = 0;
        }
    }
    function calcularComprobanetDisponible(){
        const valorInicio = isNaN(+txtInicioComprobante.value) ? 1 : parseInt(txtInicioComprobante.value);
        const valorFin = isNaN(+txtFinComprobante.value) ? 1 : parseInt(txtFinComprobante.value);
        const valorUtilizado = isNaN(+txtUtilizadoComprobante.value) ? 0 : parseInt(txtUtilizadoComprobante.value);
        const limiteComprobantes = valorFin - (valorInicio - 1) - valorUtilizado;
        txtDisponibleComprobante.value = limiteComprobantes;
        restaurarComprobantes()
    }
    function cambioComprobanteDisponibilidad(){
        const valorDisponible = isNaN(+txtDisponibleComprobante.value) ? 0 : parseInt(txtDisponibleComprobante.value);
        const valorUtilizado = isNaN(+txtUtilizadoComprobante.value) ? 0 : parseInt(txtUtilizadoComprobante.value);
        const valorFin = isNaN(+txtFinComprobante.value) ? 1 : parseInt(txtFinComprobante.value);
        const valorResultado = (valorDisponible + valorUtilizado) - valorFin;
        txtFinComprobante.value = valorFin + valorResultado;
        restaurarComprobantes();
    }
    function cambioComprobanteUtilizado(){
        const valorInicio = isNaN(+txtInicioComprobante.value) ? 1 : parseInt(txtInicioComprobante.value);
        const valorFin = isNaN(+txtFinComprobante.value) ? 1 : parseInt(txtFinComprobante.value);
        const valorUtilizado = isNaN(+txtUtilizadoComprobante.value) ? 0 : parseInt(txtUtilizadoComprobante.value);
        const limiteComprobantes = valorFin - (valorInicio - 1) - valorUtilizado;
        txtDisponibleComprobante.value = limiteComprobantes;
        if (valorUtilizado > (valorFin - (valorInicio - 1))){
            txtDisponibleComprobante.value = 0;
            txtUtilizadoComprobante.value = valorFin - (valorInicio - 1);
        }
        restaurarComprobantes();
    }
    for (const calculo of document.querySelectorAll(".calculo-disponible")) {
        calculo.addEventListener("blur", calcularComprobanetDisponible);
    }
    for (const btnCalculo of document.querySelectorAll(".btn-calculo-disponible")) {
        btnCalculo.addEventListener("click", calcularComprobanetDisponible);
    }
    for (const calculoDisponibilidad of document.querySelectorAll(".cambio-disponibilidad")) {
        calculoDisponibilidad.addEventListener("blur", cambioComprobanteDisponibilidad);
    }
    for (const btnCalculoDisponibilidad of document.querySelectorAll(".btn-cambio-disponibilidad")) {
        btnCalculoDisponibilidad.addEventListener("click", cambioComprobanteDisponibilidad);
    }
    for (const calculoDisponibilidad of document.querySelectorAll(".cambio-utilizados")) {
        calculoDisponibilidad.addEventListener("blur", cambioComprobanteUtilizado);
    }
    for (const btnCalculoDisponibilidad of document.querySelectorAll(".btn-cambio-utilizados")) {
        btnCalculoDisponibilidad.addEventListener("click", cambioComprobanteUtilizado);
    }
    let idComprobante = null;
    const btnModalSave = document.querySelector("#btnGuardarFrm");
    const formComprobante = document.querySelector("#formComprobante");
   
    formComprobante.addEventListener("submit", async function (e) {
        e.preventDefault();
        if (isNaN(+txtInicioComprobante.value)){
            txtInicioComprobante.value = 1;
        }
        if (isNaN(+txtFinComprobante.value)) {
            txtFinComprobante.value = 1000;
        }
        if (isNaN(+txtUtilizadoComprobante.value)) {
            txtUtilizadoComprobante.value = 0;
        }
        const limiteComprobantes = +txtFinComprobante.value - (+txtInicioComprobante.value - 1) - (+txtUtilizadoComprobante.value);
        if (isNaN(+txtDisponibleComprobante.value)) {
            txtDisponibleComprobante.value = limiteComprobantes;
        }
        if (txtInicioComprobante.value >= txtFinComprobante.value){
            return alertify.error("el inicio del comprobante debe ser menor que el fin del comprobante");
        }
        if (txtFinComprobante.value <= txtInicioComprobante.value) {
            return alertify.error("el fin del comprobante debe ser mayor que el inicio del comprobante");
        }
        if (+txtDisponibleComprobante.value > limiteComprobantes){
            return alertify.error("los comprobantes disponibles no debe ser mayor que " + limiteComprobantes);
        }
        const limite2 = +txtFinComprobante.value - (+txtInicioComprobante.value - 1);
        if (+txtUtilizadoComprobante.value > limite2) {
            return alertify.error("los comprobantes utilizados no debe superar a " + limite2);
        }
        let datos = new FormData(this);
        try {
            gen.cargandoPeticion(btnModalSave, gen.claseSpinner, true);
            const response = await gen.funcfetch(idComprobante ? "comprobantes/editar/" + idComprobante : "comprobantes/crear", datos);
            if (response.session) {
                return alertify.alert([...alertaSesion], () => { window.location.reload() });
            }
            if (response.error) {
                return alertify.alert("Error", response.error);
            }
            alertify.success(response.success);
            tablaComprobantesDatatable.draw();
            $('#agregarComprobante').modal("hide");
        } catch (error) {
            console.error(error);
            alertify.error("error al agregar un cliente");
        } finally {
            gen.cargandoPeticion(btnModalSave, 'fas fa-save', false);
        }
    });
    const modalTitulo = document.querySelector("#tituloComprobante");
    $('#agregarComprobante').on("hidden.bs.modal", function (e) {
        idComprobante = null;
        modalTitulo.textContent = "Crear comprobante";
        switchEstado.disabled = true;
        switchEstado.checked = true;
        switchEstado.parentElement.querySelector("label").textContent = "VIGENTE";
        formComprobante.reset();
        $('#agregarComprobante .select2-simple').trigger("change");
    });
    const switchEstado = document.querySelector("#idModalestado");
    btnModalSave.onclick = e => document.querySelector("#btnFrmEnviar").click();
    tablaComprobantes.addEventListener("click", async function (e) {
        if (e.target.classList.contains("btn-outline-info")) {
            btnModalSave.querySelector("span").textContent = "Editar";
            try {
                gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                const response = await gen.funcfetch("comprobantes/listar/" + e.target.dataset.comprobante, null, "GET");
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                if (response.session) {
                    return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                }
                modalTitulo.textContent = "Editar comprobante";
                idComprobante = e.target.dataset.comprobante;
                for (const key in response.comprobante) {
                    if (Object.hasOwnProperty.call(response.comprobante, key)) {
                        const valor = response.comprobante[key];
                        const dom = document.querySelector("#idModal" + key);
                        if (key == "estado") {
                            switchEstado.checked = valor === 1 ? true : false;
                            switchEstado.parentElement.querySelector("label").textContent = valor === 1 ? "VIGENTE" : "DESCONTINUADO";
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                switchEstado.disabled = false;
                $('#agregarComprobante').modal("show");
            } catch (error) {
                gen.cargandoPeticion(e.target, 'fas fa-pencil-alt', false);
                console.error(error);
                alertify.error("error al obtener el cliente");
            }
        }
        if (e.target.classList.contains("btn-outline-danger")) {
            alertify.confirm("Alerta", "¿Estás seguro de eliminar este cliente?", async () => {
                try {
                    gen.cargandoPeticion(e.target, gen.claseSpinner, true);
                    const response = await gen.funcfetch("comprobantes/eliminar/" + e.target.dataset.comprobante, null, "DELETE");
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    if (response.session) {
                        return alertify.alert([...gen.alertaSesion], () => { window.location.reload() });
                    }
                    tablaComprobantesDatatable.draw();
                    return alertify.success(response.success);
                } catch (error) {
                    gen.cargandoPeticion(e.target, 'fas fa-trash-alt', false);
                    console.error(error);
                    alertify.error("error al eliminar el comprobante");
                }
            }, () => { });

        }
    })
}
window.addEventListener("DOMContentLoaded", loadPage);

