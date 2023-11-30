<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\ClientesContactos;
use App\Models\Configuracion;
use App\Models\CotizacionPreSecciones;
use App\Models\PreCotizacionSeccionImagen;
use App\Models\PreCotizacionServicios;
use App\Models\PreCotizaion;
use App\Models\PreCotizaionContacto;
use App\Models\PreCotizaionTecnico;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\Tecnico;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Models\UsuarioRol;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Yajra\DataTables\Facades\DataTables;

class PreCotizacion extends Controller
{
    private $usuarioController;
    private $moduloPreCotizacion = "cotizacion.precotizacion.nueva";
    private $moduloMisPreCotizacion = "cotizacion.precotizacion.lista";
    private $moduloInformeTecnico = "cotizacion.tecnico.visita.pre";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function obtenerPreCotizaciones(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizaciones = PreCotizaion::obtenerPreCotizaciones()->groupBy("cp.id")->get();
        return DataTables::of($preCotizaciones)->toJson();
    }
    public function obtenerInformePreCotizacion($idPreCotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(PreCotizaion::obtenerPreCotizacion($idPreCotizacion,null));
    }
    public function eliminarFormatoVisita(PreCotizaion $preCotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloInformeTecnico);        
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $rutaVisataUnica = '/formatoVisitas/'.$preCotizacion->formato_visita_pdf;
        if(!empty($preCotizacion->formato_visita_pdf) && Storage::exists($rutaVisataUnica)){
            Storage::delete($rutaVisataUnica);
            $preCotizacion->update(['formato_visita_pdf' => null,'formato_visita_nombre' => null]);
            return response()->json(['success' => 'se eliminó el documento de visita de manera correcta']);
        }
        return response()->json(['alerta' => 'No se encontró ningún documento de visita para ser eliminado']);
    }
    public function visualizacionPdfReporte(PreCotizaion $preCotizacion){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','texto_datos_bancarios','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $titulo = 'REPORTE_PRECOTIZACION_'.str_pad($preCotizacion->id,5,'0',STR_PAD_LEFT);
        $rutaVisataUnica = '/formatoVisitas/'.$preCotizacion->formato_visita_pdf;
        try {
            $pdf = Pdf::loadView('preCotizacion.reporte',compact("configuracion","preCotizacion","titulo"));
            if(!empty($preCotizacion->formato_visita_pdf) && Storage::exists($rutaVisataUnica)){
                $oMerger = PDFMerger::init();
                $oMerger->addString($pdf->output());
                $oMerger->addPDF(storage_path("app".$rutaVisataUnica));
                $oMerger->merge();
                $oMerger->setFileName($titulo);
                return $oMerger->stream();
            }else{
                return $pdf->stream($titulo.".pdf");
            }
        } catch (\Throwable $th) {
            echo 'Error :' . $th->getMessage();
        }
    }
    public function showPreCotizacion(PreCotizaion $precotizacion, Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $precotizacion->listaImagenes = CotizacionPreSecciones::where('id_pre_cotizacion',$precotizacion->id)->get();
        $precotizacion->listaServicios = PreCotizacionServicios::where('id_pre_cotizacion',$precotizacion->id)->get();
        return response()->json(['precotizacion' => $precotizacion->makeHidden("fechaCreada","fechaActualizada")]);
    }
    function agregarImagenPreCotizacion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $guardarImgs = new MisProductos();
        $listaResponse = [];
        $listaImgs = $guardarImgs->guardarArhivoMasivo($request,"imagenes","imgCotizacionPre");
        for ($i=0; $i < count($request->file("imagenes")) ; $i++) { 
            $img = CotizacionPreSecciones::create([
                'id_pre_cotizacion' => $request->idPreCotizacion,
                'url_imagen' => $listaImgs[$i]['url_imagen'],
                'nombre_original_imagen' => $listaImgs[$i]['nombre_real'],
                'descripcion' => null
            ]);
            $listaResponse[] = [
                'id' => $img->id,
                'url_imagen' => $img->url_imagen,
                'nombre_original_imagen' => $img->nombre_original_imagen
            ];
        }
        return response()->json(['listaImagenes' => $listaResponse]);
    }
    public function actualizarPreCotizacion(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        DB::beginTransaction();
        $rutaReporteVisita = null;
        try {
            $fechaHrModificada = now(); 
            $datos = [
                'html_primera_visita' => $request->html,
                'usuario_modificado' => Auth::id(),
                'fechaActualizada' => $fechaHrModificada,
            ];
            $preCotizacionModel = PreCotizaion::find($request->preCotizacion);
            if($request->has('formatoVisitaPdf')){
                $nombreReporteVisita = null;
                $nombreSistemaPdf = null;
                if(!empty($preCotizacionModel->formato_visita_pdf) && Storage::exists('/formatoVisitas/'.$preCotizacionModel->formato_visita_pdf)){
                    Storage::delete('/formatoVisitas/'.$preCotizacionModel->formato_visita_pdf);
                }
                $reporteVisita = $request->file('formatoVisitaPdf');
                if ($reporteVisita) {
                    $nombreSistemaPdf = "reporte_visitas_" . $preCotizacionModel->id . "_". uniqid() .".pdf";
                    $rutaReporteVisita = "formatoVisitas/" . $nombreSistemaPdf;
                    $nombreReporteVisita = $reporteVisita->getClientOriginalName();
                    $reporteVisita->storeAs('formatoVisitas', $nombreSistemaPdf);
                }
                $preCotizacionModel->update(['formato_visita_pdf' => $nombreSistemaPdf,'formato_visita_nombre' => $nombreReporteVisita]);
            }
            $preCotizacionModel->update($datos);
            PreCotizacionServicios::where('id_pre_cotizacion',$request->preCotizacion)->delete();
            if($request->has('servicios')){
                for ($i=0; $i < count($request->servicios) ; $i++) { 
                    PreCotizacionServicios::create([
                         'id_pre_cotizacion' => $request->preCotizacion,
                         'id_servicios' => $request->servicios[$i],
                     ]);
                 }
            }
            DB::commit();
            return response()->json(['success' => 'reporte de pre - cotización actualizado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            if(Storage::exists($rutaReporteVisita)){
                Storage::delete($rutaReporteVisita);
            }
            return ['error' => $th->getMessage(),'line' => $th->getLine()];
        }
    }
    public function indexNuevaPreCotizacion()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $tecnicos = Tecnico::obtenerTecnicosActivos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        return view("preCotizacion.nuevaPreCotizacion",compact("modulos","clientes","tecnicos","tiposDocumentos"));
    }
    public function eliminarImagenPreCotizacion(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $imagen = CotizacionPreSecciones::where(['id' => $request->idImagen,'id_pre_cotizacion' => $request->idPreCotizacion]);
        $consultaImagen = $imagen->first();
        if(!empty($consultaImagen->url_imagen) && Storage::disk('imgCotizacionPre')->exists($consultaImagen->url_imagen)){
            Storage::disk('imgCotizacionPre')->delete($consultaImagen->url_imagen);
        }
        $imagen->delete();
        return response()->json(['success' => 'la imagen se a eliminado correctamente del reporte']);
    }
    public function indexMisPreCotizaciones()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $servicios = Servicio::where('estado',1)->get();
        $clientes = Clientes::obenerClientesActivos();
        $tecnicos = Tecnico::obtenerTecnicosActivos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $modulos = $this->usuarioController->obtenerModulos();
        $archivoVisitaUrl = Configuracion::where(['descripcion' => 'formato_unico_visitas_url'])->first();
        $archivoVisitaNombre = Configuracion::where(['descripcion' => 'formato_unico_visitas'])->first();
        return view("preCotizacion.misPreCotizaciones",compact("modulos","servicios","clientes","tecnicos","tiposDocumentos","archivoVisitaUrl","archivoVisitaNombre"));
    }
    public function obtenerPreCotizacionEditar($precotizacion){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        return response()->json(['preCotizacion' => PreCotizaion::obtenerPreCotizacionEditar($precotizacion)]);
    }
    public function editarPreCotizacion(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $cliente = $request->only("tipoDocumento","nroDocumento","correo","celular","telefono","direccion");
        $clienteId = $request->id_cliente;
        $idUsuario = Auth::id();
        $listaContactos = [];
        $response = ['success' => 'Pre - Cotización actualizada con éxito'];
        DB::beginTransaction();
        try {
            if($request->nuevo == "true"){
                $repetidos = User::where(['correo' => $request->correo])->count();
                if ($repetidos > 0) {
                    return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
                }
                $rolCliente = Rol::where('nombreRol','cliente')->first();
                if(empty($rolCliente)){
                    return response()->json(['alerta' => 'Para crear una cuenta de cliente se necesita el rol Cliente por favor registre el rol']);
                }
                $cliente['nombre'] = $clienteId;
                $cliente['password'] = Hash::make("sistema".date('Y')."@");
                $cliente['estado'] = 2;
                $usuario = User::create($cliente);
                UsuarioRol::create(['rolFk' => $rolCliente->id,'usuarioFk' => $usuario->id]);
                $clienteModel = Clientes::create(['id_usuario' => $usuario->id,'nombreCliente' => $clienteId,'estado' => 1]);
                if($request->has("id_cliente_contacto")){
                    for ($i=0; $i < count($request->id_cliente_contacto); $i++) {
                        $txtNombreContacto = explode("-",trim($request->id_cliente_contacto[$i]));
                        $contactos = [
                            'idCliente' => $clienteModel->id,
                            'nombreContacto' => isset($txtNombreContacto[0]) ? trim($txtNombreContacto[0]) : null,
                            'numeroContacto' => isset($txtNombreContacto[1]) ? trim($txtNombreContacto[1]) : null
                        ];
                        $ccontacto = ClientesContactos::create($contactos);
                        $listaContactos[] = $ccontacto->id;
                    }
                }
                $response['idCliente'] = $clienteModel->id;
                $response['nombreCliente'] = $clienteModel->nombreCliente;
                $clienteId = $clienteModel->id;
                
            }else{
                if($request->has("id_cliente_contacto")){
                    $listaContactos = $request->id_cliente_contacto;
                }
                $repetidos = Clientes::verificarCorreo($clienteId,$request->correo);
                if ($repetidos > 0) {
                    return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
                }
                Clientes::find($clienteId)->usuario()->update($cliente);
            }
            $preCotizacion = $request->only("fecha_hr_visita","detalle","estado");
            $preCotizacion['id_cliente'] = $clienteId;
            $preCotizacion['usuario_modificado'] = $idUsuario;
            PreCotizaion::where('id',$request->idPreCotizacion)->update($preCotizacion);
            PreCotizaionContacto::where('id_cotizacion_pre',$request->idPreCotizacion)->delete();
            foreach ($listaContactos as $vListaCont) {
                PreCotizaionContacto::create([
                    'id_cotizacion_pre' => $request->idPreCotizacion,
                    'id_cliente_contacto' => $vListaCont
                ]);
            }
            $insertarTecnicos = [
                'id_pre_cotizacion' => $request->idPreCotizacion,
                'id_tecnico' => $request->cbTecnicoResponsable,
                'responsable' => 1
            ];
            PreCotizaionTecnico::where('id_pre_cotizacion',$request->idPreCotizacion)->delete();
            PreCotizaionTecnico::create($insertarTecnicos);
            if($request->has("cbOtrosTecnicos")){
                for ($i=0; $i < count($request->cbOtrosTecnicos); $i++) {
                    $insertarTecnicos = [
                        'id_pre_cotizacion' => $request->idPreCotizacion,
                        'id_tecnico' => $request->cbOtrosTecnicos[$i],
                        'responsable' => 0
                    ];
                    PreCotizaionTecnico::create($insertarTecnicos);
                }
            }
            DB::commit();
            return response()->json($response);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage(),'code' => $th->getCode()];
        }
    }
    public function obtenerClientesEditar(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $resultado = ["cliente" => Clientes::obenerCliente($request->cliente)];
        return response()->json($resultado);
    }
    public function editarSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloInformeTecnico);        
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizacion = PreCotizaion::find($request->preCotizacion);
        if(empty($preCotizacion)){
            return response()->json(['alerta' => 'No se encontró la pre-cotizacion para agregar la seccion']);
        }
        CotizacionPreSecciones::where([
            'id_pre_cotizacion' => $preCotizacion->id,
            'id' => $request->seccion,
        ])->update([
            'titulo' => $request->titulo,
            'columnas' => $request->columnas
        ]);
        return response()->json(['success' => 'seccion actualizada correctamente', 'titulo' => $request->titulo, 'columna' => $request->columnas, 'idSeccion' =>$request->seccion]);
    }
    public function agregarImagenEnLaSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloInformeTecnico);        
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizacion = PreCotizaion::find($request->preCotizacion);
        if(empty($preCotizacion)){
            return response()->json(['alerta' => 'No se encontró la pre-cotizacion para agregar la seccion']);
        }
        $seccion = CotizacionPreSecciones::where([
            'id_pre_cotizacion' => $preCotizacion->id,
            'id' => $request->seccion,
        ])->first();
        if(empty($seccion)){
            return response()->json(['alerta' => 'No se encontró la seccion para registrar la imagen']);
        }
        $nombreArchivo = null;
        DB::beginTransaction();
        try {
            $img = PreCotizacionSeccionImagen::create([
                'id_pre_cotizacion_seccion' => $seccion->id,
                'estado' => 1,
                'descripcion' => ''
            ]);
            $extension = $request->file('imagen')->getClientOriginalExtension();
            $nombreArchivo = $seccion->id . '_' . $img->id . '_' .uniqid() . '.' . $extension;
            $request->file('imagen')->storeAs('preCotizacionImgSeccion',$nombreArchivo);
            $img->update(['url_imagen' => $nombreArchivo]);
            DB::commit();
            return response()->json(['success' => 'imagen agregada correctamente', 'idSeccion' => $seccion->id, 'urlImagen' => route("urlImagen",["preCotizacionImgSeccion",$nombreArchivo]), 'idImagen' => $img->id, 'descripcion' => ""]);
        } catch (\Throwable $th) {
            DB::rollBack();
            if(!is_null($nombreArchivo) && Storage::disk('preCotizacionImgSeccion')->exists($nombreArchivo)){
                Storage::disk('preCotizacionImgSeccion')->delete($nombreArchivo);
            }
            return response()->json(['alerta' => $th->getMessage()]);
        }
    }
    public function eliminarSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloInformeTecnico);        
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizacion = PreCotizaion::find($request->preCotizacion);
        if(empty($preCotizacion)){
            return response()->json(['alerta' => 'No se encontró la pre-cotizacion para agregar la seccion']);
        }
        $seccion = CotizacionPreSecciones::where([
            'id_pre_cotizacion' => $preCotizacion->id,
            'id' => $request->seccion,
        ])->first();
        if(empty($seccion)){
            return response()->json(['alerta' => 'No se encontró la seccion para registrar la imagen']);
        }
        foreach (PreCotizacionSeccionImagen::where('id_pre_cotizacion_seccion',$seccion->id)->get() as $key => $imagen) {
            if(Storage::disk('preCotizacionImgSeccion')->exists($imagen->url_imagen)){
                Storage::disk('preCotizacionImgSeccion')->delete($imagen->url_imagen);
            }
            $imagen->delete();
        }
        $seccion->delete();
        return response()->json(['success' => 'seccion eliminada correctamente']);
    }
    public function eliminarImagenEnLaSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloInformeTecnico);        
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizacion = PreCotizaion::find($request->preCotizacion);
        if(empty($preCotizacion)){
            return response()->json(['alerta' => 'No se encontró la pre-cotizacion para agregar la seccion']);
        }
        $seccion = CotizacionPreSecciones::where([
            'id_pre_cotizacion' => $preCotizacion->id,
            'id' => $request->seccion,
        ])->first();
        if(empty($seccion)){
            return response()->json(['alerta' => 'No se encontró la seccion para registrar la imagen']);
        }
        $imagen = PreCotizacionSeccionImagen::where(['id_pre_cotizacion_seccion' => $seccion->id,'id' => $request->img])->first();
        if(!empty($imagen) && Storage::disk('preCotizacionImgSeccion')->exists($imagen->url_imagen)){
            Storage::disk('preCotizacionImgSeccion')->delete($imagen->url_imagen);
        }
        $imagen->delete();
        return response()->json(['success' => 'imagen eliminada correctamente']);
    }
    public function obtenerInformacionSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloInformeTecnico);        
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizacion = PreCotizaion::find($request->preCotizacion);
        if(empty($preCotizacion)){
            return response()->json(['alerta' => 'No se encontró la pre-cotizacion para agregar la seccion']);
        }
        $seccion = CotizacionPreSecciones::where([
            'id_pre_cotizacion' => $preCotizacion->id,
            'id' => $request->seccion,
        ])->first();
        if(empty($seccion)){
            return response()->json(['alerta' => 'No se encontró la seccion para registrar la imagen']);
        }
        return response()->json(['success' => $seccion]);
    }
    public function actualizarDatos(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloInformeTecnico);        
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizacion = PreCotizaion::find($request->preCotizacion);
        if(empty($preCotizacion)){
            return response()->json(['alerta' => 'No se encontró la pre-cotizacion para agregar la seccion']);
        }
        $seccion = CotizacionPreSecciones::where([
            'id_pre_cotizacion' => $preCotizacion->id,
            'id' => $request->seccion,
        ])->first();
        if(empty($seccion)){
            return response()->json(['alerta' => 'No se encontró la seccion para registrar la imagen']);
        }
        PreCotizacionSeccionImagen::where(['id_pre_cotizacion_seccion' => $seccion->id,'id' => $request->imagen])->update(['descripcion' => $request->valor]);
        return response()->json(['success' => 'descripcion actualizada correctamente']);
    }
    public function agregarNuevaSeccion(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloInformeTecnico);
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $preCotizacion = PreCotizaion::find($request->preCotizacion);
        if(empty($preCotizacion)){
            return response()->json(['alerta' => 'No se encontró la pre-cotizacion para agregar la seccion']);
        }
        $preCotizacionSeccion = CotizacionPreSecciones::create([
            'id_pre_cotizacion' => $preCotizacion->id,
            'titulo' => $request->titulo,
            'columnas' => $request->columnas,
            'estado' => 1
        ]);
        return response()->json(['success' => 'seccion agregada correctamente', 'titulo' => $preCotizacionSeccion->titulo, 'columnas' => $preCotizacionSeccion->columnas, 'idSeccion' => $preCotizacionSeccion->id, 'listaImagenes' => []]);
    }
    public function accionesPreCotizacion(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPreCotizacion);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $resultado = [];
        switch ($request->acciones) {
            case 'obtener-cliente':
                $resultado = ["cliente" => Clientes::obenerCliente($request->cliente)];
            break;
            case 'agregar-precotizacion':
                $cliente = $request->only("tipoDocumento","nroDocumento","correo","celular","telefono","direccion");
                $clienteId = $request->id_cliente;
                $idUsuario = Auth::id();
                $listaContactos = [];
                DB::beginTransaction();
                try {
                    if($request->nuevo == "true"){
                        $repetidos = User::where(['correo' => $request->correo])->count();
                        if ($repetidos > 0) {
                            return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
                        }
                        $rolCliente = Rol::where('nombreRol','cliente')->first();
                        if(empty($rolCliente)){
                            return response()->json(['alerta' => 'Para crear una cuenta de cliente se necesita el rol Cliente por favor registre el rol']);
                        }
                        $cliente['nombre'] = $clienteId;
                        $cliente['password'] = Hash::make("sistema".date('Y')."@");
                        $cliente['estado'] = 2;
                        $usuario = User::create($cliente);
                        UsuarioRol::create(['rolFk' => $rolCliente->id,'usuarioFk' => $usuario->id]);
                        $clienteModel = Clientes::create(['id_usuario' => $usuario->id,'nombreCliente' => $clienteId,'id_pais' => 165,'estado' => 1]);
                        if($request->has("id_cliente_contacto")){
                            for ($i=0; $i < count($request->id_cliente_contacto); $i++) {
                                $txtNombreContacto = explode("-",trim($request->id_cliente_contacto[$i]));
                                $contactos = [
                                    'idCliente' => $clienteModel->id,
                                    'nombreContacto' => isset($txtNombreContacto[0]) ? trim($txtNombreContacto[0]) : null,
                                    'numeroContacto' => isset($txtNombreContacto[1]) ? trim($txtNombreContacto[1]) : null
                                ];
                                $ccontacto = ClientesContactos::create($contactos);
                                $listaContactos[] = $ccontacto->id;
                            }
                        }

                        $clienteId = $clienteModel->id;
                    }else{
                        if($request->has("id_cliente_contacto")){
                            $listaContactos = $request->id_cliente_contacto;
                        }
                        $repetidos = Clientes::verificarCorreo($clienteId,$request->correo);
                        if ($repetidos > 0) {
                            return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
                        }
                        Clientes::find($clienteId)->usuario()->update($cliente);
                    }
                    $preCotizacion = $request->only("fecha_hr_visita","detalle");
                    $preCotizacion['id_cliente'] = $clienteId;
                    $preCotizacion['usuario_creado'] = $idUsuario;
                    $preCotizacion['usuario_modificado'] = $idUsuario;
                    $preCotizacion['estado'] = 1;
                    $modelPreCoti = PreCotizaion::create($preCotizacion);
                    foreach ($listaContactos as $vListaCont) {
                        PreCotizaionContacto::create([
                            'id_cotizacion_pre' => $modelPreCoti->id,
                            'id_cliente_contacto' => $vListaCont
                        ]);
                    }
                    $insertarTecnicos = [
                        'id_pre_cotizacion' => $modelPreCoti->id,
                        'id_tecnico' => $request->cbTecnicoResponsable,
                        'responsable' => 1
                    ];
                    PreCotizaionTecnico::create($insertarTecnicos);
                    if($request->has("cbOtrosTecnicos")){
                        for ($i=0; $i < count($request->cbOtrosTecnicos); $i++) {
                            $insertarTecnicos = [
                                'id_pre_cotizacion' => $modelPreCoti->id,
                                'id_tecnico' => $request->cbOtrosTecnicos[$i],
                                'responsable' => 0
                            ];
                            PreCotizaionTecnico::create($insertarTecnicos);
                        }
                    }
                    DB::commit();
                    return response()->json(['success' => 'Pre - Cotización generada con éxito']);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return ['error' => $th->getMessage(),'code' => $th->getCode()];
                }
            break;
        }
        return response()->json($resultado);
    }
}
