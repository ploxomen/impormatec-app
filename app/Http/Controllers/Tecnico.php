<?php

namespace App\Http\Controllers;

use App\Models\PreCotizaion;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Tecnico extends Controller
{
    private $usuarioController;
    private $moduloPrimVisiPreCoti = "cotizacion.tecnico.visita.pre";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexPrimeraVisitaPreCotizacion()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPrimVisiPreCoti);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $idTecnico = empty(Auth::user()->tecnico) ? null : Auth::user()->tecnico->id;
        $servicios = Servicio::where('estado',1)->get();
        return view("tecnico.primeraVisita",compact("modulos","servicios"));
    }
    public function accionesPreCotizacion(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPrimVisiPreCoti);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $idTecnico = empty(Auth::user()->tecnico) ? null : Auth::user()->tecnico->id;
        $respuesta = [];
        switch ($request->acciones) {
            case 'ver-visitas':
                $visitas = PreCotizaion::obtenerPreCotizacionPorTecnicoFecha($idTecnico,$request->fecha);
                $todasFechaVisita = PreCotizaion::obtenerFechasParaFiltroTecnico($idTecnico);
                $respuesta = ['visitas' => $visitas, 'filtros' => $todasFechaVisita];
                break;
            case 'generar-reporte':
                $respuesta = PreCotizaion::actualizarReporteTecnico($request->visita,$request->html,$request->servicios,$idTecnico,Auth::id());
                break;
            default:
                # code...
                break;
        }
        return response()->json($respuesta);
    }
}
