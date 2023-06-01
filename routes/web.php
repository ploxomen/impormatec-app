<?php

// use App\Http\Controllers\Caja;
// use App\Http\Controllers\Compras\Compras;
// use App\Http\Controllers\Compras\Proveedores;
// use App\Http\Controllers\Configuracion;
use App\Http\Controllers\Modulos;
use App\Http\Controllers\Categoria;
use App\Http\Controllers\Marca;
use App\Http\Controllers\MisProductos;
// use App\Http\Controllers\Producto\Perecedero;
use App\Http\Controllers\Presentacion;
use App\Http\Controllers\Usuario;
use App\Http\Controllers\Rol;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Clientes;
// use App\Http\Controllers\Ventas\Comprobantes;
// use App\Http\Controllers\Ventas\Cotizacion;
// use App\Http\Controllers\Ventas\Ventas;
// use Illuminate\Support\Facades\File;
// use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->prefix('intranet')->group(function(){
    Route::prefix('inicio')->group(function () {
        Route::get('/', [Usuario::class, 'index'])->name('home');
        Route::post('administrador', [Usuario::class, 'inicioAdministrador']);
    });
    Route::prefix('almacen')->group(function () {
        Route::prefix('marca')->group(function () {
            Route::get('/', [Marca::class, 'index'])->name('admin.marca.index');
            Route::post('listar', [Marca::class, 'listar']);
            Route::get('listar/{marca}', [Marca::class, 'show']);
            Route::post('crear', [Marca::class, 'store']);
            Route::post('editar/{marca}', [Marca::class, 'update']);
            Route::delete('eliminar/{marca}', [Marca::class, 'destroy']);
        });
//         Route::prefix('perecederos')->group(function () {
//             Route::get('/', [Perecedero::class, 'index'])->name('admin.perecedero.index');
//             Route::post('listar', [Perecedero::class, 'listar']);
//             Route::get('listar/{perecedero}', [Perecedero::class, 'show']);
//             Route::post('crear', [Perecedero::class, 'store']);
//             Route::post('editar/{perecedero}', [Perecedero::class, 'update']);
//             Route::delete('eliminar/{perecedero}', [Perecedero::class, 'destroy']);
//         });
        Route::prefix('categoria')->group(function () {
            Route::get('/', [Categoria::class, 'index'])->name('admin.categoria.index');
            Route::post('listar', [Categoria::class, 'listar']);
            Route::get('listar/{categoria}', [Categoria::class, 'show']);
            Route::post('crear', [Categoria::class, 'store']);
            Route::post('editar/{categoria}', [Categoria::class, 'update']);
            Route::delete('eliminar/{categoria}', [Categoria::class, 'destroy']);
        });
        Route::prefix('presentacion')->group(function () {
            Route::get('/', [Presentacion::class, 'index'])->name('admin.presentacion.index');
            Route::post('listar', [Presentacion::class, 'listar']);
            Route::get('listar/{presentacion}', [Presentacion::class, 'show']);
            Route::post('crear', [Presentacion::class, 'store']);
            Route::post('editar/{presentacion}', [Presentacion::class, 'update']);
            Route::delete('eliminar/{presentacion}', [Presentacion::class, 'destroy']);
        });
        Route::prefix('producto')->group(function () {
            Route::get('/', [MisProductos::class, 'index'])->name('admin.producto.index');
            Route::post('listar', [MisProductos::class, 'listar']);
            Route::get('listar/{producto}', [MisProductos::class, 'show']);
            Route::post('crear', [MisProductos::class, 'store']);
            Route::post('editar/{producto}', [MisProductos::class, 'update']);
            Route::delete('eliminar/{producto}', [MisProductos::class, 'destroy']);
        });
       
    });
//     Route::prefix('compras')->group(function () {
//         Route::prefix('proveedores')->group(function () {
//             Route::get('/', [Proveedores::class, 'index'])->name("admin.compras.proveedores");
//             Route::post('contacto/eliminar', [Proveedores::class, 'eliminarContacto']);
//             Route::post('listar', [Proveedores::class, 'listar']);
//             Route::get('listar/{proveedor}', [Proveedores::class, 'show']);
//             Route::post('crear', [Proveedores::class, 'store']);
//             Route::delete('eliminar/{proveedor}', [Proveedores::class, 'destroy']);
//         });
//         Route::prefix('registrar')->group(function () {
//             Route::get('/', [Compras::class, 'indexNuevaCompra'])->name("admin.compras.nueva.compra");
//             Route::get('consultar/{producto}', [Compras::class, 'consultarProductos']);
//             Route::post('crear', [Compras::class, 'storeCompra']);
//         });
//         Route::prefix('listar')->group(function () {
//             Route::get('/', [Compras::class, 'listaComprasIndex'])->name("admin.compras.mis.compras");
//             Route::post('mostrar', [Compras::class, 'listaComprasTotales']);
//             Route::get('mostrar/{compra}', [Compras::class, 'obtenerEditar']);
//             Route::post('agregar', [Compras::class, 'agregarModificarCompra']);
//             Route::post('eliminar', [Compras::class, 'eliminarProductoCompra']);
//             Route::get('eliminar/compra/{compra}', [Compras::class, 'eliminarCompraCompleta']);

//         });
//         Route::prefix('historial')->group(function () {
//             Route::get('/', [Compras::class, 'indexHistorialProducto'])->name("admin.compras.historial");
//             Route::post('listar', [Compras::class, 'obtenerHistorial']);
//         });

//     });
//     Route::prefix('configuracion')->group(function () {
//         Route::prefix('negocio')->group(function () {
//             Route::get('/', [Configuracion::class, 'indexConfiguracionNegocio'])->name("admin.configuracion.negocio");
//             Route::post('actualizar', [Configuracion::class, 'actualizarInformacionNegocio']);
//         });
//     });

    Route::prefix('ventas')->group(function () {
//         Route::prefix('administrador')->group(function () {
//             Route::get('listar/{producto}', [Ventas::class, 'verProductoAsignarVenta']);
//             Route::get('listar/comprobante/{comprobante}', [Ventas::class, 'verComprobante']);
//             Route::get('listar/cliente/{cliente}', [Ventas::class, 'verCliente']);
//         });
//         Route::prefix('general')->group(function () {
//             Route::get('/', [Ventas::class, 'verMisVentas'])->name("admin.ventas.index");
//             Route::post('listar', [Ventas::class, 'listaMisVentas']);
//             Route::get('listar/{venta}', [Ventas::class, 'verVentasParaEditar']);
//             Route::post('listar/producto/{producto}', [Ventas::class, 'verProductoMisVentas']);
//             Route::delete('eliminar/{venta}', [Ventas::class, 'eliminarVenta']);
//         });
//         Route::get('registrar', [Ventas::class, 'indexRegistroVentas'])->name('ventas.registrar.index');
//         Route::get('comprobante/{venta}', [Ventas::class, 'verComprobanteVenta']);
//         Route::post('registrar', [Ventas::class, 'registrarVenta']);
//         Route::prefix('comprobantes')->group(function () {
//             Route::get('/', [Comprobantes::class, 'index'])->name('admin.ventas.comprobantes.index');
//             Route::post('listar', [Comprobantes::class, 'listar']);
//             Route::get('listar/{comprobante}', [Comprobantes::class, 'show']);
//             Route::post('crear', [Comprobantes::class, 'store']);
//             Route::post('editar/{comprobante}', [Comprobantes::class, 'update']);
//             Route::delete('eliminar/{comprobante}', [Comprobantes::class, 'destroy']);
//         });
        Route::prefix('clientes')->group(function () {
            Route::get('/', [Clientes::class, 'index'])->name('admin.ventas.clientes.index');
            Route::post('listar', [Clientes::class, 'listar']);
            Route::get('listar/{cliente}', [Clientes::class, 'show']);
            Route::post('crear', [Clientes::class, 'store']);
            Route::post('editar/{cliente}', [Clientes::class, 'update']);
            Route::delete('eliminar/{cliente}', [Clientes::class, 'destroy']);
            Route::get('contacto/eliminar/{contacto}', [Clientes::class, 'eliminarContacto']);
        });
    });
//     Route::prefix('cotizaciones')->group(function () {
//         Route::get('nuevo', [Cotizacion::class, 'indexNuevaCotizacion'])->name('cotizacion.registrar.index');
//         Route::get('comprobante/{cotizacion}', [Cotizacion::class, 'comprobanteCotizacion']);
//         Route::post('registrar', [Cotizacion::class, 'registrarCotizacion']);
//         Route::get('listar/producto/{producto}', [Cotizacion::class, 'obtenerProducto']);
//         Route::get('mostrar', [Cotizacion::class, 'verCotizacionesAdminIndex'])->name('admin.cotizaciones.index');
//         Route::post('listar', [Cotizacion::class, 'verCotizacionesAdmin']);
//         Route::delete('eliminar/{cotizacion}', [Cotizacion::class, 'eliminarCotizacion']);
//     });
//     Route::prefix('caja')->group(function () {
//         Route::get('nueva', [Caja::class, 'indexAbrirCaja'])->name("admin.caja.abrir");
//         Route::post('abrir', [Caja::class, 'abrirCaja']);
//         Route::post('cerrar', [Caja::class, 'cerrarCaja']);
//     });
    Route::prefix('usuarios')->group(function(){
        Route::post('accion',[Usuario::class,'usuarioAccion']);
        Route::post('password',[Usuario::class,'cambioContrasena']);
        Route::get('cambio/rol/{rol}', [Usuario::class, 'cambioRol'])->name('cambiarRol');
        Route::get('miperfil', [Usuario::class, 'miPerfil'])->name('miPerfil');
        Route::post('miperfil/actualizar', [Usuario::class, 'actualizarPerfil']);
        Route::get('/',[Usuario::class,'listarUsuarios'])->name('admin.usuario.index');
        Route::get('cerrar/sesion', [Usuario::class, 'logoauth'])->name('cerrarSesion');
        Route::get('rol',[Rol::class,'viewRol'])->name('admin.rol.index');
        Route::get('modulo', [Modulos::class, 'index'])->name('admin.modulos.index');
        Route::post('modulo/accion', [Modulos::class, 'accionesModulos']);
        Route::post('rol/accion', [Rol::class, 'accionesRoles']);
    });
    Route::get('storage/{tipo}/{filename}', function ($tipo,$filename){
        $path = storage_path('app/'.$tipo . '/' . $filename);
        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    })->name("urlImagen"); 
});
Route::get("/",function(){
    return redirect()->route('login');
});
Route::middleware(['guest'])->prefix('intranet')->group(function () {
    Route::get('acceso', [Usuario::class, 'loginView'])->name('login');
    Route::get("restaurar", [Usuario::class, 'retaurarContra'])->name('restaurarContra');
    Route::get("restaurar/salir", [Usuario::class, 'salirLoginFirst'])->name('salirRestaurar');
    Route::post("autenticacion", [Usuario::class, 'autenticacion']);
    Route::post("restaurar", [Usuario::class, 'restaurarContrasena']);
});


