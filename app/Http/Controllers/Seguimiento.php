<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\CotizacionSeguimiento;
use App\Models\CotizacionServicio;
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
        $fechaInicio = date('Y-m-d',strtotime($fechaFin . ' - 90 days'));
        $meses = (new Utilitarios)->obtenerNombresMeses();
        $modulos = $this->usuarioController->obtenerModulos();
        return view("cotizacion.seguimiento",compact("modulos","fechaFin","fechaInicio","meses"));
    }
    public function all(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        if($request->tipo === 'pendientes'){
            $seguimientos = Cotizacion::obtenerCotizacion()->where('fechaCotizacion','>=',$request->fechaInicio)->where('fechaCotizacion','<=',$request->fechaFin)->where('cotizacion.estado',1)->get();
        }else{
            $seguimientos = Cotizacion::obtenerCotizacion()->whereRaw('MONTH(fecha_fin_garantia) = ? AND YEAR(fecha_fin_garantia) = ?',[$request->mes,$request->year])->get();
        }
        return DataTables::of($seguimientos)->toJson();
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
        return response()->json(['success' => 'seguimiento eliminado correctamente']);
    }
    public function notificacion(Cotizacion $cotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $response = [
            'nroCotizacion' => str_pad($cotizacion->id,5,'0',STR_PAD_LEFT),
            'fechaFinGarantia' => date('d/m/Y',strtotime($cotizacion->fecha_fin_garantia)),
            'cliente' => $cotizacion->cliente->nombreCliente,
            'celular' => $cotizacion->cliente->usuario->celular,
            'servicios' => CotizacionServicio::mostrarServiciosConProductos($cotizacion->id)->select("servicios.servicio")->get(),
            'usuario' => Auth::user()->nombres
        ];
        return response()->json($response);
    }
}
