<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Usuario;
use App\Models\Almacen as ModelsAlmacen;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class Almacenes extends Controller
{
    private $moduloAlmacen = "admin.almacenes.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloAlmacen);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("almacen.almacenes",compact("modulos"));
    }
    public function listar(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAlmacen);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $marca = ModelsAlmacen::all();
        return DataTables::of($marca)->toJson();
    }
    public function store(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAlmacen);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $datos = $request->only("nombre","descripcion","direccion");
        $datos['estado'] = 1;
        ModelsAlmacen::create($datos);
        return response()->json(['success' => 'almacen agregado correctamente']);
    }
    public function show(ModelsAlmacen $almacen, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAlmacen);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $almacen = $almacen->makeHidden("fechaCreada","fechaActualizada")->toArray();
        return response()->json(["success" => $almacen]);
    }
    public function update(ModelsAlmacen $almacen, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAlmacen);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $datos = $request->only("nombre","descripcion","direccion");
        $datos['estado'] = $request->has("estado") ? 1 : 0;
        $almacen->update($datos);
        return response()->json(['success' => 'almacen modificado correctamente']);
    }
    public function destroy(ModelsAlmacen $almacen, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAlmacen);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $almacen->delete();
        return response()->json(['success' => 'almacen eliminado correctamente']);
    }
}
