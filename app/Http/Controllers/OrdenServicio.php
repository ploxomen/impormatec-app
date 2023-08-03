<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Cotizacion;
use App\Models\CotizacionServicio;
use App\Models\OrdenServicio as ModelsOrdenServicio;
use App\Models\OrdenServicioAdicional;
use App\Models\OrdenServicioCotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrdenServicio extends Controller
{
    private $usuarioController;
    private $moduloOSAgregar = "os.generar.index";
    private $moduloOsMostrar = "admin.ordenesServicios.index";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexNuevaOs() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOSAgregar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        return view("ordenesServicio.agregar",compact("modulos","clientes"));
    }
    public function obtenerCotizacionCliente($cliente) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOSAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $servicios = Cotizacion::obtenerCotizacionesAprobadas($cliente);
        return response()->json(['servicios' => $servicios]);
    }
    public function indexMisOs()  {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        return view("ordenesServicio.misOrdenes",compact("modulos","clientes"));
    }
    public function accionesOrdenServicio(Request $request)  {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        switch ($request->acciones) {
            case 'eliminar-adicional':
                OrdenServicioAdicional::where(['id_orden_servicio' => $request->ordenServicioId,'id' => $request->adicionalId])->delete();
                $this->actualizarMontosOrdenServicio($request->ordenServicioId);
                return response()->json(['success' => 'Servicio adicional eliminado correctamente']);
            break;
            case 'eliminar-cotizacion':
                DB::beginTransaction();
                try {
                    if(OrdenServicioCotizacion::where(['id_orden_servicio' => $request->ordenServicioId])->count() == 1){
                        return response()->json(['alerta' => 'La orden de servicio debe contener al menos un servicio de la cotización']);
                    }
                    OrdenServicioCotizacion::where(['id_orden_servicio' => $request->ordenServicioId,'id_cotizacion_servicio' => $request->cotizacionServicioId])->delete();
                    $cotizacionServicio = CotizacionServicio::find($request->cotizacionServicioId);
                    $cotizacion = Cotizacion::find($cotizacionServicio->id_cotizacion);
                    $cotizacionServicio->update(['estado' => 1]);
                    $cotizacion->update(['estado' => 3]);
                    if($cotizacion->cotizacionSerivicios()->count() == $cotizacion->cotizacionSerivicios()->where('estado',1)->count()){
                        $cotizacion->update(['estado' => 2]);
                    }
                    $this->actualizarMontosOrdenServicio($request->ordenServicioId); 
                    DB::commit();
                    return response()->json(['success' => 'Servicio eliminado correctamente']);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(['alerta' => $th->getMessage()]);
                }
            break;
            case 'agregar-cotizacion':
                DB::beginTransaction();
                try {
                    $respuesta = [];
                    $cotizacion = Cotizacion::find($request->idCotizacion);
                    $cotizacion->update(['estado' => 4]);
                    $ordenServicio = ModelsOrdenServicio::find($request->idOrdenServicio);
                    foreach ($cotizacion->cotizacionSerivicios()->where('estado',1)->get() as $servicio) {
                        $servicio->update(['estado' => 2]);
                        $datos = OrdenServicioCotizacion::updateOrCreate([
                            'id_orden_servicio' => $request->idOrdenServicio,
                            'id_cotizacion_servicio' => $servicio->id
                        ],['estado' => 1]);
                        $respuesta[] = [
                            'cantidad' => $servicio->cantidad,
                            'costo' => $servicio->costo,
                            'descuento' => $servicio->descuento,
                            'fechaOs' => $ordenServicio->fecha,
                            'idCotizacionServicio' => $servicio->id,
                            'idOsCotizacion' => $datos->id,
                            'igv' => $servicio->igv,
                            'importe' => $servicio->importe,
                            'nroCotizacion' => str_pad($servicio->id_cotizacion,5,'0',STR_PAD_LEFT),
                            'servicio' => $servicio->servicios->servicio,
                            'total' => $servicio->total
                        ];
                    }
                    $this->actualizarMontosOrdenServicio($request->idOrdenServicio); 
                    DB::commit();
                    return response()->json(['success' => 'Los servicios de la cotización se agregaron correctamente','listaServicios' => $respuesta]);
                }catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(['alerta' => $th->getMessage()]);
                }
            break;
            case 'actualizar-orden':
                DB::beginTransaction();
                try {
                    ModelsOrdenServicio::find($request->ordenServicioId)->update(['fecha' => $request->fecha]);
                    if($request->has('descripcion')){
                        for ($i=0; $i < count($request->descripcion); $i++) { 
                            $idAdicional = isset($request->idAdicional[$i]) ? $request->idAdicional[$i] : null;
                            $cantidad = isset($request->cantidad[$i]) && !empty($request->cantidad[$i]) ? $request->cantidad[$i] : 1;
                            $precio = isset($request->precio[$i]) && !empty($request->precio[$i]) ? $request->precio[$i] : 0;
                            $total = $cantidad * $precio;
                            $contenido = [
                                'descripcion' => $request->descripcion[$i],
                                'cantidad' => $cantidad,
                                'precio' => $precio,
                                'total' => $total
                            ];
                            if(is_null($idAdicional)){
                                $contenido['id_orden_servicio'] = $request->ordenServicioId;
                                $contenido['estado'] = 1;
                                OrdenServicioAdicional::create($contenido);
                                continue;
                            }
                            OrdenServicioAdicional::find($idAdicional)->update($contenido);
                        }
                    }
                    $this->actualizarMontosOrdenServicio($request->ordenServicioId);
                    DB::commit();
                    return response()->json(['success' => 'Los datos de la orden de servicio se actualizaron correctamente']);
                }catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(['alerta' => $th->getMessage()]);
                }
            break;
        }
    }
    function actualizarMontosOrdenServicio($idOrdenServicio){
        $cotizaciones = OrdenServicioCotizacion::select("id_cotizacion_servicio")->where('id_orden_servicio',$idOrdenServicio)->get()->toArray();
        $cotizaciones = array_map(function($val){
            return $val['id_cotizacion_servicio'];
        },$cotizaciones);
        $servicios = CotizacionServicio::whereIn('id',$cotizaciones)->get();
        $calculosTotales = [
            'importe' => 0,
            'descuento' => 0,
            'igv' => 0,
            'adicional' => 0,
            'total' => 0
        ];
        foreach ($servicios as $servicio) {
            $calculosTotales['importe'] += $servicio->total;
            $calculosTotales['descuento'] += $servicio->descuento;
            $calculosTotales['igv'] += $servicio->igv;
        }
        $adicionales = OrdenServicioAdicional::where('id_orden_servicio',$idOrdenServicio)->get();
        foreach ($adicionales as $adicional) {
            $calculosTotales['adicional'] += $adicional->total;
        }
        $calculosTotales['total'] = $calculosTotales['importe'] - $calculosTotales['descuento'] + $calculosTotales['adicional'];
        return ModelsOrdenServicio::find($idOrdenServicio)->update($calculosTotales);
    }
    public function obtenerOrdenServicio() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $ordenesServicios = ModelsOrdenServicio::misOrdeneseServicio();
        return DataTables::of($ordenesServicios)->toJson();
    }
    public function obtenerDatosOrdenServicio(ModelsOrdenServicio $ordenServicio){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $ordenServicio->cotizaciones = ModelsOrdenServicio::datosCotizaciones($ordenServicio->id);
        $ordenServicio->adicionales = OrdenServicioAdicional::select("id AS idAdicional","descripcion","precio AS precioUnitario","cantidad","total")->where('id_orden_servicio',$ordenServicio->id)->get();
        $ordenServicio->listaServicios = Cotizacion::obtenerCotizacionesAprobadas($ordenServicio->id_cliente,true);
        $ordenServicio->nombreCliente = $ordenServicio->cliente->nombreCliente;
        return response()->json(['ordenServicio' => $ordenServicio]);
    }
    public function agregarOs(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOSAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        DB::beginTransaction();
        try {
            // dd($request->all());
            if(!$request->has("listaServicios")) {
                return response()->json(['alerta' => 'Para generar una orden de servicio se debe tener al menos un servicio'],400);
            }
            $ordenServicioDatos = $request->only("id_cliente","fecha");
            $ordenServicioDatos['estado'] = 1;
            $ordenServicio = ModelsOrdenServicio::create($ordenServicioDatos);
            $listaServicios = json_decode($request->listaServicios,true);
            $listaCotizaciones =array_unique(array_column($listaServicios,'idCotizacion'));
            $cotizaciones = Cotizacion::whereIn('id',$listaCotizaciones);
            //Estado 3 se esta empezando a poner los servicios con sus respectivas ordenes
            $cotizaciones->update(['estado' => 3]);
            $calculosTotales = [
                'importe' => 0,
                'descuento' => 0,
                'igv' => 0,
                'adicional' => 0,
                'total' => 0
            ];
            foreach ($listaServicios as $servicio) {
                $calculosTotales['importe'] += $servicio['total'];
                $calculosTotales['descuento'] += $servicio['descuento'];
                $calculosTotales['igv'] += $servicio['igv'];
                OrdenServicioCotizacion::create([
                    'id_orden_servicio' => $ordenServicio->id,
                    'id_cotizacion_servicio' => $servicio['idCotizacionServicio'],
                    'estado' => 1
                ]);
                //El servicio ya paso con su orden
                CotizacionServicio::find($servicio['idCotizacionServicio'])->update(['estado' => 2]);
            }
            foreach ($cotizaciones->get() as $cotizacion) {
                //verificamos si todos los servicios relacionados a la cotizacion se han asignado a sus ordenes
                if($cotizacion->cotizacionSerivicios()->count() == $cotizacion->cotizacionSerivicios()->where('estado',2)->count()){
                    $cotizacion->update(['estado' => 4]);
                }
            }
            if($request->has('descripcion')){
                for ($i=0; $i < count($request->descripcion); $i++) { 
                    $cantidad = isset($request->cantidad[$i]) && !empty($request->cantidad[$i]) ? $request->cantidad[$i] : 1;
                    $precio = isset($request->precio[$i]) && !empty($request->precio[$i]) ? $request->precio[$i] : 0;
                    $total = $cantidad * $precio;
                    $calculosTotales['adicional'] += $total;
                    OrdenServicioAdicional::create([
                        'id_orden_servicio' => $ordenServicio->id,
                        'descripcion' => $request->descripcion[$i],
                        'cantidad' => $cantidad,
                        'precio' => $precio,
                        'total' => $total,
                        'estado' => 1
                    ]);
                }
            }
            $calculosTotales['total'] = $calculosTotales['importe'] - $calculosTotales['descuento'] + $calculosTotales['adicional'];
            $ordenServicio->update($calculosTotales);
            DB::commit();
            return response()->json(['success' => 'Orden de servicio generada de manera correcta']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()],400);
        }
    }
}
