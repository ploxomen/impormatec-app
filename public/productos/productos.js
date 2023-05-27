let general = new General();
class Productos{
    async obtenerProductos(producto){
        let datos = new FormData(producto);
        let listas = await general.funcfetch('listar',datos);
        console.log(listas);
    }
}