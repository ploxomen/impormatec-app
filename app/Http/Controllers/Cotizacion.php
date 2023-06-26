<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\PreCotizaion;
use App\Models\Servicio;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;

class Cotizacion extends Controller
{
    private $usuarioController;
    private $moduloCotizacionAgregar = "admin.cotizacion.agregar.index";
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
        return view("cotizacion.nuevaCotizacion",compact("modulos","clientes","tiposDocumentos","preCotizaciones","servicios"));
    }
}
