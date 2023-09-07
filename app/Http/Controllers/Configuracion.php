<?php

namespace App\Http\Controllers;

use App\Models\Configuracion as ModelsConfiguracion;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Configuracion extends Controller
{
    private $usuarioController;
    private $moduloConfiguracionNegocio = "admin.configuracion.negocio";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexConfiguracionNegocio()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloConfiguracionNegocio);
        if (isset($verif['session'])) {
            return redirect()->route("home");
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get(); 
        $configuracion = ModelsConfiguracion::all();
        return view("administracion.configuracion",compact("modulos","tiposDocumentos",'configuracion'));
    }
    public function actualizarInformacionNegocio(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloConfiguracionNegocio);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $datos = $request->all();
        DB::beginTransaction();
        try {
            foreach ($datos as $key => $dato) {
                ModelsConfiguracion::where(['descripcion' => $key])->update(['valor' => $dato]);
            }
            DB::commit();
            return response()->json(['success' => 'datos modificados correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
