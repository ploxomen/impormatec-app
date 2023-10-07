<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Configuracion;
use App\Models\InformeServicioSecciones;
use App\Models\InformeServicioSeccionesImg;
use App\Models\OrdenServicio;
use App\Models\OrdenServicioCotizacionServicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Informes extends Controller
{
    private $usuarioController;
    private $moduloMisOs = "admin.ordenesServicios.index";
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
                        $servicio->update(['objetivos' => $data->objetivos]);
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
        return view("ordenesServicio.generarReporte",compact("modulos","clientes","ordenServicio","idCliente","idOs","listaOs"));
    }
    public function reportePrevioInforme($idOrdenServicio,$idServicio = null) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisOs);
        if(isset($verif['session']) && isset($verif2['session'])){
            return redirect()->route("home"); 
        }
        $ordenServicioDetalle = OrdenServicio::findOrFail($idOrdenServicio);
        $nroOrdenServicio = str_pad($ordenServicioDetalle->id,5,'0',STR_PAD_LEFT);
        $utilitarios = new Utilitarios();
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        if(empty($idServicio)){
            $contenidoInformes = "";
            $tituloPdf = "INFORME GENERAL - OS " .  $nroOrdenServicio;
            foreach ($ordenServicioDetalle->servicios as $servicio) {
                $ordenServicio = $servicio;
                $fechaTime = 0;
                $fechaTerminoLargo = 'No se establecio la fecha de termino';
                $fechaNormal = "";
                if(!empty($servicio->fecha_termino)){
                    $fechaTime = strtotime($servicio->fecha_termino);
                    $fechaTerminoLargo = $utilitarios->obtenerFechaLarga($fechaTime);
                    $fechaNormal = date('d/m/Y',$fechaTime);
                }
                $nroOrdenServicio = str_pad($servicio->id_orden_servicio,5,'0',STR_PAD_LEFT);
                $nroInforme = str_pad($servicio->id,5,'0',STR_PAD_LEFT);
                $contenidoInformes .= view('ordenesServicio.reportes.informe',compact("ordenServicio","nroOrdenServicio","fechaTerminoLargo","fechaNormal","tituloPdf","configuracion","nroInforme","ordenServicioDetalle"));
            }
            $dompdf = Pdf::loadHtml($contenidoInformes);
            $dompdf->render();
            $output = $dompdf->output();
            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename=INFORME_GENERAL_OS_'.$nroOrdenServicio.'.pdf',
            ]);
        }
        $ordenServicio = OrdenServicioCotizacionServicio::where(['id' => $idServicio, 'id_orden_servicio' => $idOrdenServicio])->firstOrFail();
        $fechaTime = 0;
        $fechaTerminoLargo = 'No se establecio la fecha de termino';
        $fechaNormal = "";
        if(!empty($ordenServicio->fecha_termino)){
            $fechaTime = strtotime($ordenServicio->fecha_termino);
            $fechaTerminoLargo = $utilitarios->obtenerFechaLarga($fechaTime);
            $fechaNormal = date('d/m/Y',$fechaTime);
        }
        $nroInforme = str_pad($ordenServicio->id,5,'0',STR_PAD_LEFT);
        $tituloPdf = "INFORME DEL SERVICIO - " .  $nroInforme;
        return Pdf::loadView('ordenesServicio.reportes.informe',compact("ordenServicio","tituloPdf","nroOrdenServicio","fechaTerminoLargo","fechaNormal","configuracion","nroInforme","ordenServicioDetalle"))->stream("INF_".$nroInforme.'.pdf');
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
        $columnas = ['objetivos','acciones','descripcion','conclusiones_recomendaciones'];
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
            'columnas' => $request->columnas,
            'estado' => 1
        ]);
        return response()->json(['success' => 'seccion agregada correctamente', 'titulo' => $informeSeccion->titulo, 'columna' => $informeSeccion->columnas, 'idSeccion' => $informeSeccion->id,'idOs' => $ordenServicio->id, 'idServicio' => $osServicio->id, 'listaImagenes' => []]);
    }
    public function generarInforme(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){        
            return response()->json(['session' => true]);
        }
        $ordenServicio = OrdenServicio::where(['id' => $request->os,'estado' => 1])->first();
        if(empty($ordenServicio)){
            return response()->json(['alerta' => 'No se puede actualizar la orden de servicio debido a que no exite o se haya actualizado de estado']);
        }
        $ordenServicio->update(['estado' => 2,'usuario_informe' => Auth::id()]);
        return response()->json(['success' => 'Informe generado correctamente']);
    }
    public function eliminarImagenEnLaSeccion(Request $request) {
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
            return response()->json(['alerta' => 'No se encontró la seccion para registrar la imagen']);
        }
        $imagen = InformeServicioSeccionesImg::where(['id_informe_os_secciones' => $seccion->id,'id' => $request->img])->first();
        if(!empty($imagen) && Storage::disk('informeImgSeccion')->exists($imagen->url_imagen)){
            Storage::disk('informeImgSeccion')->delete($imagen->url_imagen);
        }
        $imagen->delete();
        return response()->json(['success' => 'imagen eliminada correctamente']);

    }
    public function editarInformeGenerado(OrdenServicio $ordenServicio){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisOs);
        if(isset($verif['session'])){        
            return redirect()->route("home"); 
        }
        if($ordenServicio->estado < 2){
            return abort(404,'No se encontró el informe');
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("ordenesServicio.editarReporte",compact("modulos","ordenServicio"));

    }
    public function actualizarDatos(Request $request) {
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
        if(!$request->has("seccion") && !$request->has("imagen")){
            $osServicio->update(['fecha_termino' => $request->valor]);
            return response()->json(['success' => 'fecha actualizada correctamente']);
        }
        $seccion = InformeServicioSecciones::where([
            'id_os_servicio' => $osServicio->id,
            'id' => $request->seccion,
        ])->first();
        if(empty($seccion)){
            return response()->json(['alerta' => 'No se encontró la seccion para registrar la imagen']);
        }
        InformeServicioSeccionesImg::where(['id_informe_os_secciones' => $seccion->id,'id' => $request->imagen])->update(['descripcion' => $request->valor]);
        return response()->json(['success' => 'descripcion actualizada correctamente']);
    }
    public function agregarImagenEnLaSeccion(Request $request) {
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
            return response()->json(['alerta' => 'No se encontró la seccion para registrar la imagen']);
        }
        $nombreArchivo = null;
        DB::beginTransaction();
        try {
            $img = InformeServicioSeccionesImg::create([
                'id_informe_os_secciones' => $seccion->id,
                'estado' => 1
            ]);
            $extension = $request->file('imagen')->getClientOriginalExtension();
            $nombreArchivo = $seccion->id . '_' . $img->id . '_' .time() . '.' . $extension;
            $request->file('imagen')->storeAs('informeImgSeccion',$nombreArchivo);
            $img->update(['url_imagen' => $nombreArchivo]);
            DB::commit();
            return response()->json(['success' => 'imagen agregada correctamente', 'idSeccion' => $seccion->id,'idOs' => $ordenServicio->id, 'idServicio' => $osServicio->id, 'urlImagen' => route("urlImagen",["informeImgSeccion",$nombreArchivo]), 'idImagen' => $img->id, 'descripcion' => ""]);
        } catch (\Throwable $th) {
            DB::rollBack();
            if(!is_null($nombreArchivo) && Storage::disk('informeImgSeccion')->exists($nombreArchivo)){
                Storage::disk('informeImgSeccion')->delete($nombreArchivo);
            }
            return response()->json(['alerta' => $th->getMessage()]);
        }
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
        foreach (InformeServicioSeccionesImg::where('id_informe_os_secciones',$seccion->id)->get() as $key => $imagen) {
            if(Storage::disk('informeImgSeccion')->exists($imagen->url_imagen)){
                Storage::disk('informeImgSeccion')->delete($imagen->url_imagen);
            }
            $imagen->delete();
        }
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
