<?php

namespace App\Http\Controllers;

use App\Models\CotizacionImagenes;
use App\Models\PreCotizacionServicios;
use App\Models\PreCotizaion;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Tecnico extends Controller
{
    private $usuarioController;
    private $moduloPrimVisiPreCoti = "cotizacion.tecnico.visita.pre";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexPrimeraVisitaPreCotizacion()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPrimVisiPreCoti);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $idTecnico = empty(Auth::user()->tecnico) ? null : Auth::user()->tecnico->id;
        $servicios = Servicio::where('estado',1)->get();
        return view("tecnico.primeraVisita",compact("modulos","servicios"));
    }
    public function accionesPreCotizacion(Request $request)
    {
        ini_set('max_execution_time', '300');
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPrimVisiPreCoti);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $idTecnico = empty(Auth::user()->tecnico) ? null : Auth::user()->tecnico->id;
        $respuesta = [];
        switch ($request->acciones) {
            case 'ver-visitas':
                $visitas = PreCotizaion::obtenerPreCotizacionPorTecnicoFecha($idTecnico,$request->fecha);
                $todasFechaVisita = PreCotizaion::obtenerFechasParaFiltroTecnico($idTecnico);
                $respuesta = ['visitas' => $visitas, 'filtros' => $todasFechaVisita];
                break;
            case 'generar-reporte':
                $idPreCotizacion = $request->visita;
                $html = $request->html;
                $servicios = $request->servicios;
                $usuario = Auth::id();
                $listaImgs = [];
                DB::beginTransaction();
                try {
                    if(PreCotizaion::validarPrecotizacionResponsable($idPreCotizacion,$idTecnico,1) === 0){
                        return ['alerta' => 'Usted no tiene autorizado la modificacion de esta Pre - Cotizacion'];
                    }
                    $guardarImgs = new MisProductos();
                    $listaImgs = $guardarImgs->guardarArhivoMasivo($request,"imagenes","imgCotizacionPre");
                    CotizacionImagenes::where('id_pre_cotizacion',$idPreCotizacion)->delete();
                    for ($i=0; $i < count($request->file("imagenes")) ; $i++) { 
                        CotizacionImagenes::create([
                            'id_pre_cotizacion' => $idPreCotizacion,
                            'url_imagen' => $listaImgs[$i]['url_imagen'],
                            'nombre_original_imagen' => $listaImgs[$i]['nombre_real'],
                            'descripcion' => isset($request->descripcionImagen[$i]) ? $request->descripcionImagen[$i] : null
                        ]);
                    }
                    PreCotizacionServicios::where('id_pre_cotizacion',$idPreCotizacion)->delete();
                    for ($i=0; $i < count($servicios) ; $i++) { 
                        if(isset($servicios[$i])){
                            $serviciosList = [
                                'id_pre_cotizacion' => $idPreCotizacion,
                                'id_servicios' => $servicios[$i],
                            ];
                            PreCotizacionServicios::create($serviciosList);
                        }
                    }
                    PreCotizaion::find($idPreCotizacion)->update(['html_primera_visita' => $html,'estado' => 2,'usuario_modificado' => $usuario]);
                    DB::commit();
                    return ['success' => 'Reporte generado con éxito'];
                } catch (\Throwable $th) {
                    DB::rollBack();
                    foreach ($listaImgs as $img) {
                        if(Storage::disk('imgCotizacionPre')->exists($img['url_imagen'])){
                            Storage::disk('imgCotizacionPre')->delete($img['url_imagen']);
                        }
                    }
                    return ['error' => $th->getMessage()];
                }
                break;
        }
        return response()->json($respuesta);
    }
}
