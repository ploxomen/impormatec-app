<?php

namespace App\Http\Controllers;

use App\Models\Servicio as ModelsServicio;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class Servicio extends Controller
{
    private $moduloArea = "admin.servicios";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("almacen.servicios",compact("modulos"));
    }
    public function listar(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $servicios = ModelsServicio::obtenerServicios();
        return DataTables::of($servicios)->toJson();
    }
    public function store(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        ModelsServicio::create(['servicio' => $request->servicio, 'descripcion' => $request->descripcion,'estado' => 1]);
        return response()->json(['success' => 'servicio agregado correctamente']);
    }
    public function show(ModelsServicio $servicio, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $servicio = $servicio->makeHidden("fechaCreada","fechaActualizada")->toArray();
        return response()->json(["servicio" => $servicio]);
    }
    public function update(ModelsServicio $servicio, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $servicio->update(['servicio' => $request->servicio,'descripcion' => $request->descripcion, 'estado' => $request->has("estado") ? 1 : 0]);
        return response()->json(['success' => 'servicio modificado correctamente']);
    }
    public function destroy(ModelsServicio $servicio, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        // if($marca->productos()->count() > 0){
        //     return ["alerta" => "Debes eliminar primero los productos relacionados a esta marca"];
        // }
        $servicio->delete();
        return response()->json(['success' => 'servicio eliminado correctamente']);
    }
}
