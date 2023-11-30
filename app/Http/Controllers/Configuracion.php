<?php

namespace App\Http\Controllers;

use App\Models\Configuracion as ModelsConfiguracion;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $datos = $request->except('formatoVisita');
        DB::beginTransaction();
        try {
            foreach ($datos as $key => $dato) {
                ModelsConfiguracion::where(['descripcion' => $key])->update(['valor' => $dato]);
            }
            if($request->has('formatoVisita')){
                $archivoAntiguo = ModelsConfiguracion::where(['descripcion' => 'formato_unico_visitas'])->first();
                if(Storage::disk('public')->exists($archivoAntiguo->valor)){
                    Storage::disk('public')->delete($archivoAntiguo->valor);
                }
                $archivo = $request->file('formatoVisita');
                $nombreGuardar = time().'_'.$archivo->getClientOriginalName();
                $archivo->storeAs('public',$nombreGuardar);
                $archivoAntiguo->update(['valor' => $archivo->getClientOriginalName()]);
                ModelsConfiguracion::where(['descripcion' => 'formato_unico_visitas_url'])->update(['valor' => $nombreGuardar]);
            }
            DB::commit();
            return response()->json(['success' => 'datos modificados correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
