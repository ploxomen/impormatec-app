<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Usuario;
use App\Models\Presentacion as ModelsPresentacion;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class Presentacion extends Controller
{
    private $moduloArea = "admin.presentacion.index";
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
        return view("productos.presentacion",compact("modulos"));
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
        $marca = ModelsPresentacion::all();
        return DataTables::of($marca)->toJson();
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
        ModelsPresentacion::create(['nombrePresentacion' => $request->nombrePresentacion,'siglas' => $request->siglas ,'estado' => $request->has("activo") ? 1 : 0]);
        return response()->json(['success' => 'presentación agregada correctamente']);
    }
    public function show(ModelsPresentacion $presentacion, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $categoria = $presentacion->makeHidden("fechaCreada","fechaActualizada")->toArray();
        return response()->json(["success" => $categoria]);
    }
    public function update(ModelsPresentacion $presentacion, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $presentacion->update(['nombrePresentacion' => $request->nombrePresentacion,'siglas' => $request->siglas ,'estado' => $request->has("activo") ? 1 : 0]);
        return response()->json(['success' => 'presentación modificada correctamente']);
    }
    public function destroy(ModelsPresentacion $presentacion, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if($presentacion->productos()->count() > 0){
            return ["alerta" => "Debes eliminar primero los productos relacionados a esta presentación"];
        }
        $presentacion->delete();
        return response()->json(['success' => 'presentación eliminada correctamente']);
    }
}
