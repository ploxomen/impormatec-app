<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use App\Models\Rol;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class Modulos extends Controller
{
    private $userControler = null;
    private $moduloModulo = "admin.modulos.index";
    function __construct()
    {
        $this->userControler = new Usuario();
    }
    public function index()
    {
        $verif = $this->userControler->validarXmlHttpRequest($this->moduloModulo);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->userControler->obtenerModulos();
        $roles = Rol::all();
        return view('usuario.modulo',compact("modulos","roles"));
    }
    public function accionesModulos(Request $request)
    {
        if(!$request->ajax()){
            return response()->json(['error' => 'error en la petición']);
        }
        $accessModulo = $this->userControler->validarXmlHttpRequest($this->moduloModulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        switch ($request->accion) {
            case 'obtener':
                $modulos = Modulo::select("id","titulo","icono","descripcion","grupoFk")->with("grupos:id,grupo,icono")->where('estado',1)->get();
                return DataTables::of($modulos)->toJson();
            break;
            case 'obtenerRoles':
                $roles = Modulo::find($request->modulo)->roles()->select("modulo_roles.rolFk")->get()->makeHidden("pivot");
                return response()->json(['roles' => $roles]);
            break;
            case 'asignarRol':
                $modulo = Modulo::find($request->modulo);
                $modulo->roles()->detach();
                $modulo->roles()->attach($request->roles);
                return response()->json(['success' => 'roles actualizados correctamente']);
            break;
        }
    }
}
