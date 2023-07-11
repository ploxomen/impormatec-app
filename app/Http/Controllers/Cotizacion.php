<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Cotizacion as ModelsCotizacion;
use App\Models\CotizacionServicio;
use App\Models\CotizacionServicioProducto;
use App\Models\PreCotizaion;
use App\Models\Productos;
use App\Models\Servicio;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

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
    public function agregarCotizacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizacionAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $diasValidos = 30;
        $preCotizacion = $request->id_pre_cotizacion == "ninguno" ? null : $request->id_pre_cotizacion;
        $cotizacion = $request->only("fechaCotizacion","tipoMoneda","referencia","id_cliente","representanteCliente","cotizadorUsuario","direccionCliente");
        $cotizacion['fechaFinCotizacion'] = date("Y-m-d",strtotime($request->fechaCotizacion."+ " . $diasValidos . " days"));
        $cotizacion['cotizadorUsuario'] = Auth::id();
        $cotizacion['id_pre_cotizacion'] = $preCotizacion;
        $detalleCotizacion = json_decode($request->servicios);
        $importes = [
            'importeTotal' => 0,
            'descuentoTotal' => 0,
            'igvTotal' => 0,
            'total' => 0
        ];
        $pImporte = 0;
        $pDescuento = 0;
        $pIgv = 0;
        $pTotal = 0;
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
                $mCotizacion->update($importes);
            }
            DB::commit();
            return response()->json(['success' => 'Cotización agregada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
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
