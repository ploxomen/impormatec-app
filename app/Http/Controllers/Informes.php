<?php

namespace App\Http\Controllers;

use App\Models\CertificadosServicios;
use App\Models\Clientes;
use App\Models\Configuracion;
use App\Models\InformeServicioSecciones;
use App\Models\InformeServicioSeccionesImg;
use App\Models\OrdenServicio;
use App\Models\OrdenServicioCotizacionServicio;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class Informes extends Controller
{
    private $usuarioController;
    private $moduloGenerarInforme = "informe.generar";
    private $moduloMisInformes = "informe.generar";

    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexGenerarInforme(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("ordenesServicio.misInformes",compact("modulos"));
    }
    public function listarInformes() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $listaDeInformes = OrdenServicioCotizacionServicio::obtenerInformesGenerados();
        return DataTables::of($listaDeInformes)->toJson();
    }
    public function visualizarInforme(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        list($ordenServicio,$firmasUsuarios) = [null,[]];
        list($idCliente,$idOs) = [$request->cliente,$request->ordenServicio];
        $listaOs = [];
        if($request->has("cliente") && $request->has("ordenServicio")){
            $ordenServicio = OrdenServicio::where(['id' => $request->ordenServicio, 'id_cliente' => $request->cliente, 'estado' => 1])->first();
            if(!empty($ordenServicio)){
                $listaOs = OrdenServicio::ordenServiciosCliente($idCliente);
                $firmasUsuarios = User::firmasHabilitadas();
                foreach ($ordenServicio->servicios as $servicio) {
                    $data = is_null($servicio->objetivos) || is_null($servicio->acciones) || is_null($servicio->descripcion) ? OrdenServicioCotizacionServicio::obtenerServicio($ordenServicio->id,$servicio->id) : null;
                    if(is_null($servicio->objetivos) && !is_null($data)){
                        $servicio->update(['objetivos' => '<h2 style="font-size:14px;">1. Objetivos</h2>' . trim($data->objetivos)]);
                    }
                    if(is_null($servicio->acciones) && !is_null($data)){
                        $servicio->update(['acciones' => '<h2 style="font-size:14px;">2. Actuaciones realizadas</h2>' . trim($data->acciones)]);
                    }
                    if(is_null($servicio->descripcion) && !is_null($data)){
                        $servicio->update(['descripcion' => '<h2 style="font-size:14px;">3. Descripción clara y precisa de la forma técnica e instrumentos utilizados</h2>' . trim($data->descripcion)]);
                    }
                    if(is_null($servicio->conclusiones_recomendaciones)){
                        $servicio->update(['conclusiones_recomendaciones' => '<h2 style="font-size:14px;">5. Conclusiones y Recomendaciones</h2>']);
                    }
                }
            }
        }
        return view("ordenesServicio.generarReporte",compact("modulos","clientes","ordenServicio","idCliente","idOs","listaOs","firmasUsuarios"));
    }
    public function actualizarCertificado(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $datos = $request->except('certificado');
        $datos['usuario_generado'] = Auth::id();
        CertificadosServicios::find($request->certificado)->update($datos);
        return response()->json(['success' => 'Datos del certificado guardados correctamente']);
    }
    public function certificadoInforme(OrdenServicioCotizacionServicio $OsCotizacionServicio) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session']) && isset($verif2['session'])){
            return redirect()->route("home");
        }
        $cetificadoOperativo = CertificadosServicios::where('id_os_cotizacion_servicio',$OsCotizacionServicio->id);
        if(!$cetificadoOperativo->count()){
            $cetificadoOperativo = CertificadosServicios::create(['estado' => 1,'id_os_cotizacion_servicio' => $OsCotizacionServicio->id]);
            $OsCotizacionServicio->update(['estado' => 3]);
        }else{
            $cetificadoOperativo = $cetificadoOperativo->first();
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view('ordenesServicio.generarCertificado',compact("cetificadoOperativo","modulos"));
    }
    public function visualizarCertificado(OrdenServicioCotizacionServicio $OsCotizacionServicio) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session']) && isset($verif2['session'])){
            return redirect()->route("home");
        }
        $utilitarios = new Utilitarios();
        $configuracion = Configuracion::whereIn('descripcion',['direccion','razon_social_largo','telefono','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter','ruc'])->get();
        $certificado = $OsCotizacionServicio->certificado;
        $certificado->fechaLarga = $utilitarios->obtenerFechaLargaSinDia(strtotime($certificado->fecha));
        $cliente = $certificado->ordenServicioCotizacion->cotizacionServicio->cotizacion->cliente;
        $direccionCliente = $certificado->ordenServicioCotizacion->cotizacionServicio->cotizacion->direccionCliente;
        $tituloPdf = 'CERTIFICADO DE OPERATIVIDAD '. str_pad($certificado->id,5,"0",STR_PAD_LEFT) .'.pdf';
        return Pdf::loadView('ordenesServicio.reportes.certificado',compact("cliente","tituloPdf","configuracion","certificado","direccionCliente"))->stream($tituloPdf);
    }
    public function reportePrevioInforme($idOrdenServicio,$idServicio = null) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session']) && isset($verif2['session'])){
            return redirect()->route("home"); 
        }
        $ordenServicioDetalle = OrdenServicio::findOrFail($idOrdenServicio);
        $nroOrdenServicio = str_pad($ordenServicioDetalle->id,5,'0',STR_PAD_LEFT);
        $utilitarios = new Utilitarios();
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $ordenServicio = !empty($idServicio) ? $ordenServicioDetalle->servicios()->where('id',$idServicio)->get() : $ordenServicioDetalle->servicios()->get();
        if($ordenServicio->isEmpty()){
            return abort(404,'No se encontro el informe');
        }
        $tituloPdf = empty($idServicio) ? "INFORME GENERAL - OS " .  $nroOrdenServicio : "INFORME DEL SERVICIO - " .  str_pad($ordenServicio->first()->id,5,'0',STR_PAD_LEFT);
        return Pdf::loadView('ordenesServicio.reportes.informe',compact("utilitarios","ordenServicio","tituloPdf","nroOrdenServicio","configuracion","ordenServicioDetalle"))->stream($tituloPdf);
    }
    public function actualizarServiciosDescripciones(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session']) && isset($verif2['session'])){        
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
        $informacion = OrdenServicioCotizacionServicio::where(['id_orden_servicio' => $ordenServicio->id, 'id' => $request->servicio])->first();
        if(empty($informacion)){
            return response()->json(['alerta' => 'No se encontró la columna el servicio para ser actualizado']);
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($request->texto);
        $imagenes = $dom->getElementsByTagName('img');
        $domDB = new DOMDocument();
        @$domDB->loadHTML(empty($informacion->{$columnas[$busqueda]}) ? '<div></div>' : $informacion->{$columnas[$busqueda]});
        $imagenesDB = $domDB->getElementsByTagName('img');
        foreach ($imagenesDB as $imagenDB) {
            $srcDB = $imagenDB->getAttribute('src');
            $srcPathDB = pathinfo($srcDB);
            $nombreArchivoDB = $srcPathDB['basename'];
            $encontrado = false;
            foreach ($imagenes as $imagen) {
                $src = $imagen->getAttribute('src');
                $srcPath = pathinfo($src);
                $nombreArchivo = $srcPath['basename'];
                if($nombreArchivo === $nombreArchivoDB){
                    $encontrado = true;
                    break;
                }
            }
            if(!$encontrado){
                $rutaCarpetaPublica = public_path('imagenesEditor');
                $rutaArchivo = $rutaCarpetaPublica . '/' . $nombreArchivoDB;
                if (File::exists($rutaArchivo)) {
                    File::delete($rutaArchivo);
                }
            }
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
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session']) && isset($verif2['session'])){
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
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session']) && isset($verif2['session'])){
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
        OrdenServicioCotizacionServicio::where(['id_orden_servicio' => $ordenServicio->id, 'estado' => 1])->update(['estado' => 2,'responsable_usuario' => Auth::id()]);
        $ordenServicio->update(['estado' => 2]);
        return response()->json(['success' => 'Informe generado correctamente']);
    }
    public function eliminarImagenEnLaSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
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
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session'])){        
            return redirect()->route("home"); 
        }
        if($ordenServicio->estado < 2){
            return abort(404,'No se encontró el informe');
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $firmasUsuarios = User::firmasHabilitadas();
        return view("ordenesServicio.editarReporte",compact("modulos","ordenServicio","firmasUsuarios"));
    }
    public function actualizarDatos(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloGenerarInforme);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisInformes);
        if(isset($verif['session']) && isset($verif2['session'])){
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
            if($request->has('firma')){
                $osServicio->update(['id_firma_profesional' => $request->valor]);
                return response()->json(['success' => 'firma actualizada correctamente']);
            }
            $nroMeses = $osServicio->cotizacionServicio->cotizacion->mesesGarantia;
            if(empty($nroMeses)){
                $nroMeses = 1;
            }
            $osServicio->update(['fecha_termino' => $request->valor,'fecha_fin_garantia' => date('Y-m-d',strtotime($request->valor . ' + '. $nroMeses . ' month'))]);
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
