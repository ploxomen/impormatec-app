function loadPage(){
    $(".select2-simple").select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: !$(this).data("placeholder") ? "Seleccione una opción" : $(this).data("placeholder"),
        tags: !$(this).data("tags") ? false : true
    });
    $(".select2-new").select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: $(this).data("placeholder"),
        tags : true   
    });
    const liActive = document.querySelector(".activeli");
    if(liActive && !liActive.parentElement.classList.contains("show")){
        liActive.parentElement.classList.add("show");
        document.querySelector(`[data-target="#${liActive.parentElement.id}"]`).classList.toggle("activesub");
        liActive.parentElement.setAttribute("arial-expanded","true");
    }
    for (const display of document.querySelectorAll('.display-submenu')) {
        display.onclick = function(e){
            for (const display2 of document.querySelectorAll('.display-submenu')) {
                if(display != display2){
                    display2.classList.remove("activesub");
                }
            }
            display.classList.toggle("activesub");
        }
    }
    const menuNavegacion = document.querySelector("#menuNavegacion");
    const contenedor = document.querySelector("#salirMenuNavegacion");
    document.querySelector("#menuDesplegable").addEventListener("click",function(e){
        e.stopPropagation();
        menuNavegacion.parentElement.classList.toggle("active-menu");
        menuNavegacion.classList.toggle("nav-active");
        contenedor.style.display = "block";
    });
    contenedor.addEventListener("click",function(e){
        menuNavegacion.parentElement.classList.toggle("active-menu");
        menuNavegacion.classList.toggle("nav-active");
        contenedor.style.display = "none";
    });
}
window.addEventListener("DOMContentLoaded",loadPage);