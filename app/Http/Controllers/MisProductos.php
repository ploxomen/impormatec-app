<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Usuario;
use App\Models\Almacen;
use App\Models\ProductoAlmacen;
use App\Models\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MisProductos extends Controller
{
    private $moduloArea = "admin.producto.index";
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
        $almacenes = Almacen::where('estado',1)->get();
        return view("productos.productos",compact("modulos","almacenes"));
    }
    public function listar(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $productos = Productos::with('almacenes:id,nombre')->get();
        return DataTables::of($productos)->toJson();
    }
    public function store(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $urlImage = null;
        DB::beginTransaction();
        try {
            $datos = $request->only("nombreProveedor","nombreProducto","descripcion","tipoMoneda","precioCompra","precioVenta","stockMin","esIntangible");
            if($request->has('urlImagen')){
                $datos['urlImagen'] = $this->guardarArhivo($request,'urlImagen',"productos");
                $urlImage = $datos['urlImagen'];
            }
            $datos['estado'] = 1;
            $producto = Productos::create($datos);
            if($request->has("idAlmacen")){
                for ($i=0; $i < count($request->idAlmacen); $i++) {
                    if(!isset($request->idAlmacen[$i])){
                        continue;
                    }
                    ProductoAlmacen::create([
                        'id_producto' => $producto->id,
                        'id_almacen' => $request->idAlmacen[$i],
                        'stock' => isset($request->stockAlmacen[$i]) ? $request->stockAlmacen[$i] : 1,
                        'estado' => 1
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'producto agregado correctamente']);
        } catch (\Throwable $th) {
            if(Storage::disk('productos')->exists($urlImage)){
                Storage::disk('productos')->delete($urlImage);
            }
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function show(Productos $producto)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $producto->urlProductos = !empty($producto->urlImagen) ? route("urlImagen",["productos",$producto->urlImagen]) : null;
        $producto->listaAlmacen = ProductoAlmacen::obtenerAlmacen($producto->id);
        return response()->json(['producto' => $producto->makeHidden("fechaCreada","fechaActualizada")]);
    }
    public function eliminarAlmacen(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        ProductoAlmacen::where(['id_almacen' => $request->idAlmacen,'id_producto' => $request->idProducto])->delete();
        return response()->json(['success' => "el producto fue eliminado del almacen de manera correcta"]);
    }
    public function update(Productos $producto, Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $urlImage = null;
        DB::beginTransaction();
        try {
            $datos = $request->only("nombreProducto","descripcion","tipoMoneda","precioCompra","precioVenta","stockMin","esIntangible");
            $datos['esIntangible'] = $request->has("esIntangible");
            if($request->has('urlImagen')){
                if(!empty($producto->urlImagen) && Storage::disk('productos')->exists($producto->urlImagen)){
                    Storage::disk('productos')->delete($producto->urlImagen);
                }
                $datos['urlImagen'] = $this->guardarArhivo($request,'urlImagen',"productos");
                $urlImage = $datos['urlImagen'];
            }
            $datos['estado'] = $request->has('estado');
            if($datos['esIntangible']){
                ProductoAlmacen::where('id_producto',$producto->id)->delete();
            }
            $producto->update($datos);
            if($request->has("idAlmacen") && !$datos['esIntangible']){
                for ($i=0; $i < count($request->idAlmacen); $i++) {
                    if(!isset($request->idAlmacen[$i])){
                        continue;
                    }
                    ProductoAlmacen::updateOrCreate(
                        ['id_producto' => $producto->id,'id_almacen' => $request->idAlmacen[$i]],
                        ['precioVenta' => isset($request->precioVenta[$i]) ? $request->precioVenta[$i] : 0 ,'stock' => isset($request->stockAlmacen[$i]) ? $request->stockAlmacen[$i] : 1,'estado' => 1]
                    );
                }
            }
            DB::commit();
            return response()->json(['success' => 'producto actualizado correctamente']);
        } catch (\Throwable $th) {
            if(Storage::disk('productos')->exists($urlImage)){
                Storage::disk('productos')->delete($urlImage);
            }
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function destroyImagen(Productos $producto) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        if(empty($producto->urlImagen)){
            return response()->json(['alerta' => 'El producto no cuenta con una imagen para ser eliminada']);
        }
        if(!empty($producto->urlImagen) && Storage::disk('productos')->exists($producto->urlImagen)){
            Storage::disk('productos')->delete($producto->urlImagen);
        }
        $producto->update(['urlImagen' => null]);
        return response()->json(['success' => 'Imagen eliminada correctamente']);
    }
    public function destroy(Productos $producto)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloArea);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        DB::beginTransaction();
        try {
            if(!empty($producto->urlImagen) && Storage::disk('productos')->exists($producto->urlImagen)){
                Storage::disk('productos')->delete($producto->urlImagen);
            }
            ProductoAlmacen::where('id_producto',$producto->id)->delete();
            $producto->delete();
            DB::commit();
            return response()->json(['success' => 'producto eliminado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function guardarArhivo($request,$key,$disk)
    {
        $nombreOriginal = $request->file($key)->getClientOriginalName();
        $nombreArchivo = pathinfo($nombreOriginal,PATHINFO_FILENAME);
        $extension = $request->file($key)->getClientOriginalExtension();
        $archivoNombreAlmacenamiento = $nombreArchivo.'_'.time().'.'.$extension;
        $request->file($key)->storeAs($disk,$archivoNombreAlmacenamiento);
        return $archivoNombreAlmacenamiento;
    }
    public function guardarArhivoMasivo($request,$key,$disk)
    {
        $tiempo = time();
        $archivosAlmacenados = [];
        for ($i=0; $i < count($request->file($key)); $i++) { 
            $tiempo++;
            $nombreOriginal = $request->file($key)[$i]->getClientOriginalName();
            $nombreArchivo = pathinfo($nombreOriginal,PATHINFO_FILENAME);
            $extension = $request->file($key)[$i]->getClientOriginalExtension();
            $archivoNombreAlmacenamiento = $nombreArchivo.'_'.$tiempo.'.'.$extension;
            $request->file($key)[$i]->storeAs($disk,$archivoNombreAlmacenamiento);
            $archivosAlmacenados[] = ['url_imagen' => $archivoNombreAlmacenamiento,'nombre_real' => $nombreArchivo . '.' . $extension];
        }
        return $archivosAlmacenados;
    }
}
