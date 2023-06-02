<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use App\Models\Rol as ModelsRol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class Rol extends Controller
{
    private $userControler = null;
    private $moduloRol = "admin.rol.index";
    function __construct()
    {
        $this->userControler = new Usuario();
    }
    public function viewRol(Request $request)
    {
        $verif = $this->userControler->validarXmlHttpRequest($this->moduloRol);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->userControler->obtenerModulos();
        $modulosLista = Modulo::with("grupos")->where('estado',1)->orderBy("grupoFk")->get();
        return view("usuario.roles",compact("modulos", "modulosLista"));
    }
    public function accionesRoles(Request $request)
    {
        if(!$request->ajax()){
            return response()->json(['error' => 'error en la consulta']);
        }
        $accessModulo = $this->userControler->validarXmlHttpRequest($this->moduloRol);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        switch ($request->accion) {
            case 'obtener':
                $roles = ModelsRol::all();
                return DataTables::of($roles)->toJson();
            break;
            case 'mostarEditar':
                $rol = ModelsRol::find($request->rol);
                return response()->json(['success' => $rol]);
            break;
            case 'editarModuloRol':
                $rol = ModelsRol::find($request->rol);
                $rol->modulos()->detach();
                $rol->modulos()->attach($request->modulo);
                return response()->json(['success' => 'modulos actualizados correctamente']);
                // ->modulos()->newPivotQuery()->upsert($moduloRol,['moduloFk','rolFk'],['moduloFk']);
            break;
            case 'nuevoRol':
                $rol = ModelsRol::create(['nombreRol' => $request->rol]);
                $rol->modulos()->attach($request->modulo);
                return response()->json(['success' => 'rol agregado correctamente']);
            break;
            case 'verModulos':
                $modulos = ModelsRol::find($request->rol)->modulos()->select("modulo_roles.moduloFk")->where('estado',1)->get()->makeHidden("pivot");
                return response()->json(['modulos' => $modulos]);
            break;
            case 'editarRol':
                ModelsRol::where('id',$request->rolId)->update(['nombreRol' => $request->rol]);
                return response()->json(['success' => 'rol actualizado correctamente']);
            break;
            case 'eliminar':
                $modeloRol = ModelsRol::find($request->rol)->usuarios();
                DB::beginTransaction();
                try {
                    if ($modeloRol->count() > 0) {
                        return response()->json(['alerta' => 'No se puede eliminar el rol, primero elimine los usuarios asociados a el.']);
                    }
                    $moduloRol = ModelsRol::find($request->rol)->modulos();
                    $moduloRol->detach();
                    $modeloRol->detach();
                    ModelsRol::find($request->rol)->delete();
                    DB::commit();
                    return response()->json(['success' => 'rol eliminado correctamente']);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(['error' => $th->getMessage(), 'codigo' => $th->getCode()]);
                }
            break;
        }
    }
}
