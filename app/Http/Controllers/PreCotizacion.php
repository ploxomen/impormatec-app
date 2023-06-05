<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Servicio;
use App\Models\Tecnico;
use Illuminate\Http\Request;

class PreCotizacion extends Controller
{
    private $usuarioController;
    private $moduloPreCotizacion = "cotizacion.precotizacion.nueva";
    private $moduloMisPreCotizacion = "cotizacion.precotizacion.lista";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexNuevaPreCotizacion()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $tecnicos = Tecnico::obtenerTecnicosActivos();
        $servicios = Servicio::where('estado',1)->get();
        return view("preCotizacion.nuevaPreCotizacion",compact("modulos","clientes","tecnicos","servicios"));
    }
    public function indexMisPreCotizaciones()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("preCotizacion.misPreCotizaciones",compact("modulos"));
    }
}
