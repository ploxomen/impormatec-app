<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\OrdenServicio;
use App\Models\OrdenServicioCotizacionServicio;
use Illuminate\Http\Request;

class Informes extends Controller
{
    private $usuarioController;
    private $moduloMisInformes = "admin.informe.lista";
    private $moduloGenerarInforme = "informe.generar";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function aprobarCotizacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session']) && isset($verif2['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $ordenServicio = null;
        if($request->has("cliente") && $request->has("ordenServicio")){
            $ordenServicio = OrdenServicio::where(['id' => $request->ordenServicio, 'id_cliente' => $request->cliente, 'estado' => 1])->first();
            if(!empty($ordenServicio)){
                foreach ($ordenServicio->servicios as $servicio) {
                    $data = is_null($servicio->objetivos) || is_null($servicio->acciones) || is_null($servicio->descripcion) ? OrdenServicioCotizacionServicio::obtenerServicio($ordenServicio->id,$servicio->id) : null;
                    if(is_null($servicio->objetivos) && !is_null($data)){
                        $servicio->update(['objetivos' => $data->servicio]);
                    }
                    if(is_null($servicio->acciones) && !is_null($data)){
                        $servicio->update(['acciones' => $data->acciones]);
                    }
                    if(is_null($servicio->descripcion) && !is_null($data)){
                        $servicio->update(['descripcion' => $data->descripcion]);
                    }
                }
            }
        }
        // dd($ordenServicio);
        return view("ordenesServicio.generarReporte",compact("modulos","clientes","ordenServicio"));
    }
    public function actualizarCamposInforme($columna) {
        
    }
    public function obtenerOrdenesServicioCliente(Request $request,$idCliente) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $ordenesServicios = OrdenServicio::ordenServiciosCliente($idCliente);
        return response()->json(['ordenesServicio' => $ordenesServicios]);
    }
}
