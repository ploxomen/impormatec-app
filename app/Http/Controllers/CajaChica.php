<?php

namespace App\Http\Controllers;

use App\Models\CajaChica as ModelsCajaChica;
use App\Models\CajaChicaDetalle;
use App\Models\OrdenServicio;
use App\Models\User;
use Illuminate\Http\Request;
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
        $cajaChica = ModelsCajaChica::where('estado',1)->first();
        $monedaTipo = null;
        $ordenesServicios = [];
        $usuarios = [];
        if(!empty($cajaChica)){
            $monedaTipo = $cajaChica->tipo_moneda === 'PEN' ? 'S/' : '$';
            $ordenesServicios = OrdenServicio::select("id")->get();
            $usuarios = User::obtenerUsuariosNoSonClientes();
        }
        return view("almacen.cajaChicaGastos",compact("modulos","cajaChica","fechaLarga","monedaTipo","ordenesServicios","usuarios"));
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("almacen.cajaChica",compact("modulos"));
    }
    public function show(ModelsCajaChica $cajaChica){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $cajaChica->makeHidden(["fechaCreada","fechaActualizada",""]);
        return response()->json($cajaChica);
    }
    public function update(ModelsCajaChica $cajaChica,Request $request){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $datos = $request->all();
        $datos['estado'] = 0;
        if($request->has('estado')){
            $datos['estado'] = 1;
            ModelsCajaChica::where('estado','>=',0)->update(['estado' => 0]);
        }
        $cajaChica->update($datos);
        return response()->json(['success' => 'caja chica actualizada correctamente']);
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
        $datos = $request->all();
        $datos['estado'] = 0;
        if($request->has('estado')){
            $datos['estado'] = 1;
            ModelsCajaChica::where('estado','>=',0)->update(['estado' => 0]);
        }
        ModelsCajaChica::create($datos);
        return response()->json(['success' => 'caja chica creada correctamente']);
    }
    public function destroy(ModelsCajaChica $cajaChica){
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloCajaChica);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
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
