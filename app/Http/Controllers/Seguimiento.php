<?php

namespace App\Http\Controllers;

use App\Exports\ExportGarantiaProductosServicios;
use App\Exports\ExportSeguimientoCotizacion;
use App\Models\Clientes;
use App\Models\Configuracion;
use App\Models\Cotizacion;
use App\Models\CotizacionProductos;
use App\Models\CotizacionSeguimiento;
use App\Models\CotizacionServicio;
use App\Models\OrdenServicioCotizacionServicio;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class Seguimiento extends Controller
{
    private $moduloSeguimiento = "admin.cotizacion.seguimiento";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $fechaFin = date('Y-m-d');
        $clientes = Clientes::obenerClientesActivos();
        $fechaInicio = date('Y-m-d',strtotime($fechaFin . ' - 90 days'));
        $meses = (new Utilitarios)->obtenerNombresMeses();
        $modulos = $this->usuarioController->obtenerModulos();
        $usuarios = User::obtenerUsuariosNoSonClientes();
        return view("cotizacion.seguimiento",compact("usuarios","modulos","fechaFin","fechaInicio","meses","clientes"));
    }
    public function all(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $seguimientos = $this->cotizacionSeguimiento($request->fechaInicio,$request->fechaFin,$request->porcentaje,$request->responsable);
        return DataTables::of($seguimientos)->toJson();
    }
    public function reporteGarantias($tipo,Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return redirect()->route('home');
        }
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','texto_datos_bancarios','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $garantias = Cotizacion::obtenerGarantiasFechas($request->mes_fin_garantia,$request->year_fin_garantia,$request->cliente,$request->vigencia);
        $fechaInicioReporte = date('d/m/Y',strtotime($request->fecha_inicio_cotizacion));
        $fechaFinReporte = date('d/m/Y',strtotime($request->fecha_fin_cotizacion));
        if($tipo === 'pdf'){
            return Pdf::loadView('cotizacion.reportes.garantiaPDF',compact("garantias","configuracion","fechaInicioReporte","fechaFinReporte"))
            ->setPaper("A4","landscape")->stream('reporte_garantias_productos_servicios.pdf');
        }else if($tipo === 'excel'){
            return Excel::download(new ExportGarantiaProductosServicios($garantias,$fechaInicioReporte,$fechaFinReporte,'cotizacion.reportes.garantiaEXCEL'),'reporte_garantias_productos_servicios.xlsx');
        }
        return abort(404);
        // dd($seguimientosGarantia,$tipo);
    }
    public function cotizacionSeguimiento($fechaInicio,$fechaFin,$porcentaje,$responsable){
        $seguimientos = Cotizacion::obtenerCotizacion()->where('fechaCotizacion','>=',$fechaInicio)->where('fechaCotizacion','<=',$fechaFin)->where('cotizacion.estado',1);
        if($porcentaje !== 'todos'){
            $seguimientos = $seguimientos->where('cotizacion.porcentaje_actual',$porcentaje);
        }
        if($responsable !== 'todos'){
            $seguimientos = $seguimientos->where('cotizacion.cotizadorUsuario',$responsable);
        }
        return $seguimientos->get();
    }
    public function reporteCotizaciones($tipo,Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return redirect()->route('home');
        }
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','texto_datos_bancarios','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $cotizaciones = $this->cotizacionSeguimiento($request->fecha_inicio_cotizacion,$request->fecha_fin_cotizacion,$request->porcentaje,$request->cotizador);
        $fechaInicioReporte = date('d/m/Y',strtotime($request->fecha_inicio_cotizacion));
        $fechaFinReporte = date('d/m/Y',strtotime($request->fecha_fin_cotizacion));
        if($tipo === 'pdf'){
            return Pdf::loadView('cotizacion.reportes.seguimientoPDF',compact("cotizaciones","configuracion","fechaInicioReporte","fechaFinReporte"))
            ->setPaper("A4","landscape")->stream('reporte_seguimineto_cotizaciones.pdf');
        }else if($tipo === 'excel'){
            return Excel::download(new ExportSeguimientoCotizacion($cotizaciones,$fechaInicioReporte,$fechaFinReporte,'cotizacion.reportes.seguimientoEXCEL'),'reporte_seguimineto_cotizaciones.xlsx');
        }
        return abort(404);
    }
    public function allGarantia(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $seguimientosGarantia = Cotizacion::obtenerGarantiasFechas($request->mes,$request->year,$request->cliente,$request->estado);
        return DataTables::of($seguimientosGarantia)->toJson();
    }
    public function showHistorialSeguimiento(Cotizacion $cotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        return response()->json(['historialSeguimientos' => $cotizacion->historialSeguimientos()->select("id","id_usuario","porcentaje","id_cotizacion","descripcion","fechaCreada")->selectRaw("DATE_FORMAT(fechaCreada,'%d/%m/%Y %h:%i %p') AS fechaCreadaFormato")->with('usuario:id,nombres,apellidos')->orderBy('porcentaje','desc')->get()]);
    }
    public function store(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        DB::beginTransaction();
        try {
            $datos = $request->except("anular");
            if($request->has('anular')){
                $datos['porcentaje'] = 0;
                $datosCotizacion['estado'] = 0;
            }
            $datosCotizacion['porcentaje_actual'] = $datos['porcentaje'];
            $datos['id_usuario'] = Auth::id();
            Cotizacion::find($request->id_cotizacion)->update($datosCotizacion);
            CotizacionSeguimiento::create($datos);
            DB::commit();
            return response()->json(['success' => 'seguimiento agregado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function update($cotizacion,Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        DB::beginTransaction();
        try {
            if($request->has("seguimiento") && isset($request->porcentaje[0])){
                Cotizacion::find($cotizacion)->update(['porcentaje_actual' => $request->porcentaje[0]]);
            }
            for ($i=0; $i < count($request->seguimiento); $i++) {
                CotizacionSeguimiento::where(['id_cotizacion' => $cotizacion,'id' => $request->seguimiento[$i]])->update([
                    'descripcion' => $request->descripcion[$i],
                    'porcentaje' => $request->porcentaje[$i],
                ]);
            }
            DB::commit();
            return response()->json(['success' => 'seguimientos actualizados correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function destroy($seguimiento,$cotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        CotizacionSeguimiento::where(['id_cotizacion' => $cotizacion,'id' => $seguimiento])->delete();
        $cotizacionSeguimientoPrimero = CotizacionSeguimiento::where(['id_cotizacion' => $cotizacion])->first();
        Cotizacion::find($cotizacion)->update(['porcentaje_actual' => empty($cotizacionSeguimientoPrimero) ? 0 : $cotizacionSeguimientoPrimero->porcentaje]);
        return response()->json(['success' => 'seguimiento eliminado correctamente']);
    }
    public function notificacion($tipo,$id) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloSeguimiento);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        if($tipo === 'Servicio'){
            $servicio = OrdenServicioCotizacionServicio::find($id);
            $response = [
                'nroCotizacion' => str_pad($servicio->cotizacionServicio->id_cotizacion,5,'0',STR_PAD_LEFT),
                'fechaFinGarantia' => date('d/m/Y',strtotime($servicio->fecha_fin_garantia)),
                'cliente' => $servicio->cotizacionServicio->cotizacion->cliente->nombreCliente,
                'celular' => $servicio->cotizacionServicio->cotizacion->cliente->usuario->celular,
                'servicios' => $servicio->cotizacionServicio->servicios->servicio,
                'usuario' => Auth::user()->nombres
            ];
        }else{
            $productos = CotizacionProductos::find($id);
            $response = [
                'nroCotizacion' => str_pad($productos->id_cotizacion,5,'0',STR_PAD_LEFT),
                'fechaFinGarantia' => date('d/m/Y',strtotime($productos->fecha_fin_garantia)),
                'cliente' => $productos->cotizacion->cliente->nombreCliente,
                'celular' => $productos->cotizacion->cliente->usuario->celular,
                'servicios' => $productos->productos->nombreProducto,
                'usuario' => Auth::user()->nombres
            ];
        }
        return response()->json($response);
    }
}
