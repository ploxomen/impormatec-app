<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Usuario;
use App\Models\Marca as ModelsMarca;
use Yajra\DataTables\Facades\DataTables;

class Marca extends Controller
{
    private $moduloArea = "admin.marca.index";
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
        return view("productos.marca",compact("modulos"));
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
        $marca = ModelsMarca::all();
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
        ModelsMarca::create(['nombreMarca' => $request->nombreMarca, 'estado' => $request->has("activo") ? 1 : 0]);
        return response()->json(['success' => 'marca agregada correctamente']);
    }
    public function show(ModelsMarca $marca, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $marca = $marca->makeHidden("fechaCreada","fechaActualizada")->toArray();
        return response()->json(["success" => $marca]);
    }
    public function update(ModelsMarca $marca, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $marca->update(['nombreMarca' => $request->nombreMarca, 'estado' => $request->has("activo") ? 1 : 0]);
        return response()->json(['success' => 'marca modificada correctamente']);
    }
    public function destroy(ModelsMarca $marca, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if($marca->productos()->count() > 0){
            return ["alerta" => "Debes eliminar primero los productos relacionados a esta marca"];
        }
        $marca->delete();
        return response()->json(['success' => 'marca eliminada correctamente']);
    }
}
