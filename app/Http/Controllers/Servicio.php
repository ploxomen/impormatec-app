<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Servicio as ModelsServicio;
use App\Models\ServicioProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class Servicio extends Controller
{
    private $moduloServicio = "admin.servicios";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloServicio);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $productos = Productos::where('estado',1)->get();
        return view("almacen.servicios",compact("modulos","productos"));
    }
    public function listar(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloServicio);
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
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloServicio);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        DB::beginTransaction();
        try {
            $datos = $request->only("servicio","descripcion","acciones","objetivos");
            $datos['estado'] = 1;
            $servicio = ModelsServicio::create($datos);
            if($request->has("idProducto")){
                for ($i=0; $i < count($request->idProducto); $i++) {
                    if(!isset($request->idProducto[$i])){
                        continue;
                    }
                    ServicioProducto::create([
                        'id_servicio' => $servicio->id,
                        'id_producto' => $request->idProducto[$i],
                        'cantidadUsada' => isset($request->cantidadProducto[$i]) ? $request->cantidadProducto[$i] : 1,
                        'estado' => 1
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'servicio agregado correctamente']);
        }catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function show(ModelsServicio $servicio, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloServicio);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $servicio->listaProductos = ServicioProducto::obtenerProductos($servicio->id);
        $servicio = $servicio->makeHidden("fechaCreada","fechaActualizada");
        return response()->json(["servicio" => $servicio]);
    }
    public function update(ModelsServicio $servicio, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloServicio);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        DB::beginTransaction();
        try {
            $datos = $request->only("servicio","descripcion","acciones","objetivos");
            $datos['estado'] = $request->has('estado');
            $servicio->update($datos);
            if($request->has("idProducto")){
                for ($i=0; $i < count($request->idProducto); $i++) {
                    if(!isset($request->idProducto[$i])){
                        continue;
                    }
                    ServicioProducto::updateOrCreate(
                        ['id_servicio' => $servicio->id,'id_producto' => $request->idProducto[$i]],
                        ['cantidadUsada' => isset($request->cantidadProducto[$i]) ? $request->cantidadProducto[$i] : 1,'estado' => 1]
                    );
                }
            }
            DB::commit();
            return response()->json(['success' => 'servicio modificado correctamente']);
        }catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function eliminarProducto(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloServicio);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        ServicioProducto::where(['id_producto' => $request->idProducto,'id_servicio' => $request->idServicio])->delete();
        return response()->json(['success' => "el producto fue eliminado del servicio de manera correcta"]);
    }
    public function destroy(ModelsServicio $servicio, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloServicio);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        ServicioProducto::where('id_servicio',$servicio->id)->delete();
        $servicio->delete();
        return response()->json(['success' => 'servicio eliminado correctamente']);
    }
}
