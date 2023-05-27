function loadPage(){
    let producto = new Productos();
    let filProducto = document.getElementById('filtros');
    producto.obtenerProductos(filProducto);
}
window.onload = loadPage;