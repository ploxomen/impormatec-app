<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Configuracion;
use App\Models\Cotizacion;
use App\Models\CotizacionProductos;
use App\Models\CotizacionServicio;
use App\Models\EntregaActa;
use App\Models\OrdenServicio as ModelsOrdenServicio;
use App\Models\OrdenServicioAdicional;
use App\Models\OrdenServicioCotizacionProducto;
use App\Models\OrdenServicioCotizacionServicio;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;

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
    public function obtenerCotizacionCliente($cliente,Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOSAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(['detalleCotizacion' => Cotizacion::obtenerCotizacionesAprobadas($cliente,$request->tipoMoneda)]);
    }
    public function indexMisOs()  {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $firmasUsuarios = User::firmasHabilitadas();
        return view("ordenesServicio.misOrdenes",compact("modulos","clientes","firmasUsuarios"));
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
                    $cantidadTotalServicios = OrdenServicioCotizacionServicio::where(['id_orden_servicio' => $request->ordenServicioId])->count();
                    $cantidadTotalProductos = OrdenServicioCotizacionProducto::where(['id_orden_servicio' => $request->ordenServicioId])->count();
                    if(($cantidadTotalServicios + $cantidadTotalProductos) === 1){
                        return response()->json(['alerta' => 'La orden de servicio debe contener al menos un item en la cotización']);
                    }
                    if($request->tipoDetalle === "servicio"){
                        OrdenServicioCotizacionServicio::where(['id_orden_servicio' => $request->ordenServicioId,'id_cotizacion_servicio' => $request->cotizacionServicioId])->delete();
                        $cotizacionServicio = CotizacionServicio::find($request->cotizacionServicioId);
                        $cotizacionServicio->update(['estado' => 1]);
                    }else{
                        OrdenServicioCotizacionProducto::where(['id_orden_servicio' => $request->ordenServicioId,'id_cotizacion_producto' => $request->cotizacionServicioId])->delete();
                        $cotizacionServicio = CotizacionProductos::find($request->cotizacionServicioId);
                        $cotizacionServicio->update(['estado' => 1]);
                    }
                    $cotizacion = Cotizacion::find($cotizacionServicio->id_cotizacion);
                    $cotizacion->update(['estado' => 3]);
                    $cantidadTotalServicios = $cotizacion->cotizacionSerivicios()->count();
                    $cantidadTotalProductos = $cotizacion->cotizacionProductos()->count();
                    $cantidadServiosOs = $cotizacion->cotizacionSerivicios()->where('estado',1)->count();
                    $cantidadProductosOs = $cotizacion->cotizacionProductos()->where('estado',1)->count();
                    if(($cantidadTotalServicios + $cantidadTotalProductos) === ($cantidadServiosOs + $cantidadProductosOs)){
                        $cotizacion->update(['estado' => 2]);
                    }
                    $this->actualizarMontosOrdenServicio($request->ordenServicioId); 
                    DB::commit();
                    return response()->json(['success' => 'Item eliminado correctamente']);
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
                    $servicios = CotizacionServicio::mostrarServiciosConProductos($cotizacion->id);
                    $detallesCotizacion = CotizacionProductos::productosServicios($servicios,$cotizacion->id)->where('estado',1);
                    foreach ($detallesCotizacion as $key => $servicio) {
                        if($servicio->estado > 1){
                            continue;
                        }
                        if($servicio->tipo === "servicio"){
                            $datos = OrdenServicioCotizacionServicio::updateOrCreate([
                                'id_orden_servicio' => $request->idOrdenServicio,
                                'id_cotizacion_servicio' => $servicio->id,
                                'orden' => $key + 1
                            ],['estado' => 1]);
                            $nombreDetalle = $servicio->servicios->servicio;
                            CotizacionServicio::find($servicio->id)->update(['estado' => 2]);
                        }else{
                            $datos = OrdenServicioCotizacionProducto::updateOrCreate([
                                'id_orden_servicio' => $request->idOrdenServicio,
                                'id_cotizacion_producto' => $servicio->id,
                                'orden' => $key + 1
                            ],['estado' => 1]);
                            $nombreDetalle = $servicio->productos->nombreProducto;
                            CotizacionProductos::find($servicio->id)->update(['estado' => 2]);
                        }
                        $respuesta[] = [
                            'cantidad' => $servicio->cantidad,
                            'descuento' => $servicio->descuento,
                            'idCotizacionServicio' => $servicio->id,
                            'idOsCotizacion' => $datos->id,
                            'importe' => $servicio->importe,
                            'tipoServicioProducto' => $servicio->tipo,
                            'nroCotizacion' => str_pad($servicio->id_cotizacion,5,'0',STR_PAD_LEFT),
                            'servicio' => $nombreDetalle,
                            'total' => $servicio->total
                        ];
                    }
                    $this->actualizarMontosOrdenServicio($request->idOrdenServicio); 
                    DB::commit();
                    return response()->json(['success' => 'Los servicios y/o productos de la cotización se agregaron correctamente','listaServicios' => $respuesta]);
                }catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(['alerta' => $th->getMessage()]);
                }
            break;
            case 'actualizar-orden':
                DB::beginTransaction();
                try {
                    ModelsOrdenServicio::find($request->ordenServicioId)->update(['fecha' => $request->fecha,'observaciones' => $request->observaciones]);
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
        $cotizaciones = OrdenServicioCotizacionServicio::select("id_cotizacion_servicio")->where('id_orden_servicio',$idOrdenServicio)->get()->toArray();
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
    public function reporteEntregaActa(EntregaActa $entregaActa) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $utilitarios = new Utilitarios();
        $tituloPdf = "ENTREGA ACTAS - " . str_pad($entregaActa->id,5,'0',STR_PAD_LEFT);
        $configuracion = Configuracion::whereIn('descripcion',['direccion','razon_social_largo','ruc','razon_social'])->get();
        $diaFecha = $utilitarios->obtenerFechaLargaSinDia(strtotime($entregaActa->fecha_entrega));
        return Pdf::loadView('ordenesServicio.reportes.entregaActa',compact("tituloPdf","configuracion","entregaActa","diaFecha"))->stream($tituloPdf.".pdf");
    }
    public function obtenerDatosActa(ModelsOrdenServicio $ordenServicio) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $entregaActa = EntregaActa::select("id","id_responsable_firmante","nombre_representante","dni_representante","firma_representante","fecha_entrega")->where('id_orden_servicio',$ordenServicio->id)->first();
        if(empty($entregaActa)){
            $entregaActa = EntregaActa::create(['id_orden_servicio' => $ordenServicio->id,'estado' => 1]);
        }
        return response()->json(['actas' => $entregaActa]);
    }
    public function guardarDatosActa(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $datos = [
            'id_responsable_firmante' => $request->usuario_entrega,
            'nombre_representante' => $request->nombre_representante,
            'dni_representante' => $request->dni_representante,
            'fecha_entrega' => $request->fecha_entrega_acta,
            'estado' => 1
        ];
        DB::beginTransaction();
        try {
            if($request->has('idEntregaActa')){
                $entregaActaModel = EntregaActa::find($request->idEntregaActa);
                if(!empty($entregaActaModel->firma_representante) && Storage::exists('/firmaEntregaActas/'.$entregaActaModel->firma_representante)){
                    Storage::delete('/firmaEntregaActas/'.$entregaActaModel->firma_representante);
                }
                if(!empty($entregaActaModel->firma_representante_cortado) && Storage::exists('/firmaEntregaActas/'.$entregaActaModel->firma_representante_cortado)){
                    Storage::delete('/firmaEntregaActas/'.$entregaActaModel->firma_representante_cortado);
                }
            }
            $data_uri = $request->imagenFirmaRepresentante;
            $encoded_image = explode(",", $data_uri)[1];
            $nombreArchivo = 'firma_' . time() . '.png';
            $decoded_image = base64_decode($encoded_image);
            file_put_contents(storage_path('/app/firmaEntregaActas/'.$nombreArchivo), $decoded_image);
            $rutaOriginal = storage_path('app/firmaEntregaActas/'.$nombreArchivo);
            //Proceso para cortar una imagen
            $imagen = Image::make($rutaOriginal);
            $imagenRecortada = $imagen->trim('transparent',['top', 'bottom','left','right'],0,15);
            $nombreArchivoCortado = 'firma_cortado_' . (time() + 1) . '.png';
            $rutaRecortada = storage_path('/app/firmaEntregaActas/'.$nombreArchivoCortado);
            $imagenRecortada->save($rutaRecortada);
            $datos['firma_representante'] = $nombreArchivo;
            $datos['firma_representante_cortado'] = $nombreArchivoCortado;
            if(!$request->has('idEntregaActa')){
                $datos['id_orden_servicio'] = $request->ordenServicio;
                EntregaActa::create($datos);
            }else{
                EntregaActa::find($request->idEntregaActa)->update($datos);
            }
            DB::commit();
            return response()->json(['success' => 'datos guardados correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(),'line' => $th->getLine()]);
        }
    }
    public function reporteOrdenServicio(ModelsOrdenServicio $ordenServicio) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $utilitarios = new Utilitarios();
        $moneda = $ordenServicio->tipoMoneda === "USD" ? '$' : 'S/';
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $nombreDia = $utilitarios->obtenerFechaLarga(strtotime($ordenServicio->fecha));
        $cliente = Clientes::find($ordenServicio->id_cliente);
        $codigoOrdenServicio = str_pad($ordenServicio->id,5,'0',STR_PAD_LEFT);
        $serviciosOS = OrdenServicioCotizacionServicio::mostrarServiciosOrdenServicio($ordenServicio->id);
        $ordenServicioDetalle = OrdenServicioCotizacionProducto::mostrarProductosOrdenServicio($serviciosOS,$ordenServicio->id);
        return  Pdf::loadView('ordenesServicio.reportes.ordenServicio',compact("moneda","configuracion","ordenServicio","cliente","nombreDia","codigoOrdenServicio","ordenServicioDetalle"))->stream("orden_servicio_".$codigoOrdenServicio.".pdf");
    }
    public function obtenerDatosOrdenServicio(ModelsOrdenServicio $ordenServicio){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $serviciosOS = OrdenServicioCotizacionServicio::mostrarServiciosOrdenServicio($ordenServicio->id);
        $ordenServicio->cotizaciones = OrdenServicioCotizacionProducto::mostrarProductosOrdenServicio($serviciosOS,$ordenServicio->id);
        $ordenServicio->adicionales = OrdenServicioAdicional::select("id AS idAdicional","descripcion","precio AS precioUnitario","cantidad","total")->where('id_orden_servicio',$ordenServicio->id)->get();
        $ordenServicio->listaServicios = Cotizacion::obtenerCotizacionesAprobadas($ordenServicio->id_cliente,$ordenServicio->tipoMoneda,true);
        $ordenServicio->nombreCliente = $ordenServicio->cliente->nombreCliente;
        return response()->json(['ordenServicio' => $ordenServicio->makeHidden("cliente","fechaActualizada","fechaCreada")]);
    }
    public function agregarOs(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOSAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        DB::beginTransaction();
        try {
            if(!$request->has("listaDetalleCotizacion")) {
                return response()->json(['alerta' => 'Para generar una orden de servicio se debe tener al menos un item en su detalle'],400);
            }
            $ordenServicioDatos = $request->only("id_cliente","fecha","tipoMoneda","observaciones");
            $ordenServicioDatos['estado'] = 1;
            $ordenServicio = ModelsOrdenServicio::create($ordenServicioDatos);
            $listaServiciosProductos = json_decode($request->listaDetalleCotizacion,true);
            $listaCotizaciones =array_unique(array_column($listaServiciosProductos,'idCotizacion'));
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
            foreach ($listaServiciosProductos as $key => $servicio) {
                $calculosTotales['importe'] += $servicio['total'];
                $calculosTotales['descuento'] += $servicio['descuento'];
                $calculosTotales['igv'] += $servicio['total'] * 0.18;
                if($servicio['tipoServicioProducto'] === "servicio"){
                    OrdenServicioCotizacionServicio::create([
                        'id_orden_servicio' => $ordenServicio->id,
                        'id_cotizacion_servicio' => $servicio['idCotizacionServicio'],
                        'orden' => $key + 1,
                        'estado' => 1
                    ]);
                    CotizacionServicio::find($servicio['idCotizacionServicio'])->update(['estado' => 2]);
                    continue;
                }
                OrdenServicioCotizacionProducto::create([
                    'id_orden_servicio' => $ordenServicio->id,
                    'id_cotizacion_producto' => $servicio['idCotizacionServicio'],
                    'orden' => $key + 1,
                    'estado' => 1
                ]);
                CotizacionProductos::find($servicio['idCotizacionServicio'])->update(['estado' => 2]);
            }
            foreach ($cotizaciones->get() as $cotizacion) {
                //verificamos si todos los servicios y productos relacionados a la cotizacion se han asignado a sus ordenes
                $cantidadTotalServicios = $cotizacion->cotizacionSerivicios()->count();
                $cantidadTotalProductos = $cotizacion->cotizacionProductos()->count();
                $cantidadServiosOs = $cotizacion->cotizacionSerivicios()->where('estado',2)->count();
                $cantidadProductosOs = $cotizacion->cotizacionProductos()->where('estado',2)->count();
                if(($cantidadTotalServicios + $cantidadTotalProductos) === ($cantidadServiosOs + $cantidadProductosOs)){
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
            return response()->json(['error' => $th->getMessage(),'line' => $th->getLine()],400);
        }
    }
}
