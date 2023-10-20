<?php

namespace App\Http\Controllers;

use App\Mail\CorreoMasivo;
use App\Models\Clientes;
use App\Models\Publicidad;
use App\Models\PublicidadClientes;
use App\Models\PublicidadDocumentos;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class Publicidades extends Controller
{
    private $moduloPublicidad = "admin.publicidad.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPublicidad);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $clientes = Clientes::obenerClientesActivos();
        return view("administracion.publicidad",compact("modulos","clientes"));
    }
    public function destroy(Publicidad $publicidad) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPublicidad);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        DB::beginTransaction();
        try {
            PublicidadClientes::where(['id_publicidad' => $publicidad->id])->delete();
            $documentos = PublicidadDocumentos::where(['id_publicidad' => $publicidad->id])->get();
            foreach ($documentos as $documento) {
                if(Storage::exists('/publicidad/' . $documento->nombre_sistema_documento)){
                    Storage::delete('/publicidad/' . $documento->nombre_sistema_documento);
                }
                $documento->delete();
            }
            $publicidad->delete();
            DB::commit();
            return response()->json(['success' => 'Publicidad eliminada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function update(Publicidad $publicidad,Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPublicidad);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $datosPublicidad = $request->only("cuerpo_publicidad","asunto");
        DB::beginTransaction();
        try {
            PublicidadClientes::where(['id_publicidad' => $publicidad->id])->delete();
            $publicidad->update(array_merge($datosPublicidad,['enviar_todos_clientes' => $request->has("envio_cliente") ? 1 : 0]));
            if($request->has('id_cliente')){
                for ($i=0; $i < count($request->id_cliente); $i++) { 
                    PublicidadClientes::create([
                        'id_publicidad' => $publicidad->id,
                        'id_cliente' => $request->id_cliente[$i]
                    ]);
                }
            }
            if($request->has('archivoPdf')){
                $archivos = $request->file('archivoPdf');
                foreach ($archivos as $archivo) {
                    $nombreArchivo = $archivo->getClientOriginalName();
                    $nombreArchivoSistema = uniqid() . '.' . $nombreArchivo;
                    $archivo->storeAs('publicidad', $nombreArchivoSistema);
                    PublicidadDocumentos::create([
                        'id_publicidad' => $publicidad->id,
                        'nombre_real_documento' => $nombreArchivo,
                        'nombre_sistema_documento' => $nombreArchivoSistema
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Publicidad actualizada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function show(Publicidad $publicidad) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPublicidad);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $response = [
            'id' => $publicidad->id,
            'asunto' => $publicidad->asunto,
            'clientes' => PublicidadClientes::select("id_cliente")->where(['id_publicidad' => $publicidad->id])->get()->toArray(),
            'documentos' => PublicidadDocumentos::select("id","nombre_real_documento")->where(['id_publicidad' => $publicidad->id])->get()->toArray(),
            'cuerpo' => $publicidad->cuerpo_publicidad,
            'enviarTodosClientes' => $publicidad->enviar_todos_clientes
        ];
        return response()->json($response);
    }
    public function eliminarDocumentoPublicidad($publicidad,$documento) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPublicidad);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $documento = PublicidadDocumentos::where(['id_publicidad' => $publicidad,'id' => $documento])->first();
        if(empty($documento)){
            return response()->json(['error' => 'No se encontro el documento para ser eliminado']);
        }
        if(Storage::exists('/publicidad/' . $documento->nombre_sistema_documento)){
            Storage::delete('/publicidad/' . $documento->nombre_sistema_documento);
        }
        $documento->delete();
        return response()->json(['success' => 'documento eliminado correctamente']);
    }
    public function reenviar(Publicidad $publicidad) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPublicidad);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        try {
            $this->enviarCorreo($publicidad->id);
            $publicidad->update(['ultimo_envio' => now()]);
            return response()->json(['success' => 'publicidad reenviada correctamente']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function store(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPublicidad);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $datosPublicidad = $request->only("cuerpo_publicidad","asunto");
        DB::beginTransaction();
        try {
            $publicidad = Publicidad::create(array_merge($datosPublicidad,['enviar_todos_clientes' => $request->has("envio_cliente") ? 1 : 0,'id_responsable' => Auth::id(),'ultimo_envio' => now()]));
            if($request->has('id_cliente')){
                for ($i=0; $i < count($request->id_cliente); $i++) { 
                    PublicidadClientes::create([
                        'id_publicidad' => $publicidad->id,
                        'id_cliente' => $request->id_cliente[$i]
                    ]);
                }
            }
            if($request->has('archivoPdf')){
                $archivos = $request->file('archivoPdf');
                foreach ($archivos as $archivo) {
                    $nombreArchivo = $archivo->getClientOriginalName();
                    $nombreArchivoSistema = uniqid() . '.' . $nombreArchivo;
                    $archivo->storeAs('publicidad', $nombreArchivoSistema);
                    PublicidadDocumentos::create([
                        'id_publicidad' => $publicidad->id,
                        'nombre_real_documento' => $nombreArchivo,
                        'nombre_sistema_documento' => $nombreArchivoSistema
                    ]);
                }
            }
            $this->enviarCorreo($publicidad->id);
            DB::commit();
            return response()->json(['success' => 'publicidad enviada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(),'l' => $th->getLine()]);
        }
    }
    public function enviarCorreo($idPublicidad){
        $publicidad = Publicidad::find($idPublicidad);
        $clientes = $publicidad->enviar_todos_clientes === 1 ? Clientes::obenerClientesActivos()->toArray() :  PublicidadClientes::obtenerDatosClientes($publicidad->id)->toArray();
        $correoClientes = [];
        $documentos = PublicidadDocumentos::where(['id_publicidad' => $publicidad->id])->get()->toArray();
        foreach ($clientes as $cliente) {
            if(empty($cliente['correo'])){
                continue;
            }
            $correoClientes[] = $cliente['correo'];
        }
        $documentosEnvio = [];
        foreach ($documentos as $documento) {
            if(Storage::exists('/publicidad/' . $documento['nombre_sistema_documento'])){
                $documentosEnvio[] = ['url' => storage_path('app/publicidad/' . $documento['nombre_sistema_documento']), 'name' => $documento['nombre_real_documento']];
            }
        }
        if(count($correoClientes) === 0){
            throw new Exception('No se encontraron correos para ser enviados');
        }
        // dd($publicidad->cuerpo_publicidad);
        Mail::to($correoClientes)->send(new CorreoMasivo($publicidad->cuerpo_publicidad,$documentosEnvio,$publicidad->asunto));
        return ['success' => 'publicidad enviada correctamente'];
    }
    public function all() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPublicidad);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $publicidades = Publicidad::obtenerPublicidad()->toArray();
        return DataTables::of($publicidades)->toJson();
    }
}
