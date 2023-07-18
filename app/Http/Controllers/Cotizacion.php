<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\ClientesContactos;
use App\Models\Cotizacion as ModelsCotizacion;
use App\Models\CotizacionImagenes;
use App\Models\CotizacionPdf;
use App\Models\CotizacionServicio;
use App\Models\CotizacionServicioProducto;
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
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $resultado = ["cliente" => Clientes::obenerCliente($request->cliente)];
        return response()->json($resultado);
    }
    public function renderPdf($idCotizacion) {
        $cotizacion = ModelsCotizacion::find($idCotizacion);
        $cliente = Clientes::find($cotizacion->id_cliente);
        $representante = ClientesContactos::find($cotizacion->representanteCliente);
        $nombreDia = $this->obtenerFechaLarga(strtotime($cotizacion->fechaCotizacion));
        $nombreMes = $this->obtenerNombreMes(strtotime($cotizacion->fechaCotizacion));
        $servicios = CotizacionServicio::where('id_cotizacion',$cotizacion->id)->get();
        $reportePreCotizacion = [];
        if($cotizacion->reportePreCotizacion === 1){
            $preCotizacion = PreCotizaion::where('id',$cotizacion->id_pre_cotizacion)->first();
            $reportePreCotizacion['html'] = $preCotizacion->html_primera_visita;
            $reportePreCotizacion['imagenes'] = CotizacionImagenes::where('id_pre_cotizacion',$preCotizacion->id)->get();
        }
        $pdf = Pdf::loadView('cotizacion.reportes.cotizacion',compact("cotizacion","cliente","nombreDia","nombreMes","representante","servicios","reportePreCotizacion"));
        $nombreDocumento = "cotizacion_" . time() . "_" . $cotizacion->id . ".pdf";
        $pdf->save(storage_path("app/cotizacion/reportes/".$nombreDocumento));
        return $nombreDocumento;
    }
    public function verPdfCotizacion($cotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
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
            case 'consultar-aprobacion':
                $consulta = ModelsCotizacion::where(['estado' => 1,'id' => $request->idCotizacion])->first();
                if(empty($consulta)){
                    $resultado['alerta'] = "Esta cotización ya a sido aprobada";
                }else{
                    $resultado['servicios'] = ModelsCotizacion::obtenerServiciosProductos($request->idCotizacion);
                }
            break;
            case 'aprobar-cotizacion':
                DB::beginTransaction();
                try {
                    ModelsCotizacion::where(['estado' => 1,'id' => $request->idCotizacion])->update(['estado' => 2]);
                    foreach (json_decode($request->servicios) as $servicio) {
                        $cotizacionModel = CotizacionServicio::where(['id' => $servicio->idServicio, 'id_cotizacion' => $request->idCotizacion])->first();
                        foreach ($servicio->productos as $producto) {
                            $productoModel = $cotizacionModel->productos()->where('id_producto',$producto->idProducto)->first();
                            $productoModel->update(['id_almacen' => $producto->idAlmacen]);
                            $productoAlmacen = ProductoAlmacen::where(['id_almacen' => $producto->idAlmacen, 'id_producto' => $producto->idProducto,'estado' => 1])->first();
                            if(empty($productoAlmacen)){
                                DB::rollBack();
                                return response()->json(['alerta' => 'No se encontró el almacen para el producto']);
                            }
                            if($productoModel->cantidad > $productoAlmacen->stock){
                                DB::rollBack();
                                return response()->json(['alerta' => 'No hay suficiente stock en los almacenes, por favor aumente el stock o cambien de almacen']);
                            }
                            $productoAlmacen->update(['stock' => $productoAlmacen->stock - $productoModel->cantidad]);
                        }
                    }
                    DB::commit();
                    $resultado['success'] = "Se asignó correctamente los almacenes";
                } catch (\Throwable $th) {
                    DB::rollBack();
                    $resultado = ['error' => $th->getMessage() , 'linea' => $th->getLine() ];
                }
            break;
            case 'ver-productos':
            break;
        }
        return response()->json($resultado);
    }
    public function agregarCotizacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizacionAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $diasValidos = 15;
        $preCotizacion = $request->id_pre_cotizacion == "ninguno" ? null : $request->id_pre_cotizacion;
        $cotizacion = $request->only("fechaCotizacion","tipoMoneda","referencia","id_cliente","representanteCliente","cotizadorUsuario","direccionCliente");
        $cotizacion['reportePreCotizacion'] = $request->has("incluirPreCotizacion");
        $cotizacion['reporteDetallado'] = $request->has("reporteDetallado");
        $cotizacion['fechaFinCotizacion'] = date("Y-m-d",strtotime($request->fechaCotizacion."+ " . $diasValidos . " days"));
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
            foreach ($detalleCotizacion as $coti) {
                $mCotiServ = CotizacionServicio::create([
                    'id_cotizacion' => $mCotizacion->id,
                    'id_servicio' => $coti->idServicio,
                    'costo' => $coti->pUni,
                    'cantidad' => $coti->cantidad,
                    'importe' => $coti->pUni,
                    'descuento' => $coti->descuento,
                    'total' => $coti->pTotal,
                    'igv' => $coti->pTotal * 0.18
                ]);
                foreach ($coti->productosLista as $producto) {
                    CotizacionServicioProducto::create([
                        'id_cotizacion_servicio' => $mCotiServ->id,
                        'id_producto' => $producto->idProducto,
                        'costo' => $producto->pVenta,
                        'cantidad' => $producto->cantidad,
                        'importe' => $producto->importe,
                        'descuento' => $producto->descuento,
                        'total' => $producto->pTotal
                    ]);
                }
                $importes['importeTotal'] += $coti->pUni;
                $importes['descuentoTotal'] += $coti->descuento;
                $importes['igvTotal'] += $coti->pTotal * 0.18;
                $importes['total'] += $coti->pTotal;
            }
            $mCotizacion->update($importes);
            $documentoCotizacion = $this->renderPdf($mCotizacion->id);
            if($request->has("archivoPdf")){
                $urlDocumentoCotizacion = storage_path("app/cotizacion/reportes/".$documentoCotizacion);
                $oMerger = PDFMerger::init();
                $oMerger->addPDF($urlDocumentoCotizacion);
                unlink($urlDocumentoCotizacion);
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
                        'nombre_archivo' => $nombreArchivo,
                        'nombre_archivo_servidor' => $archivoNombreAlmacenamiento,
                        'estado' => 1
                    ];
                    $oMerger->addPDF($_FILES['archivoPdf']['tmp_name'][$i]);
                }
                $tiempo++;
                $oMerger->merge();
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
            return response()->json(['success' => 'Cotización agregada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function obtenerNombresMeses() {
        return ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","setiembre","octubre","noviembre","diciembre"];
    }
    public function obtenerFechaLarga($fechaTime) {
        $meses = $this->obtenerNombresMeses();
        $dias = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
        return $dias[date("w",$fechaTime)] . ', ' . date("d",$fechaTime) . ' de ' . $meses[date('n',$fechaTime) - 1] . ' del ' . date('Y',$fechaTime);
    }
    public function obtenerNombreMes($fechaTime) {
        $meses = $this->obtenerNombresMeses();
        return $meses[date('n',$fechaTime) - 1];
    }
    public function indexMisCotizaciones() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $preCotizaciones = PreCotizaion::where('estado',2)->get();
        $servicios = Servicio::where('estado',1)->get();
        return view("cotizacion.misCotizaciones",compact("modulos","clientes","tiposDocumentos","preCotizaciones","servicios"));
    }
    public function datatableCotizaciones(){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisCotizaciones);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $cotizaciones = ModelsCotizacion::obtenerCotizacion();
        return DataTables::of($cotizaciones)->toJson();
    }
}
