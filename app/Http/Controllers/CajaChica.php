<?php

namespace App\Http\Controllers;

use App\Models\CajaChica as ModelsCajaChica;
use App\Models\CajaChicaAumento;
use App\Models\CajaChicaDetalle;
use App\Models\Configuracion;
use App\Models\OrdenServicio;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CajaChica extends Controller
{
    private $moduloCajaChica = "admin.caja.chica.index";
    private $moduloCajaChicaGasto = "trabajador.caja.chica.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexGastos()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChicaGasto);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $fechaLarga = (new Utilitarios)->obtenerFechaLarga(strtotime(date('Y-m-d')));
        $cajaChica = ModelsCajaChica::where(['estado' => 1,'responsable_caja' => Auth::id()])->first();
        $monedaTipo = null;
        $ordenesServicios = [];
        if(!empty($cajaChica)){
            $monedaTipo = $cajaChica->tipo_moneda === 'PEN' ? 'S/' : '$';
            $ordenesServicios = OrdenServicio::select("id")->get();
        }
        return view("almacen.cajaChicaGastos",compact("modulos","cajaChica","fechaLarga","monedaTipo","ordenesServicios"));
    }
    public function indexGastosAdministrador(ModelsCajaChica $cajaChica)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $fechaLarga = (new Utilitarios)->obtenerFechaLarga(strtotime(date('Y-m-d')));
        $monedaTipo = $cajaChica->tipo_moneda === 'PEN' ? 'S/' : '$';
        $ordenesServicios = OrdenServicio::select("id")->get();
        return view("almacen.cajaChicaGastosAdmin",compact("modulos","cajaChica","fechaLarga","monedaTipo","ordenesServicios"));
    }
    public function indexGastosReporte(ModelsCajaChica $cajaChica)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','texto_datos_bancarios','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $detalleGastos = CajaChicaDetalle::where(['id_caja_chica' => $cajaChica->id])->get();
        $tituloPdf = 'REPORTE_GASTOS_'.str_pad($cajaChica->id,5,'0',STR_PAD_LEFT).'.pdf';
        $monedaTipo = $cajaChica->tipo_moneda === 'PEN' ? 'S/' : '$';
        return Pdf::loadView('almacen.reportes.cajaChicaGastos',compact("detalleGastos","cajaChica","configuracion","tituloPdf","monedaTipo"))->setPaper('a4', 'landscape')->stream($tituloPdf);
    }

    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $usuarios = User::obtenerUsuariosNoSonClientes();
        return view("almacen.cajaChica",compact("modulos","usuarios"));
    }
    public function listarGastosTrabajadores(){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChicaGasto);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $gastos = CajaChicaDetalle::select("caja_chica_detalles.id AS idDetalle","caja_chica.id AS idCaja","proveedor","area_costo","descripcion_producto","caja_chica_detalles.tipo_moneda","igv","monto_total_cambio")
        ->selectRaw("DATE_FORMAT(caja_chica_detalles.fecha_gasto,'%d/%m/%Y') AS fechaGasto,LPAD(caja_chica_detalles.id,5,'0') AS nroGastoDetalle,IF(caja_chica_detalles.id_os IS NULL,'No establecido',LPAD(caja_chica_detalles.id_os,5,'0'))AS nroOrdenServicio")
        ->join("caja_chica","caja_chica.id","=","caja_chica_detalles.id_caja_chica")->where(["caja_chica.responsable_caja" => Auth::id(),'estado' => 1]);
        return DataTables::of($gastos)->toJson();
    }
    public function listarGastosTrabajadoresAdmin($cajaChica){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $gastos = CajaChicaDetalle::select("caja_chica_detalles.id AS idDetalle","caja_chica.id AS idCaja","proveedor","area_costo","descripcion_producto","caja_chica_detalles.tipo_moneda","igv","monto_total_cambio")
        ->selectRaw("DATE_FORMAT(caja_chica_detalles.fecha_gasto,'%d/%m/%Y') AS fechaGasto,LPAD(caja_chica_detalles.id,5,'0') AS nroGastoDetalle,IF(caja_chica_detalles.id_os IS NULL,'No establecido',LPAD(caja_chica_detalles.id_os,5,'0'))AS nroOrdenServicio")
        ->join("caja_chica","caja_chica.id","=","caja_chica_detalles.id_caja_chica")->where(["caja_chica.id" => $cajaChica]);
        return DataTables::of($gastos)->toJson();
    }
    public function show(ModelsCajaChica $cajaChica){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $cajaChica->makeHidden(["fechaCreada","fechaActualizada"]);
        return response()->json($cajaChica);
    }
    public function aumentosCajaChica(ModelsCajaChica $cajaChica){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $aumentos = CajaChicaAumento::where('id_caja_chica',$cajaChica->id)->select('id','fecha_deposito','banco','nro_operacion','monto_abonado')->get();
        return response()->json(['aumentos' => $aumentos]);
    }
    public function reporteGastosUsuario(ModelsCajaChica $cajaChica) {
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChicaGasto);
        if(isset($accessModulo['session'])){
            return redirect()->route('home');
        }
        if($cajaChica->responsable_caja !== Auth::id()){
            return abort(404);
        }
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','texto_datos_bancarios','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $detalleGastos = CajaChicaDetalle::where(['id_caja_chica' => $cajaChica->id])->get();
        $tituloPdf = 'REPORTE_GASTOS_'.str_pad($cajaChica->id,5,'0',STR_PAD_LEFT).'.pdf';
        $monedaTipo = $cajaChica->tipo_moneda === 'PEN' ? 'S/' : '$';
        return Pdf::loadView('almacen.reportes.cajaChicaGastos',compact("detalleGastos","cajaChica","configuracion","tituloPdf","monedaTipo"))->setPaper('a4', 'landscape')->stream($tituloPdf);
    }
    public function update(ModelsCajaChica $cajaChica,Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $datos = $request->except("fecha_deposito","banco","nro_operacion");
        DB::beginTransaction();
        try {
            if($request->has('estado') === false){
                ModelsCajaChica::where(['responsable_caja' => $request->responsable_caja])->update(['estado' => 0]);
            }
            $datos['estado'] = $request->has('estado') ? 1 : 0;
            $cajaChica->update($datos);
            DB::commit();
            return response()->json(['success' => 'caja chica actualizada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function all(){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $cajaChica = ModelsCajaChica::select("caja_chica.id","monto_abonado","monto_gastado","caja_chica.estado","tipo_moneda")
        ->selectRaw("DATE_FORMAT(fecha_inicio,'%d/%m/%Y') AS fechaInicio,DATE_FORMAT(fecha_fin,'%d/%m/%Y') AS fechaFin,(monto_abonado - monto_gastado) AS montoRestante,LPAD(caja_chica.id,5,'0') AS nroCaja,CONCAT(usuarios.nombres,' ',usuarios.apellidos) AS nombreResponsable")
        ->join("usuarios","usuarios.id","=","caja_chica.responsable_caja")
        ->get();
        return DataTables::of($cajaChica)->toJson();
    }
    public function store(Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $datos = $request->except("fecha_deposito","banco","nro_operacion");
        $datos['estado'] = 0;
        DB::beginTransaction();
        try {
            if($request->has('estado')){
                $datos['estado'] = 1;
                ModelsCajaChica::where(['responsable_caja' => $request->responsable_caja])->update(['estado' => 0]);
            }
            $caja = ModelsCajaChica::create($datos);
            CajaChicaAumento::create(array_merge($request->only("fecha_deposito","banco","nro_operacion","monto_abonado"),['id_caja_chica' => $caja->id,'principal' => 1]));
            DB::commit();
            return response()->json(['success' => 'caja chica creada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function modificarAumento(ModelsCajaChica $cajaChica,Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        DB::beginTransaction();
        try {
            for ($i=0; $i < count($request->id_aumento); $i++) { 
                CajaChicaAumento::where(['id_caja_chica' => $cajaChica->id,'id' => $request->id_aumento[$i]])
                ->update(['fecha_deposito' => $request->fecha_deposito[$i],'banco' => $request->banco[$i],'nro_operacion' => $request->nro_operacion[$i],'monto_abonado' => $request->monto_abonado[$i]]);
            }
            $totalAumento = CajaChicaAumento::where('id_caja_chica',$cajaChica->id)->sum('monto_abonado');
            $cajaChica->update(['monto_abonado' => $totalAumento]);
            DB::commit();
            return response()->json(['success' => 'aumentos modificados correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function agregarAumento(Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        DB::beginTransaction();
        try {
            $cajaChica = ModelsCajaChica::find($request->id_caja_chica);
            $aumento = CajaChicaAumento::create(['id_caja_chica' => $cajaChica->id,'nro_operacion' => '']);
            DB::commit();
            return response()->json(['success' => 'aumento agregado correctamente','aumento' => $aumento]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function eliminarAumento($aumento,ModelsCajaChica $cajaChica){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        DB::beginTransaction();
        try {
            CajaChicaAumento::where(['id_caja_chica'=>$cajaChica->id,'id' => $aumento])->delete();
            $totalGastos = CajaChicaDetalle::where('id_caja_chica',$cajaChica->id)->sum('monto_total_cambio');
            $totalAumento = CajaChicaAumento::where('id_caja_chica',$cajaChica->id)->sum('monto_abonado');
            if($totalGastos > $totalAumento){
                DB::rollBack();
                return response()->json(['alerta' => 'El monto gastado no debe superar el monto abonado, por favor revice los montos']);
            }
            $cajaChica->update(['monto_abonado' => $totalAumento]);
            DB::commit();
            return response()->json(['success' => 'aumento eliminado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function destroy(ModelsCajaChica $cajaChica){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        CajaChicaAumento::where(['id_caja_chica'=>$cajaChica->id])->delete();
        CajaChicaDetalle::where(['id_caja_chica'=>$cajaChica->id])->delete();
        $cajaChica->delete();
        return response()->json(['success' => 'caja chica eliminada correctamente']);
    }
    public function listarGasto($gasto,$cajaChica){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChicaGasto);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $modeloCaja = ModelsCajaChica::where(['estado' => 1,'responsable_caja' => Auth::id(),'id'=>$cajaChica])->first();
        if(empty($modeloCaja)){
            return response()->json(['alerta' => 'No se encontró ninguna caja chica disponible para usted, por favor intentelo más tarde']);
        }
        $detalle = CajaChicaDetalle::select('id','id_os','fecha_gasto','tipo_comprobante','nro_comprobante','proveedor','url_imagen','proveedor_ruc','area_costo','descripcion_producto','tipo_moneda','tipo_cambio','monto_total','igv')->where(['id_caja_chica'=>$modeloCaja->id,'id' => $gasto])->first();
        if(empty($detalle)){
            return response()->json(['alerta' => 'No se encontró el gasto seleccionado, por favor intentelo nuevamente']);
        }
        return response()->json(['gasto' => $detalle]);
    }
    public function listarGastoAdmin($gasto,ModelsCajaChica $cajaChica){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $detalle = CajaChicaDetalle::select('id','id_os','fecha_gasto','tipo_comprobante','nro_comprobante','proveedor','url_imagen','proveedor_ruc','area_costo','descripcion_producto','tipo_moneda','tipo_cambio','monto_total','igv')->where(['id_caja_chica'=>$cajaChica->id,'id' => $gasto])->first();
        if(empty($detalle)){
            return response()->json(['alerta' => 'No se encontró el gasto seleccionado, por favor intentelo nuevamente']);
        }
        return response()->json(['gasto' => $detalle]);
    }
    public function eliminarGastoAdmin($gasto,ModelsCajaChica $cajaChica) {
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        CajaChicaDetalle::where(['id_caja_chica'=>$cajaChica->id,'id'=>$gasto])->delete();
        $totalDetalle = CajaChicaDetalle::where('id_caja_chica',$cajaChica->id)->sum('monto_total_cambio');
        $cajaChica->update(['monto_gastado' => $totalDetalle]);
        $cajaChica->refresh();
        return response()->json(['success' => 'Gasto eliminado correctamente','tipoMoneda' => $cajaChica->tipo_moneda, 'montoGastado' => $cajaChica->monto_gastado,'montoAbonado' => $cajaChica->monto_abonado,'montoRestante' => $cajaChica->monto_abonado - $cajaChica->monto_gastado]);
    }
    public function eliminarGasto($gasto,$cajaChica) {
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChicaGasto);
        $accessModulo2 = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session']) && isset($accessModulo2['session'])){
            return response()->json($accessModulo);
        }
        $modeloCaja = ModelsCajaChica::where(['id'=>$cajaChica,'estado' => 1,'responsable_caja' => Auth::id()])->first();
        if(empty($modeloCaja)){
            return response()->json(['alerta' => 'No se encontró ninguna caja chica disponible para usted, por favor intentelo más tarde']);
        }
        $detalleGasto = CajaChicaDetalle::where(['id_caja_chica'=>$modeloCaja->id,'id'=>$gasto])->first();
        if(!empty($detalleGasto->url_imagen) && Storage::disk('imgGastosCaja')->exists($detalleGasto->url_imagen)){
            Storage::disk('imgGastosCaja')->delete($detalleGasto->url_imagen);
        }
        $detalleGasto->delete();
        $totalDetalle = CajaChicaDetalle::where('id_caja_chica',$modeloCaja->id)->sum('monto_total_cambio');
        $modeloCaja->update(['monto_gastado' => $totalDetalle]);
        $modeloCaja->refresh();
        return response()->json(['success' => 'Gasto eliminado correctamente','tipoMoneda' => $modeloCaja->tipo_moneda, 'montoGastado' => $modeloCaja->monto_gastado,'montoAbonado' => $modeloCaja->monto_abonado,'montoRestante' => $modeloCaja->monto_abonado - $modeloCaja->monto_gastado]);
    }
    public function editarGasto(CajaChicaDetalle $gasto,Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChicaGasto);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $cajaChica = ModelsCajaChica::find($gasto->id_caja_chica);
        $datos = $request->all();
        if($request->id_os === 'NINGUNO'){
            $datos['id_os'] = null;
        }
        $montoConvertido = $request->monto_total;
        if($cajaChica->tipo_moneda !== $request->tipo_moneda){
            if(empty($request->tipo_cambio)){
                return response()->json(['alerta' => 'El tipo de cambio debe ser mayor a cero']);
            }
            $montoConvertido = $cajaChica->tipo_moneda === 'USD' ? $request->monto_total / $request->tipo_cambio : $request->monto_total * $request->tipo_cambio;
        }
        $datos['monto_total_cambio'] = $montoConvertido;
        DB::beginTransaction();
        try {
            $gasto->update($datos);
            $totalDetalle = CajaChicaDetalle::where('id_caja_chica',$cajaChica->id)->sum('monto_total_cambio');
            if($totalDetalle > $cajaChica->monto_abonado){
                DB::rollBack();
                return response()->json(['alerta' => 'El monto gastano supera al monto abonado, por favor verificar los montos']);
            }
            if(!empty($gasto->url_imagen) && Storage::disk('imgGastosCaja')->exists($gasto->url_imagen)){
                Storage::disk('imgGastosCaja')->delete($gasto->url_imagen);
            }
            if($request->has('urlImagen')){
                $nombreArchivo = $request->file('urlImagen')->getClientOriginalName();
                $datos['url_imagen'] = time().'_'.$nombreArchivo;
                $request->file('urlImagen')->storeAs('imgGastosCaja/', $datos['url_imagen']);
                $gasto->update(['url_imagen' => $datos['url_imagen']]);
            }
            $cajaChica->update(['monto_gastado' => $totalDetalle]);
            $cajaChica->refresh();
            DB::commit();
            return response()->json(['success' => 'Gasto actualizado correctamente','tipoMoneda' => $cajaChica->tipo_moneda, 'montoGastado' => $cajaChica->monto_gastado,'montoAbonado' => $cajaChica->monto_abonado,'montoRestante' => $cajaChica->monto_abonado - $cajaChica->monto_gastado]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function editarGastoAdmin(ModelsCajaChica $cajaChica,CajaChicaDetalle $gasto,Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $datos = $request->all();
        if($request->id_os === 'NINGUNO'){
            $datos['id_os'] = null;
        }
        $montoConvertido = $request->monto_total;
        if($cajaChica->tipo_moneda !== $request->tipo_moneda){
            if(empty($request->tipo_cambio)){
                return response()->json(['alerta' => 'El tipo de cambio debe ser mayor a cero']);
            }
            $montoConvertido = $cajaChica->tipo_moneda === 'USD' ? $request->monto_total / $request->tipo_cambio : $request->monto_total * $request->tipo_cambio;
        }
        $datos['monto_total_cambio'] = $montoConvertido;
        DB::beginTransaction();
        try {
            $gasto->update($datos);
            $totalDetalle = CajaChicaDetalle::where('id_caja_chica',$cajaChica->id)->sum('monto_total_cambio');
            if($totalDetalle > $cajaChica->monto_abonado){
                DB::rollBack();
                return response()->json(['alerta' => 'El monto gastano supera al monto abonado, por favor verificar los montos']);
            }
            if(!empty($gasto->url_imagen) && Storage::disk('imgGastosCaja')->exists($gasto->url_imagen)){
                Storage::disk('imgGastosCaja')->delete($gasto->url_imagen);
            }
            if($request->has('urlImagen')){
                $nombreArchivo = $request->file('urlImagen')->getClientOriginalName();
                $datos['url_imagen'] = time().'_'.$nombreArchivo;
                $request->file('urlImagen')->storeAs('imgGastosCaja/', $datos['url_imagen']);
                $gasto->update(['url_imagen' => $datos['url_imagen']]);
            }
            $cajaChica->update(['monto_gastado' => $totalDetalle]);
            $cajaChica->refresh();
            DB::commit();
            return response()->json(['success' => 'Gasto actualizado correctamente','tipoMoneda' => $cajaChica->tipo_moneda, 'montoGastado' => $cajaChica->monto_gastado,'montoAbonado' => $cajaChica->monto_abonado,'montoRestante' => $cajaChica->monto_abonado - $cajaChica->monto_gastado]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function agregarGastos(Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChicaGasto);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $cajaChica = ModelsCajaChica::where(['estado' => 1,'responsable_caja' => Auth::id()])->first();
        if(empty($cajaChica)){
            return response()->json(['alerta' => 'No se encontró ninguna caja chica activada que le corresponda, por favor actualize la página y vuelva a intentarlo']);
        }
        if(strtotime($cajaChica->fecha_inicio) > strtotime(now()) || strtotime($cajaChica->fecha_fin) < strtotime(now())){
            return response()->json(['alerta' => 'No se puede registrar los gastos de la caja debido a que no esta dentro de la fecha limite.<br>Fechas limite desde ' . date('d/m/Y',strtotime($cajaChica->fecha_inicio)) . ' hasta ' .date('d/m/Y',strtotime($cajaChica->fecha_fin))]);
        }
        $datos = $request->all();
        $datos['id_caja_chica'] = $cajaChica->id;
        if($request->id_os === 'NINGUNO'){
            $datos['id_os'] = null;
        }
        $montoConvertido = $request->monto_total;
        if($cajaChica->tipo_moneda !== $request->tipo_moneda){
            if(empty($request->tipo_cambio)){
                return response()->json(['alerta' => 'El tipo de cambio debe ser mayor a cero']);
            }
            $montoConvertido = $cajaChica->tipo_moneda === 'USD' ? $request->monto_total / $request->tipo_cambio : $request->monto_total * $request->tipo_cambio;
        }
        $datos['monto_total_cambio'] = $montoConvertido;
        DB::beginTransaction();
        try {
            $cajaChicaDetalle = CajaChicaDetalle::create($datos);
            $totalDetalle = CajaChicaDetalle::where('id_caja_chica',$cajaChica->id)->sum('monto_total_cambio');
            if($totalDetalle > $cajaChica->monto_abonado){
                DB::rollBack();
                return response()->json(['alerta' => 'El monto gastano supera al monto abonado, por favor verificar los montos']);
            }
            $comprobanteNombre = null;
            if($request->has('urlImagen')){
                $nombreArchivo = $request->file('urlImagen')->getClientOriginalName();
                $comprobanteNombre = time().'_'.$nombreArchivo;
                $request->file('urlImagen')->storeAs('imgGastosCaja/', $comprobanteNombre);
            }
            $cajaChicaDetalle->update(['url_imagen' => $comprobanteNombre]);
            $cajaChica->update(['monto_gastado' => $totalDetalle]);
            $cajaChica->refresh();
            DB::commit();
            return response()->json(['success' => 'Gasto registrado correctamente','tipoMoneda' => $cajaChica->tipo_moneda, 'montoGastado' => $cajaChica->monto_gastado,'montoAbonado' => $cajaChica->monto_abonado,'montoRestante' => $cajaChica->monto_abonado - $cajaChica->monto_gastado]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function eliminarImagenDetalleGastos(CajaChicaDetalle $gasto) {
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChicaGasto);
        $accessModulo2 = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session']) && isset($accessModulo2['session'])){
            return response()->json($accessModulo);
        }
        if(!empty($gasto->url_imagen) && Storage::disk('imgGastosCaja')->exists($gasto->url_imagen)){
            Storage::disk('imgGastosCaja')->delete($gasto->url_imagen);
            $gasto->update(['url_imagen' => null]);
        }
        return response()->json(['success' => 'imagen eliminada correctamente']);
    }
    public function agregarGastosAdmin(ModelsCajaChica $cajaChica,Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $datos = $request->all();
        $datos['id_caja_chica'] = $cajaChica->id;
        if($request->id_os === 'NINGUNO'){
            $datos['id_os'] = null;
        }
        $montoConvertido = $request->monto_total;
        if($cajaChica->tipo_moneda !== $request->tipo_moneda){
            if(empty($request->tipo_cambio)){
                return response()->json(['alerta' => 'El tipo de cambio debe ser mayor a cero']);
            }
            $montoConvertido = $cajaChica->tipo_moneda === 'USD' ? $request->monto_total / $request->tipo_cambio : $request->monto_total * $request->tipo_cambio;
        }
        $datos['monto_total_cambio'] = $montoConvertido;
        DB::beginTransaction();
        try {
            $cajaChicaDetalle = CajaChicaDetalle::create($datos);
            $totalDetalle = CajaChicaDetalle::where('id_caja_chica',$cajaChica->id)->sum('monto_total_cambio');
            if($totalDetalle > $cajaChica->monto_abonado){
                DB::rollBack();
                return response()->json(['alerta' => 'El monto gastano supera al monto abonado, por favor verificar los montos']);
            }
            $comprobanteNombre = null;
            if($request->has('urlImagen')){
                $nombreArchivo = $request->file('urlImagen')->getClientOriginalName();
                $comprobanteNombre = time().'_'.$nombreArchivo;
                $request->file('urlImagen')->storeAs('imgGastosCaja/', $comprobanteNombre);
            }
            $cajaChicaDetalle->update(['url_imagen' => $comprobanteNombre]);
            $cajaChica->update(['monto_gastado' => $totalDetalle]);
            $cajaChica->refresh();
            DB::commit();
            return response()->json(['success' => 'Gasto registrado correctamente','tipoMoneda' => $cajaChica->tipo_moneda, 'montoGastado' => $cajaChica->monto_gastado,'montoAbonado' => $cajaChica->monto_abonado,'montoRestante' => $cajaChica->monto_abonado - $cajaChica->monto_gastado]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
