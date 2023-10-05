<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\InformeServicioSecciones;
use App\Models\InformeServicioSeccionesImg;
use App\Models\OrdenServicio;
use App\Models\OrdenServicioCotizacionServicio;
use Illuminate\Http\Request;

class Informes extends Controller
{
    private $usuarioController;
    private $moduloMisInformes = "admin.informe.lista";
    private $moduloGenerarInforme = "informe.generar";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function aprobarCotizacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $ordenServicio = null;
        list($idCliente,$idOs) = [$request->cliente,$request->ordenServicio];
        $listaOs = [];
        if($request->has("cliente") && $request->has("ordenServicio")){
            $ordenServicio = OrdenServicio::where(['id' => $request->ordenServicio, 'id_cliente' => $request->cliente, 'estado' => 1])->first();
            if(!empty($ordenServicio)){
                $listaOs = OrdenServicio::ordenServiciosCliente($idCliente);
                foreach ($ordenServicio->servicios as $servicio) {
                    $data = is_null($servicio->objetivos) || is_null($servicio->acciones) || is_null($servicio->descripcion) ? OrdenServicioCotizacionServicio::obtenerServicio($ordenServicio->id,$servicio->id) : null;
                    if(is_null($servicio->objetivos) && !is_null($data)){
                        $servicio->update(['objetivos' => $data->servicio]);
                    }
                    if(is_null($servicio->acciones) && !is_null($data)){
                        $servicio->update(['acciones' => $data->acciones]);
                    }
                    if(is_null($servicio->descripcion) && !is_null($data)){
                        $servicio->update(['descripcion' => $data->descripcion]);
                    }
                }
            }
        }
        // dd($ordenServicio);
        return view("ordenesServicio.generarReporte",compact("modulos","clientes","ordenServicio","idCliente","idOs","listaOs"));
    }
    public function actualizarServiciosDescripciones(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){        
            return response()->json(['session' => true]);
        }
        $ordenServicio = $this->verificarDisponibilidadOs($request->os,false);
        if(isset($ordenServicio['alerta'])){
            return response()->json($ordenServicio);
        }
        $columnas = ['objetivos','acciones','descripcion'];
        $busqueda = array_search($request->columna,$columnas);
        if($busqueda === false){
            return response()->json(['alerta' => 'No se encontró la columna ' . $request->columna .' para editar el informe']);
        }
        OrdenServicioCotizacionServicio::where(['id_orden_servicio' => $ordenServicio->id, 'id' => $request->servicio])->update([$columnas[$busqueda] => $request->texto]);
        return response()->json(['success' => 'datos actualizados correctamente']);
    }
    public function verificarDisponibilidadOs($idOs,$esAdministrador){
        $ordenServicio = OrdenServicio::find($idOs);
        if(is_null($ordenServicio)){
            return ['alerta' => 'No se encontró la orden de servicio, por favor vuelva a intentarlo más tarde'];
        }
        if(!$esAdministrador && $ordenServicio->estado > 10){
            return ['alerta' => 'Ya se generó el informe para esta orden de servicio, por favor actualize la página'];
        }
        return $ordenServicio;
    }
    public function obtenerInformacionSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){        
            return response()->json(['session' => true]);
        }
        $ordenServicio = $this->verificarDisponibilidadOs($request->os,false);
        if(isset($ordenServicio['alerta'])){
            return response()->json($ordenServicio);
        }
        $osServicio = OrdenServicioCotizacionServicio::where(['id_orden_servicio' => $ordenServicio->id, 'id' => $request->servicio])->first();
        if(empty($osServicio)){
            return response()->json(['alerta' => 'No se encontró el servicio para esta orden de servicio']);
        }
        $seccion = InformeServicioSecciones::where(['id_os_servicio' => $osServicio->id, 'id' => $request->seccion])->select("titulo","columnas","id")->first();
        if(empty($seccion)){
            return response()->json(['alerta' => 'No se encontró la sección para este servicio']);
        }
        return response()->json(['success' => $seccion]);
    }
    public function agregarNuevaSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){        
            return response()->json(['session' => true]);
        }
        $ordenServicio = $this->verificarDisponibilidadOs($request->os,false);
        if(isset($ordenServicio['alerta'])){
            return response()->json($ordenServicio);
        }
        $osServicio = OrdenServicioCotizacionServicio::where(['id_orden_servicio' => $ordenServicio->id, 'id' => $request->servicio])->first();
        if(empty($osServicio)){
            return response()->json(['alerta' => 'No se encontró el servicio para esta orden de servicio']);
        }
        $informeSeccion = InformeServicioSecciones::create([
            'id_os_servicio' => $osServicio->id,
            'titulo' => $request->titulo,
            'columnas' => $request->columnas
        ]);
        return response()->json(['success' => 'seccion agregada correctamente', 'titulo' => $informeSeccion->titulo, 'columna' => $informeSeccion->columnas, 'idSeccion' => $informeSeccion->id,'idOs' => $ordenServicio->id, 'idServicio' => $osServicio->id, 'listaImagenes' => []]);
    }
    public function eliminarSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){        
            return response()->json(['session' => true]);
        }
        $ordenServicio = $this->verificarDisponibilidadOs($request->os,false);
        if(isset($ordenServicio['alerta'])){
            return response()->json($ordenServicio);
        }
        $osServicio = OrdenServicioCotizacionServicio::where(['id_orden_servicio' => $ordenServicio->id, 'id' => $request->servicio])->first();
        if(empty($osServicio)){
            return response()->json(['alerta' => 'No se encontró el servicio para esta orden de servicio']);
        }
        $seccion = InformeServicioSecciones::where([
            'id_os_servicio' => $osServicio->id,
            'id' => $request->seccion,
        ])->first();
        if(empty($seccion)){
            return response()->json(['alerta' => 'No se encontró la seccion para ser eliminada']);
        }
        InformeServicioSeccionesImg::where('id_informe_os_secciones',$seccion->id)->delete();
        $seccion->delete();
        return response()->json(['success' => 'seccion eliminada correctamente']);
    }
    public function editarSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){        
            return response()->json(['session' => true]);
        }
        $ordenServicio = $this->verificarDisponibilidadOs($request->os,false);
        if(isset($ordenServicio['alerta'])){
            return response()->json($ordenServicio);
        }
        $osServicio = OrdenServicioCotizacionServicio::where(['id_orden_servicio' => $ordenServicio->id, 'id' => $request->servicio])->first();
        if(empty($osServicio)){
            return response()->json(['alerta' => 'No se encontró el servicio para esta orden de servicio']);
        }
        InformeServicioSecciones::where([
            'id_os_servicio' => $osServicio->id,
            'id' => $request->seccion,
        ])->update([
            'titulo' => $request->titulo,
            'columnas' => $request->columnas
        ]);
        return response()->json(['success' => 'seccion actualizada correctamente', 'titulo' => $request->titulo, 'columna' => $request->columnas, 'idSeccion' =>$request->seccion, 'idServicio' => $osServicio->id]);
    }
    public function obtenerOrdenesServicioCliente(Request $request,$idCliente) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $ordenesServicios = OrdenServicio::ordenServiciosCliente($idCliente);
        return response()->json(['ordenesServicio' => $ordenesServicios]);
    }
}
