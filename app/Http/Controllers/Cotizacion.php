<?php

namespace App\Http\Controllers;

use App\Exports\ExportCotizaciones;
use App\Models\Clientes;
use App\Models\ClientesContactos;
use App\Models\Configuracion;
use App\Models\Cotizacion as ModelsCotizacion;
use App\Models\CotizacionPreSecciones;
use App\Models\CotizacionPdf;
use App\Models\CotizacionProductos;
use App\Models\CotizacionServicio;
use App\Models\CotizacionServicioProducto;
use App\Models\OrdenServicioCotizacionProducto;
use App\Models\OrdenServicioCotizacionServicio;
use App\Models\PreCotizaion;
use App\Models\ProductoAlmacen;
use App\Models\Productos;
use App\Models\Servicio;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Cotizacion extends Controller
{
    private $usuarioController;
    private $moduloCotizacionAgregar = "admin.cotizacion.agregar.index";
    private $moduloMisCotizaciones = "admin.caotizacion.todos";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexNuevaCotización() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizacionAgregar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $preCotizaciones = PreCotizaion::where('estado',2)->get();
        $servicios = Servicio::where('estado',1)->get();
        $productos =  Productos::where('estado',1)->get();
        return view("cotizacion.nuevaCotizacion",compact("modulos","productos","clientes","tiposDocumentos","preCotizaciones","servicios"));
    }
    public function obtenerPreCotizacion(Request $request,$idprecotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizacionAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizacion = PreCotizaion::obtenerDatosPreCotizacion($idprecotizacion);
        return response()->json(['preCotizacion' => $preCotizacion]);
    }
    public function obtenerServicio($servicio) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizacionAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $servicio = Servicio::obtenerServicioProductos($servicio);
        return response()->json(['servicio' => $servicio]);
    }
    public function obtenerProducto(Productos $producto){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizacionAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(['producto' => $producto]);
    }
    public function obtenerCliente(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizacionAgregar);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $resultado = ["cliente" => Clientes::obenerCliente($request->cliente)];
        return response()->json($resultado);
    }
    public function renderPdf($idCotizacion) {
        $utilitarios = new Utilitarios();
        $cotizacion = ModelsCotizacion::find($idCotizacion);
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','texto_datos_bancarios','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $cliente = Clientes::find($cotizacion->id_cliente);
        $representante = ClientesContactos::find($cotizacion->representanteCliente);
        $nombreDia = $utilitarios->obtenerFechaLarga(strtotime($cotizacion->fechaCotizacion));
        $nombreMes = $utilitarios->obtenerNombreMes(strtotime($cotizacion->fechaCotizacion));
        $servicios = CotizacionServicio::mostrarServiciosConProductos($cotizacion->id);
        $productosServicios = CotizacionProductos::productosServicios($servicios,$cotizacion->id);
        $moneda = $cotizacion->tipoMoneda === "USD" ? '$' : 'S/';
        $reportePreCotizacion = [];
        $documentoVisitaUnicoPrecotizacion = "";
        $preCotizacion = [];
        if($cotizacion->reportePreCotizacion === 1){
            $preCotizacion = PreCotizaion::where('id',$cotizacion->id_pre_cotizacion)->first();
            $reportePreCotizacion['html'] = $preCotizacion->html_primera_visita;
            $reportePreCotizacion['imagenes'] = CotizacionPreSecciones::where('id_pre_cotizacion',$preCotizacion->id)->get();
            $documentoVisitaUnicoPrecotizacion = $preCotizacion->formato_visita_pdf;
        }
        $pdf = Pdf::loadView('cotizacion.reportes.cotizacion',compact("moneda","configuracion","cotizacion","cliente","nombreDia","nombreMes","representante","productosServicios","reportePreCotizacion","preCotizacion"));
        $nombreDocumento = "cotizacion_" . time() . "_" . $cotizacion->id . ".pdf";
        $pdf->save(storage_path("app/cotizacion/reportes/".$nombreDocumento));
        if(!empty($documentoVisitaUnicoPrecotizacion)){
            $rutaArchivo = "/cotizacion/reportes/" . $nombreDocumento;
            $oMerger = PDFMerger::init();
            $oMerger->addPDF(storage_path("app/cotizacion/reportes/".$nombreDocumento));
            $oMerger->addPDF(storage_path("app/formatoVisitas/".$documentoVisitaUnicoPrecotizacion));
            $oMerger->merge();
            $nombreDocumento = "cotizacion_" . time() . "_" . $cotizacion->id . ".pdf";
            if(Storage::exists($rutaArchivo)){
                Storage::delete($rutaArchivo);
            }
            $oMerger->save(storage_path() . '/app/cotizacion/reportes/'.$nombreDocumento);
        }
        return $nombreDocumento;
    }
    public function obtenerCotizacion(ModelsCotizacion $cotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $servicios = CotizacionServicio::mostrarServiciosConProductos($cotizacion->id);
        $detalleCotizacion = CotizacionProductos::productosServicios($servicios,$cotizacion->id);
        foreach ($detalleCotizacion->where('tipo','servicio') as $servicio) {
            $servicio->detalleProductos = CotizacionServicioProducto::select("productos.id AS idProducto","productos.urlImagen","productos.nombreProducto","cotizacion_servicio_productos.cantidad AS cantidadUsada","cotizacion_servicio_productos.costo AS pVentaConvertido","cotizacion_servicio_productos.total AS precioTotal","cotizacion_servicio_productos.importe", "cotizacion_servicio_productos.descuento","productos.precioVenta")->selectRaw("? AS tipoMoneda", [$cotizacion->tipoMoneda])
            ->join("productos","cotizacion_servicio_productos.id_producto","=","productos.id")
            ->where(['id_cotizacion_servicio' => $servicio->id])->get();
        }
        $cotizacion->cliente_pais = $cotizacion->cliente->id_pais;
        $cotizacion->serviciosProductos = $detalleCotizacion;
        $cotizacion->contactosClientes = ClientesContactos::where('idCliente',$cotizacion->id_cliente)->get();
        $cotizacion->documentosPdf = CotizacionPdf::select("id","nombre_archivo")->where('id_cotizacion',$cotizacion->id)->get();
        return response()->json(['cotizacion' => $cotizacion]);
    }
    public function actualizarCotizacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $cotizacionModel = ModelsCotizacion::find($request->idCotizacion);
        $preCotizacion = $request->id_pre_cotizacion == "ninguno" ? null : $request->id_pre_cotizacion;
        $cotizacion = $request->only("fechaCotizacion","tipoMoneda","conversionMoneda","textoNota","referencia","id_cliente","representanteCliente","cotizadorUsuario","direccionCliente","mesesGarantia");
        $incluirIgv = !$request->has('incluirIGV') ? false : ($request->incluirIGV === "1" ? true : false);
        $cotizacion['reportePreCotizacion'] = $request->has("reportePreCotizacion");
        $cotizacion['incluirIGV'] = $incluirIgv;
        $cotizacion['reporteDetallado'] = $request->has("reporteDetallado");
        $cotizacion['fechaFinCotizacion'] = $request->fechaVencimiento;
        $cotizacion['id_pre_cotizacion'] = $preCotizacion;
        $detalleCotizacion = json_decode($request->servicios);
        $importes = [
            'cantidad' => 1,
            'importeTotal' => 0,
            'descuentoTotal' => 0,
            'igvTotal' => 0,
            'total' => 0
        ];
        $documentosOtros = [];
        $documentoCotizacion = null; 
        DB::beginTransaction();
        try {
            $cotizacionModel->update($cotizacion);
            foreach ($detalleCotizacion as $key => $coti) {
                $importes['descuentoTotal'] += $coti->descuento;
                if($incluirIgv){
                    $importes['igvTotal'] += $coti->pTotal * 0.18;
                }
                $importes['importeTotal'] += $coti->pTotal;
                $coleccionDatos = [
                    'precio' => $coti->pUni,
                    'orden' => $key + 1,
                    'cantidad' => $coti->cantidad,
                    'importe' => $coti->pImporte,
                    'descuento' => $coti->descuento,
                    'total' => $coti->pTotal,
                    'igv' => $incluirIgv ? $coti->pTotal * 0.18 : 0,
                ];
                if(empty($coti->idServicio) && !empty($coti->idProducto)){
                    CotizacionProductos::updateOrCreate([
                        'id_cotizacion' => $cotizacionModel->id,
                        'id_producto' => $coti->idProducto
                    ],$coleccionDatos);
                    continue;
                }
                $mCotiServ = CotizacionServicio::updateOrCreate([
                    'id_cotizacion' => $cotizacionModel->id,
                    'id_servicio' => $coti->idServicio,
                    ],$coleccionDatos);
                foreach ($coti->productosLista as $producto) {
                    CotizacionServicioProducto::updateOrCreate([
                        'id_cotizacion_servicio' => $mCotiServ->id,
                        'id_producto' => $producto->idProducto,
                    ],[
                        'costo' => $producto->pVentaConvertido,
                        'cantidad' => $producto->cantidad,
                        'importe' => $producto->importe,
                        'descuento' => $producto->descuento,
                        'total' => $producto->pTotal
                    ]);
                }
            }
            $importes['total'] = $incluirIgv ? $importes['importeTotal'] + $importes['igvTotal'] : $importes['importeTotal'];
            $importes['importeTotal'] = $importes['importeTotal'] + $importes['descuentoTotal'];
            $cotizacionModel->update($importes);
            $cotizacionModel->fresh();
            $rutaArchivo = "/cotizacion/reportes/" . $cotizacionModel->documento;
            if(Storage::exists($rutaArchivo)){
                Storage::delete($rutaArchivo);
            }
            $documentoCotizacion = $this->renderPdf($cotizacionModel->id);
            $documentosDb = CotizacionPdf::where('id_cotizacion',$cotizacionModel->id);
            $oMerger = null;
            $urlDocumentoCotizacion = null;
            $tiempo = time();
            if($documentosDb->count() > 0){
                $urlDocumentoCotizacion = storage_path("app/cotizacion/reportes/".$documentoCotizacion);
                $oMerger = PDFMerger::init();
                $oMerger->addPDF($urlDocumentoCotizacion);
                foreach ($documentosDb->get() as $pdf) {
                    if(Storage::exists("/cotizacion/documentos/" . $pdf->nombre_archivo_servidor)){
                        $oMerger->addPDF(storage_path("app/cotizacion/documentos/" .
                         $pdf->nombre_archivo_servidor));
                    }
                }
            }
            if($request->has("archivoPdf")){
                if(is_null($oMerger)){
                    $urlDocumentoCotizacion = storage_path("app/cotizacion/reportes/".$documentoCotizacion);
                    $oMerger = PDFMerger::init();
                    $oMerger->addPDF($urlDocumentoCotizacion);
                }
                for ($i=0; $i < count($request->archivoPdf) ; $i++) {
                    $tiempo++;
                    $nombreOriginal = $request->file('archivoPdf')[$i]->getClientOriginalName();
                    $nombreArchivo = pathinfo($nombreOriginal,PATHINFO_FILENAME);
                    $extension = $request->file('archivoPdf')[$i]->getClientOriginalExtension();
                    $archivoNombreAlmacenamiento = $nombreArchivo.'_'.$tiempo.'.'.$extension;
                    $request->file('archivoPdf')[$i]->storeAs('/cotizacion/documentos/', $archivoNombreAlmacenamiento);
                    $documentosOtros[] = [
                        'id_cotizacion' => $cotizacionModel->id,
                        'nombre_archivo' => $nombreOriginal,
                        'nombre_archivo_servidor' => $archivoNombreAlmacenamiento,
                        'estado' => 1
                    ];
                    $oMerger->addPDF($_FILES['archivoPdf']['tmp_name'][$i]);
                }
            }
            if(!is_null($oMerger)){
                $tiempo++;
                $oMerger->merge();
                $documentoCotizacion = 'cotizacion_'.$tiempo.'_'.$cotizacionModel->id .".pdf";
                $oMerger->save(storage_path() . '/app/cotizacion/reportes/'.$documentoCotizacion);
            }
            if(!is_null($urlDocumentoCotizacion)){
                unlink($urlDocumentoCotizacion);
            }
            $cotizacionModel->update(['documento' => $documentoCotizacion]);
            foreach ($documentosOtros as $documento) {
                CotizacionPdf::create($documento);
            }
            DB::commit();
            return response()->json(['success' => 'Cotización actualizada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(),'linea' => $th->getLine()]);
        }
    }
    public function accionesCotizacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        switch ($request->acciones) {
            case 'eliminar-producto':
                $servicios = CotizacionServicio::where(['id_cotizacion' => $request->idCotizacion,'id_servicio' => $request->idServicio])->first();
                if(empty($servicios)){
                    return response()->json(['alerta' => 'No se encotro el servicio para el producto que se requiere eliminar']);
                }
                $productos = CotizacionServicioProducto::where('id_cotizacion_servicio',$servicios->id);
                $productos->where('id_producto',$request->idProducto)->delete();
                return response()->json(['success' => 'El prodcto se a eliminado de manera correcta']);
            break;
            case 'eliminar-producto-servicio':
                $cotizacion = CotizacionProductos::where(['id_cotizacion' => $request->idCotizacion, 'id_producto' => $request->idDetalle])->first();
                $ordenServicio = OrdenServicioCotizacionProducto::where(['id_cotizacion_producto' => $cotizacion->id]);
                if($ordenServicio->count() > 0){
                    return response()->json(['alerta' => 'Este producto no puede ser eliminado debido a que esta asociado con la orden de servicio N° ' . str_pad($ordenServicio->first()->id_orden_servicio,5,"0",STR_PAD_LEFT)]);
                }
                $cotizacion->delete();
                return response()->json(['success' => 'El producto se a eliminado de manera correcta']);
            break;
            case 'eliminar-servicio':
                $servicios = CotizacionServicio::where('id_cotizacion',$request->idCotizacion);
                $consultaServicio = $servicios->where('id_servicio',$request->idDetalle)->first();
                $ordenServicio = OrdenServicioCotizacionServicio::where(['id_cotizacion_servicio' => $consultaServicio->id]);
                if($ordenServicio->count() > 0){
                    return response()->json(['alerta' => 'Este servicio no puede ser eliminado debido a que esta asociado con la orden de servicio N° ' . str_pad($ordenServicio->first()->id_orden_servicio,5,"0",STR_PAD_LEFT)]);
                }
                CotizacionServicioProducto::where('id_cotizacion_servicio',$consultaServicio->id)->delete();
                $consultaServicio->delete();
                return response()->json(['success' => 'El servicio se a eliminado de manera correcta']);
            break;
            case 'eliminar-pdf':
                $documento = CotizacionPdf::where(['id_cotizacion' => $request->idCotizacion,'id' => $request->idPdf])->first();
                $rutaArchivo = "/cotizacion/documentos/".$documento->nombre_archivo_servidor;
                if(Storage::exists($rutaArchivo)){
                    Storage::delete($rutaArchivo);
                }
                $documento->delete();
                return response()->json(['success' => 'El documento se a eliminado de manera correcta']);
            break;
        }
    }
    public function verPdfCotizacion($cotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizacionAgregar);
        if(isset($verif['session']) && isset($verif2['session'])){
            return redirect()->route("home"); 
        }
        $cotizacion = ModelsCotizacion::findOrFail($cotizacion);
        $rutaPdf = "/cotizacion/reportes/" . $cotizacion->documento;
        if(!Storage::exists($rutaPdf)){
            abort(404,'No se encontró el pdf de la cotizacion');
        }
        $contenido = Storage::get($rutaPdf);
        return response($contenido, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="cotizacion.pdf"'
        ]);
    }
    public function aprobarCotizacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $resultado = [];
        switch ($request->acciones) {
            case 'consultar-almacenes':
                $resultado['productos'] = CotizacionProductos::productosCotizacionAprobar($request->idCotizacion,0);
                $resultado['servicios'] = ModelsCotizacion::obtenerServiciosProductos($request->idCotizacion);
                $resultado['aprobar'] = 0;
            break;
            case 'aprobar-solo':
                $cotizacion = ModelsCotizacion::where('id',$request->idCotizacion)->first();
                $cotizacion->update(['estado' => 2]);
                CotizacionProductos::where(['id_cotizacion' => $request->idCotizacion])->update(['fecha_fin_garantia' => date('Y-m-d',strtotime(date('Y-m-d') . ' + '. $cotizacion->mesesGarantia . ' month'))]);
                $resultado['success'] = "Cotizacion aprobada correctamente";
            break;
            case 'consultar-aprobacion':
                $consulta = ModelsCotizacion::where(['estado' => 1,'id' => $request->idCotizacion])->first();
                if(empty($consulta)){
                    $resultado['alerta'] = "Esta cotización ya a sido aprobada";
                }else{
                    $resultado['productos'] = CotizacionProductos::productosCotizacionAprobar($request->idCotizacion,0);
                    $resultado['servicios'] = ModelsCotizacion::obtenerServiciosProductos($request->idCotizacion);
                    $resultado['aprobar'] = 1;
                }
            break;
            case 'aprobar-cotizacion':
                DB::beginTransaction();
                try {
                    $cotizacion = ModelsCotizacion::where('id',$request->idCotizacion)->first();
                    $aprobacion = false;
                    if($cotizacion->estado === 1){
                        $cotizacion->update(['estado' => 2]);
                        $aprobacion = true;
                    }
                    foreach (json_decode($request->productos) as $productoAlmacen) {
                        $almacenProducto = ProductoAlmacen::where(['id_almacen' => $productoAlmacen->idAlmacen, 'id_producto' => $productoAlmacen->idProducto,'estado' => 1])->first();
                        if(empty($almacenProducto)){
                            DB::rollBack();
                            return response()->json(['alerta' => 'No se encontró el almacen para el producto ' . Productos::find($productoAlmacen->idProducto)->nombreProducto]);
                        }
                        $productoModel = CotizacionProductos::where(['id_producto' => $productoAlmacen->idProducto, 'id_cotizacion' => $request->idCotizacion])->first();
                        if($productoModel->cantidad > $almacenProducto->stock){
                            DB::rollBack();
                            return response()->json(['alerta' => 'No hay suficiente stock en el almacen para el producto '. Productos::find($productoAlmacen->idProducto)->nombreProducto .', por favor aumente el stock o cambie de almacen']);
                        }
                        $productoModel->update(['id_almacen' => $productoAlmacen->idAlmacen]);
                        if($aprobacion){
                            $almacenProducto->update(['stock' => $almacenProducto->stock - $productoModel->cantidad]);
                        }
                    }
                    if($request->calcularGarantia === 'true'){
                        CotizacionProductos::where(['id_cotizacion' => $request->idCotizacion])->update(['fecha_fin_garantia' => date('Y-m-d',strtotime(date('Y-m-d') . ' + '. $cotizacion->mesesGarantia . ' month'))]);
                    }
                    foreach (json_decode($request->servicios) as $servicioAlmacen) {
                        $cotizacionModel = CotizacionServicio::where(['id' => $servicioAlmacen->idServicio, 'id_cotizacion' => $request->idCotizacion])->first();
                        foreach ($servicioAlmacen->productos as $producto) {
                            $productoAlmacen = ProductoAlmacen::where(['id_almacen' => $producto->idAlmacen, 'id_producto' => $producto->idProducto,'estado' => 1])->first();
                            if(empty($productoAlmacen)){
                                DB::rollBack();
                                return response()->json(['alerta' => 'No se encontró el almacen para el producto ' . Productos::find($producto->idProducto)->nombreProducto . ' del servicio ' . $cotizacionModel->servicios->servicio]);
                            }
                            $productoModel = $cotizacionModel->productos()->where('id_producto',$producto->idProducto)->first();
                            if($productoModel->cantidad > $productoAlmacen->stock){
                                DB::rollBack();
                                return response()->json(['alerta' => 'No hay suficiente stock en el almacen para el producto ' .  Productos::find($producto->idProducto)->nombreProducto .' del servicio ' . $cotizacionModel->servicios->servicio . ', por favor aumente el stock o cambie de almacen']);
                            }
                            $productoModel->update(['id_almacen' => $producto->idAlmacen]);
                            if($aprobacion){
                                $productoAlmacen->update(['stock' => $productoAlmacen->stock - $productoModel->cantidad]);
                            }
                        }
                    }
                    DB::commit();
                    $resultado['success'] = "Se asignó correctamente los almacenes";
                } catch (\Throwable $th) {
                    DB::rollBack();
                    $resultado = ['error' => $th->getMessage() , 'linea' => $th->getLine() ];
                }
            break;
        }
        return response()->json($resultado);
    }
    public function agregarCotizacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizacionAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizacion = $request->id_pre_cotizacion == "ninguno" ? null : $request->id_pre_cotizacion;
        $cotizacion = $request->only("fechaCotizacion","tipoMoneda","conversionMoneda","textoNota","referencia","id_cliente","representanteCliente","cotizadorUsuario","direccionCliente","mesesGarantia");
        $incluirIgv = !$request->has('incluirIGV') ? false : ($request->incluirIGV === "1" ? true : false);
        $cotizacion['incluirIGV'] = $incluirIgv;
        $cotizacion['reportePreCotizacion'] = $request->has("reportePreCotizacion");
        $cotizacion['reporteDetallado'] = $request->has("reporteDetallado");
        $cotizacion['fechaFinCotizacion'] = $request->fechaVencimiento;
        $cotizacion['cotizadorUsuario'] = Auth::id();
        $cotizacion['id_pre_cotizacion'] = $preCotizacion;
        $detalleCotizacion = json_decode($request->servicios);
        $importes = [
            'cantidad' => 1,
            'importeTotal' => 0,
            'descuentoTotal' => 0,
            'igvTotal' => 0,
            'total' => 0
        ];
        $documentosOtros = [];
        $documentoCotizacion = null; 
        DB::beginTransaction();
        try {
            $mCotizacion = ModelsCotizacion::create($cotizacion);
            foreach ($detalleCotizacion as $key => $coti) {
                // dd($coti);
                $importes['descuentoTotal'] += $coti->descuento;
                if($incluirIgv){
                    $importes['igvTotal'] += $coti->total * 0.18;
                }
                $importes['importeTotal'] += $coti->importeTotal;
                $importes['total'] += $coti->total;
                $coleccionDatos = [
                    'id_cotizacion' => $mCotizacion->id,
                    'precio' => $coti->precioUnitarioNormal,
                    'orden' => $key + 1,
                    'cantidad' => $coti->cantidad,
                    'importe' => $coti->importeTotal,
                    'descuento' => $coti->descuento,
                    'total' => $coti->total,
                    'igv' => $incluirIgv ? $coti->total * 0.18 : 0,
                    'estado' => 1
                ];
                if(empty($coti->idServicio) && !empty($coti->idProducto)){
                    $coleccionDatos['id_producto'] = $coti->idProducto;
                    CotizacionProductos::create($coleccionDatos);
                    continue;
                }
                $coleccionDatos['id_servicio'] = $coti->idServicio;
                $mCotiServ = CotizacionServicio::create($coleccionDatos);
                foreach ($coti->productosLista as $producto) {
                    CotizacionServicioProducto::create([
                        'id_cotizacion_servicio' => $mCotiServ->id,
                        'id_producto' => $producto->idProducto,
                        'costo' => $producto->precioUnitarioNormal,
                        'cantidad' => $producto->cantidad,
                        'importe' => $producto->importeTotal,
                        'descuento' => $producto->descuento,
                        'total' => $producto->total
                    ]);
                }
            }
            $importes['total'] = $importes['total'] + round($importes['igvTotal'],2);
            $mCotizacion->update($importes);
            $documentoCotizacion = $this->renderPdf($mCotizacion->id);
            if($request->has("archivoPdf")){
                $urlDocumentoCotizacion = storage_path("app/cotizacion/reportes/".$documentoCotizacion);
                $oMerger = PDFMerger::init();
                $oMerger->addPDF($urlDocumentoCotizacion);
                $tiempo = time();
                for ($i=0; $i < count($request->archivoPdf) ; $i++) {
                    $tiempo++;
                    $nombreOriginal = $request->file('archivoPdf')[$i]->getClientOriginalName();
                    $nombreArchivo = pathinfo($nombreOriginal,PATHINFO_FILENAME);
                    $extension = $request->file('archivoPdf')[$i]->getClientOriginalExtension();
                    $archivoNombreAlmacenamiento = $nombreArchivo.'_'.$tiempo.'.'.$extension;
                    $request->file('archivoPdf')[$i]->storeAs('/cotizacion/documentos/', $archivoNombreAlmacenamiento);
                    $documentosOtros[] = [
                        'id_cotizacion' => $mCotizacion->id,
                        'nombre_archivo' => $nombreOriginal,
                        'nombre_archivo_servidor' => $archivoNombreAlmacenamiento,
                        'estado' => 1
                    ];
                    $oMerger->addPDF($_FILES['archivoPdf']['tmp_name'][$i]);
                }
                $tiempo++;
                $oMerger->merge();
                unlink($urlDocumentoCotizacion);
                $documentoCotizacion = 'cotizacion_'.$tiempo.'_'.$mCotizacion->id .".pdf";
                $oMerger->save(storage_path() . '/app/cotizacion/reportes/'.$documentoCotizacion);
            }
            $mCotizacion->update(['documento' => $documentoCotizacion]);
            foreach ($documentosOtros as $documento) {
                CotizacionPdf::create($documento);
            }
            if($request->id_pre_cotizacion != "ninguno"){
                PreCotizaion::find($request->id_pre_cotizacion)->update(['estado' => 3]);
            }
            DB::commit();
            return response()->json(['success' => 'Cotización agregada correctamente','urlPdf' => route('ver.cotizacion.pdf',[$mCotizacion->id])]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(), 'line' => $th->getLine()]);
        }
    }
    public function indexMisCotizaciones() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $preCotizaciones = PreCotizaion::where('estado','>=',1)->get();
        $servicios = Servicio::where('estado',1)->get();
        $productos =  Productos::where('estado',1)->get();
        $fechaFin = date('Y-m-d');
        $fechaInicio = date('Y-m-d',strtotime($fechaFin . ' - 90 days'));
        return view("cotizacion.misCotizaciones",compact("modulos","fechaFin","fechaInicio","clientes","tiposDocumentos","preCotizaciones","servicios","productos"));
    }
    public function reportesCotizaciones(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','texto_datos_bancarios','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $fechaInicioReporte = date('d/m/Y',strtotime($request->fecha_inicio));
        $fechaFinReporte = date('d/m/Y',strtotime($request->fecha_fin));
        $cotizaciones = $this->listaCotizaciones($request->fecha_inicio,$request->fecha_fin,$request->cliente,$request->estado);
        $titulo = 'cotizaciones';
        if($request->has('exportarPdf')){
            return Pdf::loadView('cotizacion.reportes.cotizacionPDF',compact("cotizaciones","configuracion","fechaInicioReporte","fechaFinReporte"))
            ->setPaper("A4","landscape")->stream($titulo.'.pdf');
        }else if($request->has('exportarExcel')){
            return Excel::download(new ExportCotizaciones($cotizaciones,$fechaInicioReporte,$fechaFinReporte,'cotizacion.reportes.cotizacionEXCEL'),$titulo.'.xlsx');
        }
    }
    public function datatableCotizaciones(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return DataTables::of($this->listaCotizaciones($request->fechaInicio,$request->fechaFin,$request->cliente,$request->estado))->toJson();
    }
    public function listaCotizaciones($fechaInicio,$fechaFin,$cliente,$estado) {
        $cotizaciones = ModelsCotizacion::obtenerCotizacion()->whereBetween('cotizacion.fechaCotizacion',[$fechaInicio,$fechaFin]);
        if($cliente !== 'TODOS'){
            $cotizaciones = $cotizaciones->where('cotizacion.id_cliente','=',$cliente);
        }
        if($estado !== 'TODOS'){
            $cotizaciones = $cotizaciones->where('cotizacion.estado','=',$estado);
        }
        return $cotizaciones->get();
    }
}
