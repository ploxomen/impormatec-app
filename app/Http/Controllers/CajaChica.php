<?php

namespace App\Http\Controllers;

use App\Models\CajaChica as ModelsCajaChica;
use App\Models\CajaChicaAumento;
use App\Models\CajaChicaDetalle;
use App\Models\OrdenServicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $cajaChica = ModelsCajaChica::select("id","monto_abonado","monto_gastado","estado","tipo_moneda")->selectRaw("DATE_FORMAT(fecha_inicio,'%d/%m/%Y') AS fechaInicio,DATE_FORMAT(fecha_fin,'%d/%m/%Y') AS fechaFin,(monto_abonado - monto_gastado) AS montoRestante,LPAD(id,5,'0') AS nroCaja")->get();
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
    public function agregarGastos(Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChicaGasto);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $cajaChica = ModelsCajaChica::where('estado',1)->first();
        if(empty($cajaChica)){
            return response()->json(['alerta' => 'No se encontró ninguna caja chica activada, por favor actualize la página y vuelva a intentarlo']);
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
            CajaChicaDetalle::create($datos);
            $totalDetalle = CajaChicaDetalle::where('id_caja_chica',$cajaChica->id)->sum('monto_total_cambio');
            $cajaChica->update(['monto_gastado' => $totalDetalle]);
            DB::commit();
            return response()->json(['success' => 'Gasto registrado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
