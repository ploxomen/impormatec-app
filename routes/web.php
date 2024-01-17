<?php
use App\Http\Controllers\Configuracion;
use App\Http\Controllers\Modulos;
use App\Http\Controllers\Categoria;
use App\Http\Controllers\Marca;
use App\Http\Controllers\MisProductos;
use App\Http\Controllers\Almacenes;
use App\Http\Controllers\CajaChica;
use App\Http\Controllers\Usuario;
use App\Http\Controllers\Rol;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Clientes;
use App\Http\Controllers\Cotizacion;
use App\Http\Controllers\FacturacionElectronica;
use App\Http\Controllers\Informes;
use App\Http\Controllers\OrdenServicio;
use App\Http\Controllers\PreCotizacion;
use App\Http\Controllers\Programacion;
use App\Http\Controllers\Publicidades;
use App\Http\Controllers\RapiFac;
use App\Http\Controllers\Seguimiento;
use App\Http\Controllers\Servicio;
use App\Http\Controllers\Tecnico;
use App\Http\Controllers\Utilitarios;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
Route::middleware('auth')->prefix('intranet')->group(function(){
    Route::prefix('inicio')->group(function () {
        Route::get('/', [Usuario::class, 'index'])->name('home');
        Route::post('administrador', [Usuario::class, 'inicioAdministrador']);
    });
    Route::prefix('almacen')->group(function () {
        Route::prefix('caja-chica')->group(function () {
            Route::get('administrar', [CajaChica::class, 'index'])->name('admin.caja.chica.index');
            Route::post('editar/{cajaChica}', [CajaChica::class, 'update']);
            Route::post('crear', [CajaChica::class, 'store']);
            Route::get('listar', [CajaChica::class, 'all']);
            Route::get('listar-aumentos/{cajaChica}', [CajaChica::class, 'aumentosCajaChica']);
            Route::delete('eliminar/{cajaChica}', [CajaChica::class, 'destroy']);
            Route::post('agregar-aumento', [CajaChica::class, 'agregarAumento']);
            Route::put('modificar-aumentos/{cajaChica}', [CajaChica::class, 'modificarAumento']);
            Route::delete('eliminar-aumento/{aumento}/{cajaChica}', [CajaChica::class, 'eliminarAumento']);
            Route::get('listar/{cajaChica}', [CajaChica::class, 'show']);
            Route::get('listar-gastos', [CajaChica::class, 'listarGastosTrabajadores']);
            Route::get('listar-gasto/{gasto}/{cajaChica}', [CajaChica::class, 'listarGasto']);
            Route::prefix('gastos')->group(function () {
                Route::get('reporte/usuario/{cajaChica}', [CajaChica::class, 'reporteGastosUsuario'])->name('caja.chica.reporte.gastos.usuario');
                Route::get('/', [CajaChica::class, 'indexGastos'])->name('trabajador.caja.chica.index');
                Route::get('modificar/{cajaChica}', [CajaChica::class, 'indexGastosAdministrador']);
                Route::get('reporte/{cajaChica}', [CajaChica::class, 'indexGastosReporte'])->name('caja.chica.reporte.gastos');
                Route::get('modificar/listar-gastos/{cajaChica}', [CajaChica::class, 'listarGastosTrabajadoresAdmin']);
                Route::post('modificar/actualizar-gasto/{cajaChica}/{gasto}', [CajaChica::class, 'editarGastoAdmin']);
                Route::post('modificar/agregar-gasto/{cajaChica}', [CajaChica::class, 'agregarGastosAdmin']);
                Route::get('modificar/listar-gasto/{gasto}/{cajaChica}', [CajaChica::class, 'listarGastoAdmin']);
                Route::delete('modificar/eliminar/{gasto}/{cajaChica}', [CajaChica::class, 'eliminarGasto']);
                Route::post('editar/{gasto}', [CajaChica::class, 'editarGasto']);
                Route::post('agregar', [CajaChica::class, 'agregarGastos']);
                Route::delete('eliminar/imagen/{gasto}', [CajaChica::class, 'eliminarImagenDetalleGastos']);
                Route::delete('eliminar/{gasto}/{cajaChica}', [CajaChica::class, 'eliminarGasto']);
            });
        });
        Route::prefix('servicio')->group(function () {
            Route::get('/', [Servicio::class, 'index'])->name('admin.servicios');
            Route::post('listar', [Servicio::class, 'listar']);
            Route::get('listar/{servicio}', [Servicio::class, 'show']);
            Route::post('crear', [Servicio::class, 'store']);
            Route::post('producto/eliminar', [Servicio::class, 'eliminarProducto']);
            Route::post('editar/{servicio}', [Servicio::class, 'update']);
            Route::delete('eliminar/{servicio}', [Servicio::class, 'destroy']);
        });
        Route::prefix('marca')->group(function () {
            Route::get('/', [Marca::class, 'index'])->name('admin.marca.index');
            Route::post('listar', [Marca::class, 'listar']);
            Route::get('listar/{marca}', [Marca::class, 'show']);
            Route::post('crear', [Marca::class, 'store']);
            Route::post('editar/{marca}', [Marca::class, 'update']);
            Route::delete('eliminar/{marca}', [Marca::class, 'destroy']);
        });
        Route::prefix('categoria')->group(function () {
            Route::get('/', [Categoria::class, 'index'])->name('admin.categoria.index');
            Route::post('listar', [Categoria::class, 'listar']);
            Route::get('listar/{categoria}', [Categoria::class, 'show']);
            Route::post('crear', [Categoria::class, 'store']);
            Route::post('editar/{categoria}', [Categoria::class, 'update']);
            Route::delete('eliminar/{categoria}', [Categoria::class, 'destroy']);
        });
        Route::prefix('almacenes')->group(function () {
            Route::get('/', [Almacenes::class, 'index'])->name('admin.almacenes.index');
            Route::post('listar', [Almacenes::class, 'listar']);
            Route::get('listar/{almacen}', [Almacenes::class, 'show']);
            Route::post('crear', [Almacenes::class, 'store']);
            Route::post('editar/{almacen}', [Almacenes::class, 'update']);
            Route::delete('eliminar/{almacen}', [Almacenes::class, 'destroy']);
        });
        Route::prefix('producto')->group(function () {
            Route::get('/', [MisProductos::class, 'index'])->name('admin.producto.index');
            Route::post('listar', [MisProductos::class, 'listar']);
            Route::get('reportes/exportar', [MisProductos::class, 'exportarProductos'])->name('exportar.productos');
            Route::post('importar/utilidades',[MisProductos::class,'importarUtilidades']);
            Route::post('importar/utilidades/actualizar',[MisProductos::class,'actualizarUtilidades']);
            Route::get('listar/{producto}', [MisProductos::class, 'show']);
            Route::post('almacen/eliminar', [MisProductos::class, 'eliminarAlmacen']);
            Route::post('crear', [MisProductos::class, 'store']);
            Route::post('editar/{producto}', [MisProductos::class, 'update']);
            Route::delete('eliminar/{producto}', [MisProductos::class, 'destroy']);
            Route::post('eliminar/imagen/{producto}', [MisProductos::class, 'destroyImagen']);
        });
       
    });
    Route::prefix('configuracion')->group(function () {
        Route::prefix('mi-negocio')->group(function () {
            Route::get('/', [Configuracion::class, 'indexConfiguracionNegocio'])->name("admin.configuracion.negocio");
            Route::post('actualizar', [Configuracion::class, 'actualizarInformacionNegocio']);
        });
    });
    Route::prefix('ventas')->group(function () {
        Route::prefix('programacion')->group(function () {
            Route::get('/', [Programacion::class, 'index'])->name("admin.programacion.index");
            Route::get('listar', [Programacion::class, 'all']);
            Route::post('agregar', [Programacion::class, 'store']);
            Route::get('{programacion}', [Programacion::class, 'show']);
            Route::put('{programacion}', [Programacion::class, 'update']);
            Route::delete('{programacion}', [Programacion::class, 'destroy']);
            Route::get('reporte/pdf', [Programacion::class, 'reporteProgramacion'])->name('reporte.programacion');
        });
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
    Route::prefix('advertising')->group(function () {
        Route::post('guardar', [Publicidades::class, 'store']);
        Route::get('listar', [Publicidades::class, 'index'])->name("admin.publicidad.index");
        Route::get('obtener', [Publicidades::class, 'all']);
        Route::get('reenviar/{publicidad}', [Publicidades::class, 'reenviar']);
        Route::get('obtener/{publicidad}', [Publicidades::class, 'show']);
        Route::put('actualizar/{publicidad}', [Publicidades::class, 'update']);
        Route::delete('eliminar/{publicidad}', [Publicidades::class, 'destroy']);
        Route::delete('documento/{publicidad}/{documento}', [Publicidades::class, 'eliminarDocumentoPublicidad']);
    });
    Route::prefix('cotizaciones')->group(function () {
        Route::get('ver/pdf/{idCotizacion}', [Cotizacion::class, 'verPdfCotizacion'])->name("ver.cotizacion.pdf");
        Route::get('reportes', [Cotizacion::class, 'reportesCotizaciones'])->name("cotizacion.reportes");
        Route::get('obtener/{cotizacion}', [Cotizacion::class, 'obtenerCotizacion']);
        Route::post('aprobar', [Cotizacion::class, 'aprobarCotizacion']);
        Route::delete('eliminar/{cotizacion}', [Cotizacion::class, 'eliminarCotizacion']);
        Route::post('acciones', [Cotizacion::class, 'accionesCotizacion']);
        Route::post('modificar', [Cotizacion::class, 'actualizarCotizacion']);
        Route::get('agregar', [Cotizacion::class, 'indexNuevaCotización'])->name('admin.cotizacion.agregar.index');
        Route::get('obtener/precotizacion/{idprecotizacion}', [Cotizacion::class, 'obtenerPreCotizacion']);
        Route::get('obtener/servicio/{servicio}', [Cotizacion::class, 'obtenerServicio']);
        Route::get('obtener/producto/{producto}', [Cotizacion::class, 'obtenerProducto']);
        Route::post('obtener/cliente', [Cotizacion::class, 'obtenerCliente']);        
        Route::post('agregar', [Cotizacion::class, 'agregarCotizacion']);
        Route::get('todos',[Cotizacion::class,'indexMisCotizaciones'])->name("admin.caotizacion.todos");
        Route::post('lista-cotizacion', [Cotizacion::class, 'datatableCotizaciones']);
        Route::prefix('precotizacion')->group(function () {
            Route::get('nuevo', [PreCotizacion::class, 'indexNuevaPreCotizacion'])->name('cotizacion.precotizacion.nueva');
            Route::get('lista', [PreCotizacion::class, 'indexMisPreCotizaciones'])->name('cotizacion.precotizacion.lista');
            Route::post('lista-precotizacion', [PreCotizacion::class, 'obtenerPreCotizaciones']);
            Route::post('seccion/agregar', [PreCotizacion::class, 'agregarNuevaSeccion']);
            Route::post('seccion/imagen/agregar', [PreCotizacion::class, 'agregarImagenEnLaSeccion']);
            Route::post('actualizar/datos', [PreCotizacion::class, 'actualizarDatos']);
            Route::post('seccion/editar', [PreCotizacion::class, 'editarSeccion']);
            Route::post('seccion/obtener', [PreCotizacion::class, 'obtenerInformacionSeccion']);
            Route::post('seccion/eliminar', [PreCotizacion::class, 'eliminarSeccion']);
            Route::post('seccion/imagen/eliminar', [PreCotizacion::class, 'eliminarImagenEnLaSeccion']);
            Route::get('lista/{precotizacion}', [PreCotizacion::class, 'showPreCotizacion']);
            Route::get('reporte/{preCotizacion}', [PreCotizacion::class, 'visualizacionPdfReporte']);
            Route::get('informe/{idPreCotizacion}', [PreCotizacion::class, 'obtenerInformePreCotizacion']);
            Route::delete('eliminar/formato-visita/{preCotizacion}', [PreCotizacion::class, 'eliminarFormatoVisita']);
            Route::delete('eliminar/{preCotizacion}', [PreCotizacion::class, 'eliminarPreCotizacion']);
            Route::post('acciones', [PreCotizacion::class, 'accionesPreCotizacion']);
            Route::post('eliminar/imagen', [PreCotizacion::class, 'eliminarImagenPreCotizacion']);
            Route::post('actualizar', [PreCotizacion::class, 'actualizarPreCotizacion']);
            Route::post('agregar/imagen', [PreCotizacion::class, 'agregarImagenPreCotizacion']);
            Route::get('listar-pre/{precotizacion}', [PreCotizacion::class, 'obtenerPreCotizacionEditar']);
            Route::post('editar', [PreCotizacion::class, 'editarPreCotizacion']);
            Route::post('obtener/clientes', [PreCotizacion::class, 'obtenerClientesEditar']);
        });
        Route::prefix('tecnico')->group(function () {
            Route::get('pre-cotizacion', [Tecnico::class, 'indexPrimeraVisitaPreCotizacion'])->name('cotizacion.tecnico.visita.pre');
            Route::post('acciones', [Tecnico::class, 'accionesPreCotizacion']);
            Route::get('informe/{idPreCotizacion}', [Tecnico::class, 'obtenerInformePreCotizacion']);
        });
        Route::prefix('seguimiento')->group(function () {
            Route::get('/', [Seguimiento::class, 'index'])->name('admin.cotizacion.seguimiento');
            Route::get('listar', [Seguimiento::class, 'all']);
            Route::get('listar-garantia', [Seguimiento::class, 'allGarantia']);
            Route::get('historial/{cotizacion}', [Seguimiento::class, 'showHistorialSeguimiento']);
            Route::get('reporte/cotizacion/{tipo}', [Seguimiento::class, 'reporteCotizaciones']);
            Route::get('reporte/garantia/{tipo}', [Seguimiento::class, 'reporteGarantias']);
            Route::post('agregar', [Seguimiento::class, 'store']);
            Route::put('editar/{cotizacion}', [Seguimiento::class, 'update']);
            Route::get('notificar/{tipo}/{id}', [Seguimiento::class, 'notificacion']);
            Route::delete('eliminar/{seguimiento}/{cotizacion}', [Seguimiento::class, 'destroy']);
        });
    });
    Route::prefix('informe')->group(function () {
        Route::get('cliente/{idCliente}', [Informes::class, 'obtenerOrdenesServicioCliente']);
        Route::post('servicios/actualizar', [Informes::class, 'actualizarServiciosDescripciones']);
        Route::post('seccion/agregar', [Informes::class, 'agregarNuevaSeccion']);
        Route::post('seccion/editar', [Informes::class, 'editarSeccion']);
        Route::post('seccion/obtener', [Informes::class, 'obtenerInformacionSeccion']);
        Route::post('seccion/eliminar', [Informes::class, 'eliminarSeccion']);
        Route::get('certificado/{OsCotizacionServicio}', [Informes::class, 'certificadoInforme']);
        Route::get('certificado/visualizar/{cetificadoOperativo}', [Informes::class, 'visualizarCertificadoInforme'])->name("certificado.informe");
        Route::post('certificado/actualizar', [Informes::class, 'actualizarCertificado']);
        Route::get('certificado/reporte/{certificado}', [Informes::class, 'visualizarCertificado']);
        Route::get('reporte/previa/{idOrdenServicio}/{idServicio?}', [Informes::class, 'reportePrevioInforme'])->name("reporte.previo.informe");
        Route::post('seccion/imagen/agregar', [Informes::class, 'agregarImagenEnLaSeccion']);
        Route::post('actualizar/datos', [Informes::class, 'actualizarDatos']);
        Route::get('completado/{ordenServicio}', [Informes::class, 'editarInformeGenerado']);
        Route::post('seccion/imagen/eliminar', [Informes::class, 'eliminarImagenEnLaSeccion']);
        Route::post('generar', [Informes::class, 'generarInforme']);
        Route::get('generar/nuevo', [Informes::class, 'visualizarInforme'])->name("informe.generar");
        Route::get('lista', [Informes::class, 'indexGenerarInforme'])->name("admin.informe.index");
        Route::get('obtener', [Informes::class, 'listarInformes']);
    });
    Route::prefix('comprobantes')->group(function () {
        Route::get('facturar', [FacturacionElectronica::class, 'indexFactura'])->name('admin.comprobantes.sunat');
        Route::get('interno/{comprobante}', [OrdenServicio::class, 'comprobanteInterno'])->name('comprobante.interno');
        Route::post('facturar/eliminar', [FacturacionElectronica::class, 'eliminarFacturaElectronica']);
        Route::prefix('rapifac')->group(function () {
            Route::get('token', [RapiFac::class, 'obtenerToken']);
        });
    });
    Route::prefix('ordenes-servicio')->group(function () {
        Route::get('guiar-remision/consultar/{ordenServicio}', [OrdenServicio::class, 'consultarDatosGuiaRemision']);
        Route::get('mis-comprobantes/{ordenServicio}', [OrdenServicio::class, 'misComprobantes']);
        Route::post('mis-comprobantes/anular/{comprobante}', [OrdenServicio::class, 'anularComprobanteInterno']);
        Route::get('reportes', [OrdenServicio::class, 'reportesOrdenesServicios'])->name('ordenes.servicios.reportes');
        Route::delete('eliminar/{ordenServicio}', [OrdenServicio::class, 'eliminarOrdenServicio']);
        Route::get('probar/{ordenServicio}', [OrdenServicio::class, 'probarBoloeta']);
        Route::get('nueva', [OrdenServicio::class, 'indexNuevaOs'])->name("os.generar.index");
        Route::get('clientes/{cliente}', [OrdenServicio::class, 'obtenerCotizacionCliente']);
        Route::post('agregar', [OrdenServicio::class, 'agregarOs']);
        Route::post('generar/factura/guia-remision', [OrdenServicio::class, 'facturarGuiaRemision']);
        Route::post('generar/factura', [OrdenServicio::class, 'generarComprobante']);
        Route::get('todos', [OrdenServicio::class, 'indexMisOs'])->name("admin.ordenesServicios.index");
        Route::post('pago/cuota-agregar', [OrdenServicio::class, 'agregarCuota']);
        Route::get('pago/generar/{ordenServicio}', [OrdenServicio::class, 'previoPago']);
        Route::post('pago/cuota-modificar', [OrdenServicio::class, 'modificarCuota']);
        Route::post('pago/cuota/facturacion-externa', [OrdenServicio::class, 'modificarFacturacionExterna']);
        Route::delete('pago/cuota/{ordenServicio}/{cuota}', [OrdenServicio::class, 'eliminarCuota']);
        Route::delete('pago/cuota-prueba/imagen/{cuota}/{imagen}', [OrdenServicio::class, 'eliminarImagenCuola']);
        Route::delete('pago/cuota-comprobante/{ordenServicio}/{cuota}', [OrdenServicio::class, 'eliminarComprobanteSunat']);
        Route::post('pago/cuota-prueba/imagen', [OrdenServicio::class, 'guardarImagenCuota']);
        Route::get('pago/cuota/comprobante-sunat/{ordenServicio}/{idCuota}', [OrdenServicio::class, 'verComprobanteSunat']);
        Route::get('pago/cuotas/{ordenServicio}', [OrdenServicio::class, 'obtenerCuotas']);
        Route::get('pago/comprobante-cuota/{cuota}', [OrdenServicio::class, 'verComprobanteCuota']);
        Route::get('pago/cuota/{ordenServicio}/{cuota}', [OrdenServicio::class, 'obtenerCuota']);
        Route::post('obtener', [OrdenServicio::class, 'obtenerOrdenServicio']);
        Route::post('acciones', [OrdenServicio::class, 'accionesOrdenServicio']);
        Route::get('mostrar/{ordenServicio}', [OrdenServicio::class, 'obtenerDatosOrdenServicio']);
        Route::get('reporte/{ordenServicio}', [OrdenServicio::class, 'reporteOrdenServicio']);
        Route::get('acta-entrega/{ordenServicio}', [OrdenServicio::class, 'obtenerDatosActa']);
        Route::get('acta-entrega/reporte/{entregaActa}', [OrdenServicio::class, 'reporteEntregaActa']);
        Route::post('acta-entrega/guardar', [OrdenServicio::class, 'guardarDatosActa']);
    });
    Route::prefix('general')->group(function(){
        Route::get('cotizaciones', [Clientes::class, 'cotizacionesIndex'])->name('cliente.cotizaciones.index');
        Route::post('cotizaciones/listar', [Clientes::class, 'obtenerCotizaciones']);
        Route::get('cotizacion/ver/pdf/{idCotizacion}', [Clientes::class, 'verPdfCotizacion']);
        Route::get('visitas', [Clientes::class, 'visitasIndex'])->name('cliente.precotizaciones.index');
        Route::post('visitas/listar', [Clientes::class, 'obtenerVisitas']);
        Route::get('visita/ver/pdf/{preCotizacion}', [Clientes::class, 'visualizacionPdfReporte']);
        Route::get('comprobantes', [Clientes::class, 'misComprobantes'])->name('cliente.comprobantes.index');
        Route::post('comprobantes/listar', [Clientes::class, 'obtenerComprobantes']);
        Route::get('comprobante/ver/pdf/cuota/{cuota}', [Clientes::class, 'verComprobanteCuota']);
        Route::get('comprobante/ver/pdf/facturacion/{comprobante}', [Clientes::class, 'verComprobanteFacturacion']);
        Route::get('informes', [Clientes::class, 'misInformesIndex'])->name('cliente.informes.index');
        Route::post('informes/listar', [Clientes::class, 'obtenerInformes']);
        Route::get('informe/ver/pdf/{idOrdenServicio}/{idServicio}', [Clientes::class, 'reportePrevioInforme']);
        Route::get('certificados', [Clientes::class, 'misCertificados'])->name('cliente.certificados.index');
        Route::post('certificados/listar', [Clientes::class, 'obtenerCertificados']);
        Route::get('certificado/ver/pdf/{certificado}', [Clientes::class, 'visualizarCertificado']);
        Route::get('actas', [Clientes::class, 'misActas'])->name('cliente.actas.index');
        Route::post('actas/listar', [Clientes::class, 'obtenerActas']);
        Route::get('acta/ver/pdf/{entregaActa}', [Clientes::class, 'verActa']);
    });
    Route::prefix('usuarios')->group(function(){
        Route::post('accion',[Usuario::class,'usuarioAccion']);
        Route::post('password',[Usuario::class,'cambioContrasena']);
        Route::get('cambio/rol/{rol}', [Usuario::class, 'cambioRol'])->name('cambiarRol');
        Route::get('miperfil', [Usuario::class, 'miPerfil'])->name('miPerfil');
        Route::post('miperfil/actualizar', [Usuario::class, 'actualizarPerfil']);
        Route::post('miperfil/eliminar-firma', [Usuario::class, 'eliminarFirmaUsuarios']);
        Route::get('/',[Usuario::class,'listarUsuarios'])->name('admin.usuario.index');
        Route::get('cerrar/sesion', [Usuario::class, 'logoauth'])->name('cerrarSesion');
        Route::get('rol',[Rol::class,'viewRol'])->name('admin.rol.index');
        Route::get('modulo', [Modulos::class, 'index'])->name('admin.modulos.index');
        Route::post('modulo/accion', [Modulos::class, 'accionesModulos']);
        Route::post('rol/accion', [Rol::class, 'accionesRoles']);
    });
    Route::post('storage/editor/img-certificado/save',[Utilitarios::class,'guardarImagenesEditorTextoCertificado']);
    Route::post('storage/editor/img/save',[Utilitarios::class,'guardarImagenesEditorTexto']);
    Route::post('storage/editor/img-cotizacion/save',[Utilitarios::class,'guardarImagenesEditorTextoCotizacion']);
    Route::post('storage/editor/img-os/save',[Utilitarios::class,'guardarImagenesEditorTextoOs']);
    Route::post('storage/editor/img-configuracion/save',[Utilitarios::class,'guardarImagenesEditorTextoConfiguracion']);

    Route::get('storage/{tipo}/{filename}', function ($tipo,$filename){
        $path = storage_path('app/'.$tipo . '/' . $filename);
        if (!File::exists($path)) {
            $path = storage_path('app/productos/sin-imagen.png');
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    })->name("urlImagen");
    Route::get("descargar/{archivo}",function ($archivo){
        $rutaArchivo = storage_path('app/public/' . $archivo);
        if (Storage::disk('local')->exists('public/' . $archivo)) {
            return response()->file($rutaArchivo);
        } else {
            abort(404);
        }
    })->name("descargarArchivo");
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


