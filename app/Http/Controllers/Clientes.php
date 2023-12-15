<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Usuario;
use App\Models\CertificadosServicios;
use App\Models\Clientes as ModelsClientes;
use App\Models\ClientesContactos;
use App\Models\Comprobantes;
use App\Models\Configuracion;
use App\Models\Cotizacion as ModelCotizacion;
use App\Models\EntregaActa;
use App\Models\OrdenServicio;
use App\Models\OrdenServicioCotizacionServicio;
use App\Models\PagoCuotas;
use App\Models\Pais;
use App\Models\PreCotizaion;
use App\Models\Rol;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Models\UsuarioRol;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Webklex\PDFMerger\Facades\PDFMergerFacade;
use Yajra\DataTables\Facades\DataTables;

class Clientes extends Controller
{
    private $usuarioController;
    private $moduloCliente = "admin.ventas.clientes.index";
    private $moduloComprobantes = "cliente.comprobantes.index";
    private $moduloVisitas = "cliente.precotizaciones.index";
    private $moduloCotizaciones = "cliente.cotizaciones.index";
    private $moduloInformes = "cliente.informes.index";
    private $moduloCertificados = "cliente.certificados.index";
    private $moduloActas = "cliente.actas.index";

    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $paises = Pais::all()->where('estado',1);
        return view("ventas.clientes",compact("modulos","tiposDocumentos","paises"));
    }
    public function listar(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $clientes = ModelsClientes::obenerClientes();
        return DataTables::of($clientes)->toJson();
    }
    public function store(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $repetidos = User::where(['correo' => $request->correo])->count();
        if ($repetidos > 0) {
            return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
        }
        $rolCliente = Rol::where('nombreRol','cliente')->first();
        if(empty($rolCliente)){
            return response()->json(['alerta' => 'Para crear una cuenta de cliente se necesita el rol Cliente por favor registre el rol']);
        }
        $datosUsuario = $request->only("correo","password","tipoDocumento","nroDocumento","telefono","celular","direccion");
        $datosUsuario['password'] = Hash::make($datosUsuario['password']);
        $datosUsuario['estado'] = 2;
        $datosUsuario['nombres'] = $request->nombreCliente;
        DB::beginTransaction();
        try {
            $usuario = User::create($datosUsuario);
            UsuarioRol::create(['rolFk' => $rolCliente->id,'usuarioFk' => $usuario->id]);
            $datosCliente = $request->only("nombreCliente","id_pais","distrito","provincia","departamento");
            $datosCliente['id_usuario'] = $usuario->id;
            $datosCliente['estado'] = 1;
            $cliente = ModelsClientes::create($datosCliente);
            if(isset($request->contactoNombres)){
                for ($i=0; $i < count($request->contactoNombres); $i++) {
                    $contactos = [
                        'idCliente' => $cliente->id,
                        'nombreContacto' => isset($request->contactoNombres[$i]) ? $request->contactoNombres[$i] : null,
                        'numeroContacto' => isset($request->contactoNumero[$i]) ? $request->contactoNumero[$i] : null
                    ];
                    ClientesContactos::create($contactos);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Cliente creado correctamente, recuerde que su contraseña temporal es ' . $request->password]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(),'codigo' => $th->getCode()]);
        }
    }
    public function show($cliente)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        return response()->json(['cliente' => ModelsClientes::obenerCliente($cliente)]);
    }
    public function update(ModelsClientes $cliente, Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        DB::beginTransaction();
        try {
            $datosUsuario = $request->only("tipoDocumento","nroDocumento","telefono","celular","direccion");
            $datosUsuario['nombres'] = $request->nombreCliente;
            $datosCliente = $request->only("nombreCliente","id_pais","distrito","provincia","departamento");
            $datosCliente['estado'] = $request->has("estado") ? 1 : 0;
            User::where('id',$cliente->id_usuario)->update($datosUsuario);
            $cliente->update($datosCliente);
            if(isset($request->contactoNombres)){
                for ($i=0; $i < count($request->contactoNombres); $i++) {
                    $contactos = [
                        'nombreContacto' => isset($request->contactoNombres[$i]) ? $request->contactoNombres[$i] : null,
                        'numeroContacto' => isset($request->contactoNumero[$i]) ? $request->contactoNumero[$i] : null
                    ];
                    if(isset($request->idContacto[$i])){
                        ClientesContactos::where(['id' => $request->idContacto[$i],'idCliente' => $cliente->id])->update($contactos);
                    }else{
                        $contactos['idCliente'] = $cliente->id;
                        ClientesContactos::create($contactos);
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => 'Cliente actualizado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function eliminarContacto(ClientesContactos $contacto)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        if($contacto->cotizaciones()->count()){
            return response()->json(['alerta' => 'Este contacto no puede ser eliminado debido a que esta asociado a una o varias cotizaciones, si se requiere eliminar, por favor elimine o cambie el representante de las cotizaciones asociadas']);
        }
        $contacto->delete();
        return response()->json(['success' => 'contacto eliminado correctamente']);
    }
    public function destroy(ModelsClientes $cliente)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        DB::beginTransaction();
        try {
            if($cliente->cotizaciones()->where('estado','>=',0)->count()){
                return response()->json(['alerta' => 'Primero se deben eliminar las cotizaciones asociadas a este cliente']);
            }
            $cliente->update(['estado' => 0]);
            $cliente->usuario()->update(['estado' => 0]);
            DB::commit();
            return response()->json(['success' => 'cliente eliminado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function cotizacionesIndex() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizaciones);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $fechaFin = date('Y-m-d');
        $fechaInicio = date('Y-m-d',strtotime($fechaFin . ' - 90 days'));
        return view("clientes.cotizaciones",compact("modulos","fechaFin","fechaInicio"));
    }
    public function obtenerClienteId($idUsuario) {
        $cliente = ModelsClientes::where(['id_usuario' => $idUsuario,'estado' => 1])->first();
        return empty($cliente) ? 0 : $cliente->id;
    }
    public function obtenerCotizaciones(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizaciones);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $cotizacionControlador = new Cotizacion();
        return DataTables::of($cotizacionControlador->listaCotizaciones($request->fechaInicio,$request->fechaFin,$this->obtenerClienteId(Auth::id()),'TODOS'))->toJson();
    }
    public function verPdfCotizacion($idCotizacion) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCotizaciones);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $cotizacion = ModelCotizacion::where(['id_cliente' => $this->obtenerClienteId(Auth::id()),'id' => $idCotizacion])->first();
        if(empty($cotizacion)){
            return abort(404);
        }
        $rutaPdf = "/cotizacion/reportes/" . $cotizacion->documento;
        if(!Storage::exists($rutaPdf)){
            abort(404,'No se encontró el pdf de la cotizacion');
        }
        $contenido = Storage::get($rutaPdf);
        return response($contenido, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="cotizacion.pdf"'
        ]);
    }
    public function visitasIndex() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloVisitas);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $fechaFin = date('Y-m-d',strtotime(date('Y-m-d') . ' + 90 days'));
        $fechaInicio = date('Y-m-d',strtotime(date('Y-m-d') . ' - 90 days'));
        return view("clientes.visitas",compact("modulos","fechaFin","fechaInicio"));
    }
    public function obtenerVisitas(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloVisitas);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $preCotizaciones = PreCotizaion::obtenerPreCotizaciones()->where('c.id',$this->obtenerClienteId(Auth::id()))->whereBetween('cp.fecha_hr_visita',[$request->fechaInicio . ' 00:00:00',$request->fechaFin . ' 00:00:00'])->groupBy("cp.id")->get();
        return DataTables::of($preCotizaciones)->toJson();
    }
    public function visualizacionPdfReporte(PreCotizaion $preCotizacion){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloVisitas);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $preCotizacion = $preCotizacion->where('id_cliente',$this->obtenerClienteId(Auth::id()))->first();
        if(empty($preCotizacion)){
            return abort(404);
        }
        $configuracion = Configuracion::obtener();
        $titulo = 'REPORTE_PRECOTIZACION_'.str_pad($preCotizacion->id,5,'0',STR_PAD_LEFT);
        $rutaVisataUnica = '/formatoVisitas/'.$preCotizacion->formato_visita_pdf;
        try {
            $pdf = Pdf::loadView('preCotizacion.reporte',compact("configuracion","preCotizacion","titulo"));
            if(!empty($preCotizacion->formato_visita_pdf) && Storage::exists($rutaVisataUnica)){
                $oMerger = PDFMergerFacade::init();
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
    public function misInformesIndex() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloInformes);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $fechaFin = date('Y-m-d',strtotime(date('Y-m-d') . ' + 90 days'));
        $fechaInicio = date('Y-m-d',strtotime(date('Y-m-d') . ' - 90 days'));
        $modulos = $this->usuarioController->obtenerModulos();
        return view("clientes.informes",compact("modulos","fechaFin","fechaInicio"));
    }
    public function obtenerInformes(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloInformes);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $listaDeInformes = OrdenServicioCotizacionServicio::obtenerInformesGenerados($request->fechaInicio . ' 00:00:00',$request->fechaFin . ' 00:00:00')->where('clientes.id',$this->obtenerClienteId(Auth::id()))->get();
        return DataTables::of($listaDeInformes)->toJson();
    }
    public function reportePrevioInforme($idOrdenServicio,$idServicio) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloInformes);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $ordenServicioDetalle = OrdenServicio::where(['id_cliente'=>$this->obtenerClienteId(Auth::id()),'id'=>$idOrdenServicio])->first();
        if(empty($ordenServicioDetalle)){
            return abort(404);
        }
        $nroOrdenServicio = str_pad($ordenServicioDetalle->id,5,'0',STR_PAD_LEFT);
        $utilitarios = new Utilitarios();
        $configuracion = Configuracion::obtener();
        $ordenServicio = $ordenServicioDetalle->servicios()->where('id',$idServicio)->get();
        if($ordenServicio->isEmpty()){
            return abort(404,'No se encontro el informe');
        }
        $tituloPdf = "INFORME DEL SERVICIO - " .  str_pad($ordenServicio->first()->id,5,'0',STR_PAD_LEFT);
        return Pdf::loadView('ordenesServicio.reportes.informe',compact("utilitarios","ordenServicio","tituloPdf","nroOrdenServicio","configuracion","ordenServicioDetalle"))->stream($tituloPdf . '.pdf');
    }
    public function misCertificados() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCertificados);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $fechaFin = date('Y-m-d',strtotime(date('Y-m-d') . ' + 90 days'));
        $fechaInicio = date('Y-m-d',strtotime(date('Y-m-d') . ' - 90 days'));
        $modulos = $this->usuarioController->obtenerModulos();
        return view("clientes.certificados",compact("modulos","fechaFin","fechaInicio"));
    }
    public function obtenerCertificados(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCertificados);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $listaCertificados = CertificadosServicios::obtenerCertificados($request->fechaInicio,$request->fechaFin)->where('orden_servicio.id_cliente',$this->obtenerClienteId(Auth::id()))->get();
        return DataTables::of($listaCertificados)->toJson();
    }
    public function visualizarCertificado(CertificadosServicios $certificado) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCertificados);
        if(isset($verif['session'])){
            return redirect()->route("home");
        }
        $acceso = CertificadosServicios::obtenerCertificados()->where(['orden_servicio.id_cliente' => $this->obtenerClienteId(Auth::id()),'certificados_servicios.id' => $certificado->id])->first();
        if(empty($acceso)){
            return abort(404);
        }
        $utilitarios = new Utilitarios();
        $configuracion = Configuracion::obtener();
        $certificado->fechaLarga = $utilitarios->obtenerFechaLargaSinDia(strtotime($certificado->fecha));
        $cliente = $certificado->ordenServicioCotizacion->cotizacionServicio->cotizacion->cliente;
        $direccionCliente = $certificado->ordenServicioCotizacion->cotizacionServicio->cotizacion->direccionCliente;
        $tituloPdf = 'CERTIFICADO DE OPERATIVIDAD '. str_pad($certificado->id,5,"0",STR_PAD_LEFT);
        return Pdf::loadView('ordenesServicio.reportes.certificado',compact("cliente","tituloPdf","configuracion","certificado","direccionCliente"))->stream($tituloPdf.'.pdf');
    }
    public function misComprobantes() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloComprobantes);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $fechaFin = date('Y-m-d',strtotime(date('Y-m-d') . ' + 90 days'));
        $fechaInicio = date('Y-m-d',strtotime(date('Y-m-d') . ' - 90 days'));
        $modulos = $this->usuarioController->obtenerModulos();
        return view("clientes.comprobantes",compact("modulos","fechaFin","fechaInicio"));
    }
    public function obtenerComprobantes(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloComprobantes);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $listaComprobantes = Comprobantes::comprobantesClientes($request->fechaInicio,$request->fechaFin,$this->obtenerClienteId(Auth::id()));
        return DataTables::of($listaComprobantes)->toJson();
    }
    public function verComprobanteFacturacion(Comprobantes $comprobante) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloComprobantes);
        if(isset($verif['session'])){
            return redirect()->route('home');
        }
        if(($comprobante->ordenServicio->id_cliente !== $this->obtenerClienteId(Auth::id())) || $comprobante->ordenServicio->estado < 1){
            return abort(404);
        }
        if($comprobante->tipo_comprobante === "00"){
            $configuracion = Configuracion::obtener();
            $comprobanteInterno = $comprobante->comprobanteInterno;
            $numeroPago = str_pad($comprobanteInterno->id,4,'0',STR_PAD_LEFT);
            $titulo = "GUIA INTERNA - " . $numeroPago;
            $strFechaPago = strtotime($comprobanteInterno->fecha_emision);
            return Pdf::loadView('facturacion.comprobanteInterno',compact("comprobanteInterno","configuracion","numeroPago","titulo","strFechaPago"))->setPaper('A4','landscape')->stream($titulo.'.pdf');
        }
        return redirect((new RapiFac)->urlPdfComprobantes.'?key=' . $comprobante->repositorio);
    }
    public function verComprobanteCuota(PagoCuotas $cuota) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloComprobantes);
        if(isset($verif['session'])){
            return redirect()->route('home');
        }
        if(($cuota->ordenServicio->id_cliente !== $this->obtenerClienteId(Auth::id())) || $cuota->ordenServicio->estado < 1){
            return abort(404);
        }
        $configuracion = Configuracion::obtener();
        $numeroPago = str_pad($cuota->id,4,'0',STR_PAD_LEFT);
        $titulo = "CUOTA - " . $numeroPago;
        $strFechaPago = strtotime($cuota->fecha_pagada);
        $fechaTexto = (new Utilitarios)->obtenerFechaLargaSinDia($strFechaPago);
        $fechaFormato = date('d/m/Y',$strFechaPago);
        $nombreMonto = (new RapiFac)->numeroAPalabras($cuota->monto_pagado,$cuota->ordenServicio->tipoMoneda);
        return Pdf::loadView('ordenesServicio.reportes.pagoCuota',compact("cuota","titulo","configuracion","fechaFormato","fechaTexto","numeroPago","nombreMonto"))->setPaper('none')->stream($titulo . '.pdf');
    }
    public function misActas() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloActas);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $fechaFin = date('Y-m-d',strtotime(date('Y-m-d') . ' + 90 days'));
        $fechaInicio = date('Y-m-d',strtotime(date('Y-m-d') . ' - 90 days'));
        $modulos = $this->usuarioController->obtenerModulos();
        return view("clientes.actas",compact("modulos","fechaFin","fechaInicio"));
    }
    public function obtenerActas(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloComprobantes);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $listaActas = EntregaActa::actascClientes($request->fechaInicio,$request->fechaFin,$this->obtenerClienteId(Auth::id()));
        return DataTables::of($listaActas)->toJson();
    }
    public function verActa(EntregaActa $entregaActa) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloComprobantes);
        if(isset($verif['session'])){
            return redirect()->route('home');
        }
        if(($entregaActa->ordenServicio->id_cliente !== $this->obtenerClienteId(Auth::id())) || $entregaActa->ordenServicio->estado < 1){
            return abort(404);
        }
        $configuracion = Configuracion::obtener();
        $utilitarios = new Utilitarios();
        $tituloPdf = "ENTREGA ACTAS - " . str_pad($entregaActa->id,5,'0',STR_PAD_LEFT);
        $diaFecha = $utilitarios->obtenerFechaLargaSinDia(strtotime($entregaActa->fecha_entrega));
        return Pdf::loadView('ordenesServicio.reportes.entregaActa',compact("tituloPdf","configuracion","entregaActa","diaFecha"))->stream($tituloPdf.".pdf");
    }
}
