<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\PreCotizaion;
use App\Models\User;
use App\Models\UsuariosActvidades;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class Programacion extends Controller
{
    private $moduloProgramacion = "admin.programacion.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProgramacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $usuarios = User::obtenerUsuariosNoSonClientes();
        $hoy = date('Y-m-d');
        $nroDiaHoy = date('N',strtotime($hoy));
        $nroDiaLunes = $nroDiaHoy - 1;
        $nroDiaDomingo = 7 - $nroDiaHoy;
        $fechaFin = date('Y-m-d',strtotime($hoy . '+ ' . $nroDiaDomingo . ' days'));
        $fechaInicio = date('Y-m-d',strtotime($hoy . '- ' . $nroDiaLunes . ' days'));
        return view("administracion.programacion",compact("modulos","usuarios","fechaInicio","fechaFin"));
    }
    public function reporteProgramacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProgramacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $fechaInicio = $request->fecha_inicio . ' 00:00:00';
        $fechaFin = $request->fecha_fin . ' 23:59:59';
        $actividades = UsuariosActvidades::obtenerActividades($fechaInicio,$fechaFin,$request->responsables);
        $configuracion = Configuracion::obtener();
        $titulo = "PROGRAMACIÓN DE ACTIVIDADES DESDE " . date('d/m/Y',strtotime($request->fecha_inicio)) . ' HASTA ' . date('d/m/Y',strtotime($request->fecha_fin));
        return Pdf::loadView('administracion.reportes.programacionReporte',compact("titulo","actividades","configuracion"))->stream("PROGRAMACION_REPORTE_" . $fechaInicio . '_'.$fechaFin. '.pdf');

    }
    public function all(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProgramacion);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $fechaInicio = $request->fechaHrInicio . ' 00:00:00';
        $fechaFin = $request->fechaHrFin . ' 23:59:59';
        $actividades = UsuariosActvidades::obtenerActividades($fechaInicio,$fechaFin,$request->reponsable);
        return DataTables::of($actividades)->toJson();
    }
    public function update(UsuariosActvidades $programacion, Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProgramacion);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $datos = $request->except("_method");
        $programacion->update($datos);
        return response()->json(['success' => 'actividad actualizada correctamente']);
    }
    public function destroy(UsuariosActvidades $programacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProgramacion);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $programacion->delete();
        return response()->json(['success' => 'actividad eliminada correctamente']);
    }
    public function show(UsuariosActvidades $programacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProgramacion);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        return response()->json(['programacion' => $programacion->select("id","id_usuario","fecha_hr_inicio","fecha_hr_fin","tarea")->find($programacion->id)]);
    }
    public function store(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProgramacion);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        DB::beginTransaction();
        try {
            for ($i=0; $i < count($request->tarea); $i++) { 
                UsuariosActvidades::create([
                    'id_usuario' => $request->id_usuario,
                    'fecha_hr_inicio' => $request->fechaHrInicio[$i],
                    'fecha_hr_fin' => $request->fechaHrFin[$i],
                    'tarea' => $request->tarea[$i]
                ]);
            }
            DB::commit();
            return response()->json(['success' => 'actividades generadas correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
