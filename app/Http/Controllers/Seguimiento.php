<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Cotizacion;
use App\Models\CotizacionProductos;
use App\Models\CotizacionSeguimiento;
use App\Models\CotizacionServicio;
use App\Models\OrdenServicioCotizacionServicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class Seguimiento extends Controller
{
    private $moduloSeguimiento = "admin.cotizacion.seguimiento";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $fechaFin = date('Y-m-d');
        $clientes = Clientes::obenerClientesActivos();
        $fechaInicio = date('Y-m-d',strtotime($fechaFin . ' - 90 days'));
        $meses = (new Utilitarios)->obtenerNombresMeses();
        $modulos = $this->usuarioController->obtenerModulos();
        $usuarios = User::obtenerUsuariosNoSonClientes();
        return view("cotizacion.seguimiento",compact("usuarios","modulos","fechaFin","fechaInicio","meses","clientes"));
    }
    public function all(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $seguimientos = Cotizacion::obtenerCotizacion()->where('fechaCotizacion','>=',$request->fechaInicio)->where('fechaCotizacion','<=',$request->fechaFin)->where('cotizacion.estado',1);
        if($request->porcentaje !== 'todos'){
            $seguimientos = $seguimientos->where('cotizacion.porcentaje_actual',$request->porcentaje);
        }
        if($request->responsable !== 'todos'){
            $seguimientos = $seguimientos->where('cotizacion.cotizadorUsuario',$request->responsable);
        }
        return DataTables::of($seguimientos->get())->toJson();
    }
    public function allGarantia(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $seguimientosGarantia = Cotizacion::obtenerGarantiasFechas($request->mes,$request->year,$request->cliente,$request->estado);
        return DataTables::of($seguimientosGarantia)->toJson();
    }
    public function showHistorialSeguimiento(Cotizacion $cotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        return response()->json(['historialSeguimientos' => $cotizacion->historialSeguimientos()->select("id","id_usuario","porcentaje","id_cotizacion","descripcion","fechaCreada")->selectRaw("DATE_FORMAT(fechaCreada,'%d/%m/%Y %h:%i %p') AS fechaCreadaFormato")->with('usuario:id,nombres,apellidos')->orderBy('porcentaje','desc')->get()]);
    }
    public function store(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        DB::beginTransaction();
        try {
            $datos = $request->all();
            $datos['id_usuario'] = Auth::id();
            Cotizacion::find($request->id_cotizacion)->update(['porcentaje_actual' => $request->porcentaje]);
            CotizacionSeguimiento::create($datos);
            DB::commit();
            return response()->json(['success' => 'seguimiento agregado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function update($cotizacion,Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        DB::beginTransaction();
        try {
            if($request->has("seguimiento") && isset($request->porcentaje[0])){
                Cotizacion::find($cotizacion)->update(['porcentaje_actual' => $request->porcentaje[0]]);
            }
            for ($i=0; $i < count($request->seguimiento); $i++) {
                CotizacionSeguimiento::where(['id_cotizacion' => $cotizacion,'id' => $request->seguimiento[$i]])->update([
                    'descripcion' => $request->descripcion[$i],
                    'porcentaje' => $request->porcentaje[$i],
                ]);
            }
            DB::commit();
            return response()->json(['success' => 'seguimientos actualizados correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function destroy($seguimiento,$cotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        CotizacionSeguimiento::where(['id_cotizacion' => $cotizacion,'id' => $seguimiento])->delete();
        $cotizacionSeguimientoPrimero = CotizacionSeguimiento::where(['id_cotizacion' => $cotizacion])->first();
        Cotizacion::find($cotizacion)->update(['porcentaje_actual' => empty($cotizacionSeguimientoPrimero) ? 0 : $cotizacionSeguimientoPrimero->porcentaje]);
        return response()->json(['success' => 'seguimiento eliminado correctamente']);
    }
    public function notificacion($tipo,$id) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        if($tipo === 'Servicio'){
            $servicio = OrdenServicioCotizacionServicio::find($id);
            $response = [
                'nroCotizacion' => str_pad($servicio->cotizacionServicio->id_cotizacion,5,'0',STR_PAD_LEFT),
                'fechaFinGarantia' => date('d/m/Y',strtotime($servicio->fecha_fin_garantia)),
                'cliente' => $servicio->cotizacionServicio->cotizacion->cliente->nombreCliente,
                'celular' => $servicio->cotizacionServicio->cotizacion->cliente->usuario->celular,
                'servicios' => $servicio->cotizacionServicio->servicios->servicio,
                'usuario' => Auth::user()->nombres
            ];
        }else{
            $productos = CotizacionProductos::find($id);
            $response = [
                'nroCotizacion' => str_pad($productos->id_cotizacion,5,'0',STR_PAD_LEFT),
                'fechaFinGarantia' => date('d/m/Y',strtotime($productos->fecha_fin_garantia)),
                'cliente' => $productos->cotizacion->cliente->nombreCliente,
                'celular' => $productos->cotizacion->cliente->usuario->celular,
                'servicios' => $productos->productos->nombreProducto,
                'usuario' => Auth::user()->nombres
            ];
        }
        return response()->json($response);
    }
}
