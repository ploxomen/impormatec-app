<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Cotizacion;
use Illuminate\Http\Request;

class OrdenServicio extends Controller
{
    private $usuarioController;
    private $moduloOSAgregar = "os.generar.index";
    private $moduloOSlista = "admin.caotizacion.todos";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexNuevaOs() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOSAgregar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        return view("ordenesServicio.agregar",compact("modulos","clientes"));
    }
    public function obtenerCotizacionCliente($cliente) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOSAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $servicios = Cotizacion::obtenerCotizacionesAprobadas($cliente);
        return response()->json(['servicios' => $servicios]);
    }
}
