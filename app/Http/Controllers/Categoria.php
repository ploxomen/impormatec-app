<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Usuario;
use App\Models\Categoria as ModelsCategoria;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class Categoria extends Controller
{
    private $moduloArea = "admin.categoria.index";
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
        return view("productos.categoria",compact("modulos"));
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
        $marca = ModelsCategoria::all();
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
        ModelsCategoria::create(['nombreCategoria' => $request->nombreCategoria, 'estado' => $request->has("activo") ? 1 : 0]);
        return response()->json(['success' => 'categoría agregada correctamente']);
    }
    public function show(ModelsCategoria $categoria, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $categoria = $categoria->makeHidden("fechaCreada","fechaActualizada")->toArray();
        return response()->json(["success" => $categoria]);
    }
    public function update(ModelsCategoria $categoria, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $categoria->update(['nombreCategoria' => $request->nombreCategoria, 'estado' => $request->has("activo") ? 1 : 0]);
        return response()->json(['success' => 'categoría modificada correctamente']);
    }
    public function destroy(ModelsCategoria $categoria, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if($categoria->productos()->count() > 0){
            return ["alerta" => "Debes eliminar primero los productos relacionados a esta categoria"];
        }
        $categoria->delete();
        return response()->json(['success' => 'categoría eliminada correctamente']);
    }
}
