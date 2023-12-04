<?php

namespace App\Http\Controllers;

use App\Exports\ExportOrdenesServicios;
use App\Exports\ExportPagos;
use App\Models\Clientes;
use App\Models\ComprobanteInterno;
use App\Models\ComprobanteInternoDetalle;
use App\Models\Comprobantes;
use App\Models\Configuracion;
use App\Models\Cotizacion;
use App\Models\CotizacionProductos;
use App\Models\CotizacionServicio;
use App\Models\EntregaActa;
use App\Models\OrdenServicio as ModelsOrdenServicio;
use App\Models\OrdenServicioAdicional;
use App\Models\OrdenServicioCotizacionProducto;
use App\Models\OrdenServicioCotizacionServicio;
use App\Models\PagoCuotas;
use App\Models\PagoCuotasImg;
use App\Models\TipoDocumento;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class OrdenServicio extends Controller
{
    private $usuarioController;
    private $moduloOSAgregar = "os.generar.index";
    private $moduloOsMostrar = "admin.ordenesServicios.index";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function agregarCuotaOrdenServicio($cantidadCuotas,$idOrdenServicio) {
        DB::beginTransaction();
        try {
            $cuotaAntigua = PagoCuotas::where('id_orden_servicio',$idOrdenServicio);
            $fechaInicio = date('Y-m-d');
            $numeroCuota = 0;
            if(!$cuotaAntigua->get()->isEmpty()){
                $fechaInicio = $cuotaAntigua->orderBy('fecha_vencimiento','desc')->first()->fecha_vencimiento;
                $numeroCuota = $cuotaAntigua->orderBy('nro_cuota','desc')->first()->nro_cuota;
            }
            for ($i=0; $i < $cantidadCuotas; $i++) { 
                $numeroCuota++;
                $fechaInicio = date('Y-m-d',strtotime($fechaInicio . "+ 1 month"));
                PagoCuotas::create([
                    'id_orden_servicio' => $idOrdenServicio,
                    'nro_cuota' => $numeroCuota,
                    'fecha_vencimiento' => $fechaInicio
                ]);
            }
            DB::commit();
            return ['success' => 'cuotas agregadas correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public function modificarFacturacionExterna(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        ModelsOrdenServicio::find($request->ordenServicio)->update(['facturacion_externa' => $request->valor === 'true' ? 1 : 0]);
        return response()->json(['success' => 'facturacion externa modificada correctamente']);
    }
    public function modificarCuota(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $pagoCuota = PagoCuotas::where(['id_orden_servicio' => $request->ordenServicioId,'id' => $request->cuotaId])->first();
        if(empty($pagoCuota)){
            return response()->json(['alerta' => 'No se encontro la cuota para ser modificada']);
        }
        $datosCuotas = $request->only("fecha_vencimiento","monto_pagar");
        $datosPagos = [
            'estado' => 1,
            'fecha_pagada' => null,
            'monto_pagado' => null,
            'id_firmante_pago' => null,
            'descripcion_pagada' => null
        ];
        if($request->has('cuota_pagada')){
            $datosPagos = $request->only("fecha_pagada","monto_pagado","id_firmante_pago","descripcion_pagada");
            $datosPagos['estado'] = 2;
        }
        list($comprobanteSunat,$nombreComprobante) = [$pagoCuota->comprobante_unico,$pagoCuota->comprobante_nombre];
        if($request->has('comprobante_sunat')){
            if(!is_null($pagoCuota->comprobante_unico) && Storage::disk('pagoCuotasSunat')->exists($pagoCuota->comprobante_unico)){
                Storage::disk('pagoCuotasSunat')->delete($pagoCuota->comprobante_unico);
            }
            $extension = $request->file('comprobante_sunat')->getClientOriginalExtension();
            $comprobanteSunat = $pagoCuota->id . '_' . $pagoCuota->id_orden_servicio . '_' .time() . '.' . $extension;
            $request->file('comprobante_sunat')->storeAs('pagoCuotasSunat',$comprobanteSunat);
            $nombreComprobante = $request->file('comprobante_sunat')->getClientOriginalName();
        }
        $ordenServicio = ModelsOrdenServicio::find($request->ordenServicioId);
        $pagoCuota->update(array_merge($datosCuotas,$datosPagos,['comprobante_unico' => $comprobanteSunat, 'comprobante_nombre' => $nombreComprobante]));
        return response()->json(['success' => 'cuota modificada correctamente', 'cuotas' => PagoCuotas::obtenerCuotasOrdenServicio($ordenServicio->id,$ordenServicio->tipoMoneda)]);
    }
    public function previoPago(ModelsOrdenServicio $ordenServicio) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $cuotasTotales = PagoCuotas::where('id_orden_servicio',$ordenServicio->id)->count();
        $cuotasPendientes = PagoCuotas::where(['id_orden_servicio' => $ordenServicio->id,'estado' => 2])->count();
        if($cuotasTotales > 0 &&  $cuotasPendientes < $cuotasTotales){
            return response()->json(['alerta' => 'Para generar el comprobante se deben de cancelar todas las cuotas, actualmente hay ' . $cuotasPendientes . ' cuotas de ' . $cuotasTotales]);
        }
        $montoAPagar = PagoCuotas::where('id_orden_servicio',$ordenServicio->id)->sum('monto_pagar');
        $montoPagado = PagoCuotas::where('id_orden_servicio',$ordenServicio->id)->sum('monto_pagado');
        if($cuotasTotales > 0 && $montoPagado < $montoAPagar){
            $tipoMoneda = $ordenServicio->tipoMoneda === 'USD' ? '$' : 'S/';
            return response()->json(['alerta' => 'Para generar el comprobante, la suma total de las cuotas debe ser igual o mayor a  ' . $tipoMoneda. ' ' .  number_format($montoAPagar,2) . ', actualmente se a pagado ' . $tipoMoneda. ' '. number_format($montoPagado,2)]);
        }
        $datos = [
            'comprobanteInterno' => 1,
            'comprobanteBoleta' => 1,
            'comprobanteFactura' => 1,
            'comprobanteExtranjero' => $ordenServicio->cliente->id_pais === 165 ? false : true,
            'nombreCliente' => $ordenServicio->cliente->nombreCliente,
            'direccionCliente' => $ordenServicio->cliente->usuario->direccion,
            'tipoDocumentoCliente' => !empty($ordenServicio->cliente->usuario->documento) ? $ordenServicio->cliente->usuario->documento->valor : null,
            'numeroDocumentoCliente' => $ordenServicio->cliente->usuario->nroDocumento
        ];
        if($ordenServicio->facturacion_externa === 1 || (!$datos['comprobanteExtranjero']) && !$ordenServicio->incluir_igv){
            $datos['comprobanteFactura'] = 0;
            $datos['comprobanteBoleta'] = 0;
        }
        if($datos['comprobanteExtranjero']){
            $datos['comprobanteBoleta'] = 0;
        }
        $detalleComprobante = $this->detalleComprobante($ordenServicio->id,$ordenServicio->incluir_igv,$ordenServicio->tipoMoneda);
        return response()->json(['comprobanteCliente' => $datos, 'comprobanteDetalle' => $detalleComprobante]);
    }
    public function detalleComprobante($idOrdenServicio,$incluirIgv,$moneda) {
        $serviciosOS = OrdenServicioCotizacionServicio::mostrarServiciosOrdenServicio($idOrdenServicio);
        $detalleTotal = OrdenServicioCotizacionProducto::mostrarProductosOrdenServicio($serviciosOS,$idOrdenServicio)->toArray();
        list($importeTotal,$igvTotal,$operacionGravada,$descuento) = [0,0,0,0];
        foreach ($detalleTotal as $detalle) {
            // dd($detalle);
            $operacionGravada += $detalle['total'];
            $descuento += $detalle['descuento'];
            if($incluirIgv){
                $igvTotal += $detalle['igv'];
            }
            $importeTotal += $detalle['total'] + $detalle['igv'];
        }
        // $importeTotal = $operacionGravada - $igvTotal;
        // dd($detalleTotal,$importeTotal);
        $montoPagado = PagoCuotas::where('id_orden_servicio',$idOrdenServicio)->sum('monto_pagado');
        if($montoPagado !== 0 && $montoPagado > $importeTotal){
            $adiconal = $montoPagado - $importeTotal;
            // dd($adiconal);
            $baseAdicional = $incluirIgv ? round($adiconal/1.18,2) : $adiconal;
            $igvAdicional = $incluirIgv ? round($baseAdicional * 0.18,2) : 0;
            $igvTotal += $igvAdicional;
            $operacionGravada += $baseAdicional;
            $importeTotal += $adiconal;
            $detalleTotal[] = [
                'idOsCotizacion' => 1,
                'cantidad' => 1,
                'descuento' => "0",
                'igv' => $igvAdicional,
                'importe' => $baseAdicional,
                'precio' => $baseAdicional,
                'tipoServicioProducto' => 'adicional',
                'servicio' => 'Costo adicional por pago en cuotas',
                'total' => $baseAdicional
            ];
        }
        // dd($detalleTotal);
        $letraNumero = (new RapiFac)->numeroAPalabras($importeTotal,$moneda);
        return ['detalle' => $detalleTotal,'descuentoTotal' => $descuento,'tipoMoneda' => $moneda, 'igvTotal' => $igvTotal, 'importeTotal' => $importeTotal, 'operacionGravada' => $operacionGravada,'letraImporteTotal' => $letraNumero];
    }
    public function agregarCuota(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $ordenServicio = ModelsOrdenServicio::find($request->ordenServicioId);
        $agregarCuota = $this->agregarCuotaOrdenServicio($request->numeroCuota,$ordenServicio->id);
        $listaCuotas = ['cuotas' => PagoCuotas::obtenerCuotasOrdenServicio($ordenServicio->id,$ordenServicio->tipoMoneda)];
        return response()->json(array_merge($agregarCuota,$listaCuotas));
    }
    public function eliminarComprobanteSunat($ordenServicio, $cuota){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $comprobante = PagoCuotas::where(['id_orden_servicio' => $ordenServicio,'id' => $cuota])->first();
        if(empty($comprobante)){
            return response()->json(['alerta' => 'No se encontro el comprobante para ser eliminado']);
        }
        if(!is_null($comprobante->comprobante_unico) && Storage::disk('pagoCuotasSunat')->exists($comprobante->comprobante_unico)){
            Storage::disk('pagoCuotasSunat')->delete($comprobante->comprobante_unico);
            $comprobante->update(['comprobante_unico' => null, 'comprobante_nombre' => null]);
            return response()->json(['success' => 'se elimino el comprobante externo de forma correcta']);
        }
        return response()->json(['alerta' => 'El comprobante externo no se encuentra almacenado en el sistema']);
    }
    public function eliminarCuota(ModelsOrdenServicio $ordenServicio, $cuota){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $comprobante = PagoCuotas::where(['id_orden_servicio' => $ordenServicio->id,'id' => $cuota])->first();
        if(empty($comprobante)){
            return response()->json(['alerta' => 'No se encontro el comprobante para ser eliminado']);
        }
        $imagenes = PagoCuotasImg::where(['id_pago_cuota' => $cuota])->get();
        DB::beginTransaction();
        try {
            foreach ($imagenes as $imagen) {
                if(!is_null($imagen->url_imagen) && Storage::disk('pagoCuotasImg')->exists($imagen->url_imagen)){
                    Storage::disk('pagoCuotasImg')->delete($imagen->url_imagen);
                }
                $imagen->delete();
            }
            if(!is_null($comprobante->comprobante_unico) && Storage::disk('pagoCuotasSunat')->exists($comprobante->comprobante_unico)){
                Storage::disk('pagoCuotasSunat')->delete($comprobante->comprobante_unico);
            }
            $comprobante->delete();
            $comporbantes = PagoCuotas::where(['id_orden_servicio' => $ordenServicio->id])->orderBy('nro_cuota','asc')->get();
            foreach ($comporbantes as $key => $comprobante) {
                $comprobante->update(['nro_cuota' => $key + 1]);
            }
            DB::commit();
            return response()->json(['success' => 'se elimino la cuota de forma correcta','cuotas' => PagoCuotas::obtenerCuotasOrdenServicio($ordenServicio->id,$ordenServicio->tipoMoneda)]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['alerta' => $th->getMessage()]);
        }
    }
    public function eliminarImagenCuola($cuota, $imagen){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $imagen = PagoCuotasImg::where(['id_pago_cuota' => $cuota,'id' => $imagen])->first();
        if(empty($imagen)){
            return response()->json(['alerta' => 'No se encontro la imagen para ser eliminada']);
        }
        if(!is_null($imagen->url_imagen) && Storage::disk('pagoCuotasImg')->exists($imagen->url_imagen)){
            Storage::disk('pagoCuotasImg')->delete($imagen->url_imagen);
            $imagen->delete();
            return response()->json(['success' => 'se elimino la imagen de forma correcta']);
        }
        return response()->json(['alerta' => 'La imagen no se encuentra almacenada en el sistema']);
    }
    public function verComprobanteSunat($ordenServicio,$idCuota){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route('home');
        }
        $cuota = PagoCuotas::where(['id' => $idCuota,'id_orden_servicio' => $ordenServicio])->first();
        if(empty($cuota) || is_null($cuota->comprobante_unico) || !Storage::disk('pagoCuotasSunat')->exists($cuota->comprobante_unico)){
            return abort(404);
        }
        $documento = Storage::disk('pagoCuotasSunat')->get($cuota->comprobante_unico);
        return response($documento,200,[
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $cuota->comprobante_nombre . '"',
        ]);
    }
    public function guardarImagenCuota(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $nombreArchivo = null;
        DB::beginTransaction();
        try {
            $img = PagoCuotasImg::create([
                'id_pago_cuota' => $request->cuota,
            ]);
            $extension = $request->file('imagen')->getClientOriginalExtension();
            $nombreArchivo = $request->cuota . '_' . $img->id . '_' .time() . '.' . $extension;
            $request->file('imagen')->storeAs('pagoCuotasImg',$nombreArchivo);
            $nombreOriginal = $request->file('imagen')->getClientOriginalName();
            $img->update(['url_imagen' => $nombreArchivo,'nombre_imagen' => $nombreOriginal]);
            DB::commit();
            return response()->json(['success' => 'imagen agregada correctamente', 'urlImagen' => $nombreArchivo, 'id' => $img->id, 'nombre' => $nombreOriginal]);
        } catch (\Throwable $th) {
            DB::rollBack();
            if(!is_null($nombreArchivo) && Storage::disk('pagoCuotasImg')->exists($nombreArchivo)){
                Storage::disk('pagoCuotasImg')->delete($nombreArchivo);
            }
            return response()->json(['alerta' => $th->getMessage()]);
        }
    }
    public function obtenerCuotas(ModelsOrdenServicio $ordenServicio){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(['cuotas' => PagoCuotas::obtenerCuotasOrdenServicio($ordenServicio->id,$ordenServicio->tipoMoneda),'facturacionExterna' => $ordenServicio->facturacion_externa]);
    }
    public function verComprobanteCuota(PagoCuotas $cuota) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route('home');
        }
        $configuracion = Configuracion::whereIn('descripcion',['direccion','razon_social_largo','ruc','razon_social'])->get();
        $numeroPago = str_pad($cuota->id,4,'0',STR_PAD_LEFT);
        $titulo = "CUOTA - " . $numeroPago;
        $strFechaPago = strtotime($cuota->fecha_pagada);
        $fechaTexto = (new Utilitarios)->obtenerFechaLargaSinDia($strFechaPago);
        $fechaFormato = date('d/m/Y',$strFechaPago);
        $nombreMonto = (new RapiFac)->numeroAPalabras($cuota->monto_pagado,$cuota->ordenServicio->tipoMoneda);
        return Pdf::loadView('ordenesServicio.reportes.pagoCuota',compact("cuota","titulo","configuracion","fechaFormato","fechaTexto","numeroPago","nombreMonto"))->setPaper('none')->stream($titulo . '.pdf');
    }
    public function obtenerCuota($ordenServicio,$cuota){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(['cuota' => PagoCuotas::obtenerCuota($ordenServicio,$cuota),'imagenesPagos' => PagoCuotasImg::obtenerImagenes($cuota)]);
    }
    public function reportesOrdenesServicios(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route('home');
        }
        $configuracion = Configuracion::whereIn('descripcion',['direccion','telefono','texto_datos_bancarios','red_social_facebook','red_social_instagram','red_social_tiktok','red_social_twitter'])->get();
        $fechaInicioReporte = date('d/m/Y',strtotime($request->fecha_inicio));
        $fechaFinReporte = date('d/m/Y',strtotime($request->fecha_fin));
        $ordenesServicios = ModelsOrdenServicio::misOrdeneseServicio($request->fecha_inicio,$request->fecha_fin,$request->cliente,$request->estado);
        $titulo = 'ordenes_servicios';
        $tituloPagos = 'pagos';
        if($request->has('exportarPdf')){
            return Pdf::loadView('ordenesServicio.reportes.osPDF',compact("ordenesServicios","configuracion","fechaInicioReporte","fechaFinReporte"))
            ->setPaper("A4","landscape")->stream($titulo.'.pdf');
        }else if($request->has('exportarExcel')){
            return Excel::download(new ExportOrdenesServicios($ordenesServicios,$fechaInicioReporte,$fechaFinReporte,'ordenesServicio.reportes.osEXCEL'),$titulo.'.xlsx');
        }else if($request->has('exportarPdfPagos')){
            return Pdf::loadView('ordenesServicio.reportes.pagosPDF',compact("ordenesServicios","configuracion","fechaInicioReporte","fechaFinReporte"))
            ->setPaper("A4","landscape")->stream($tituloPagos.'.pdf');
        }else if($request->has('exportarExcelPagos')){
            return Excel::download(new ExportPagos($ordenesServicios,$fechaInicioReporte,$fechaFinReporte,'ordenesServicio.reportes.pagosEXCEL'),$tituloPagos.'.xlsx');
        }
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
    public function generarComprobante(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $tiposComprobantes = ['Factura' => '01','Boleta' => '03', 'Comprobante' => '00'];
        $tipoComprobante = $tiposComprobantes[$request->modoFacturaChec];
        $rapifact = new RapiFac();
        $ordenServicio = ModelsOrdenServicio::find($request->ordenServicio);
        $comprobanteExtranjero = $ordenServicio->cliente->id_pais === 165 ? false : true;
        $datosGenerales = $request->except("modoFacturaChec","modoFacturaChec");
        $datosGenerales['tipoComprobante'] = $tipoComprobante;
        $detalleComprobante = $this->detalleComprobante($ordenServicio->id,!$comprobanteExtranjero ? true : false,$ordenServicio->tipoMoneda);
        DB::beginTransaction();
        try {
            if($tipoComprobante === '00'){
                $comprobanteInterno = ComprobanteInterno::create([
                    'fecha_emision' => $request->fechaEmision,
                    'tipo_moneda' => $detalleComprobante['tipoMoneda'],
                    'cliente' => $request->nombreCliente,
                    'tipo_documento' => TipoDocumento::where('valor',$request->tipoDocumentoCliente)->first()->documento,
                    'numero_documento' => $request->numeroDocumentoCliente,
                    'direccion' => $request->direccionCliente,
                    'observaciones' => $request->observaciones,
                    'subtotal' => $detalleComprobante['operacionGravada'] + $detalleComprobante['descuentoTotal'],
                    'descuento' => $detalleComprobante['descuentoTotal'],
                    'igv_total' => $detalleComprobante['igvTotal'],
                    'total' => $detalleComprobante['importeTotal'],
                    'monto_letras' => $detalleComprobante['letraImporteTotal']
                ]);
                foreach ($detalleComprobante['detalle'] as $detalle) {
                    ComprobanteInternoDetalle::create([
                        'id_comprobante_interno' => $comprobanteInterno->id,
                        'descripcion' => $detalle['servicio'],
                        'cantidad' => $detalle['cantidad'],
                        'precio' => $detalle['precio'],
                        'descuento' => $detalle['descuento'],
                        'igv' => $detalle['igv'],
                        'total' => $detalle['precio'] + $detalle['igv']
                    ]);
                }
                $comprobante = Comprobantes::create([
                    'id_os_servicio' => $ordenServicio->id,
                    'numero_comprobante' => 'GI001-'. str_pad($comprobanteInterno->id,4,'0',STR_PAD_LEFT),
                    'estado' => 1,
                    'tipo_moneda' => $ordenServicio->tipoMoneda,
                    'monto_total' => $detalleComprobante['importeTotal'],
                    'fecha_emision' => $request->fechaEmision,
                    'tipo_comprobante' => $tipoComprobante
                ]);
                $comprobanteInterno->update(['id_comprobante' => $comprobante->id]);
                DB::commit();
                return response()->json(['success' => 'Comprobante interno generado correctamente','urlPdf' => route('comprobante.interno',['comprobante' => $comprobante->id])]);
            }
            $respuestaComprobante = null;
            if(!$comprobanteExtranjero){
                $respuestaComprobante = $rapifact->generarComprobanteAgrabadoSUNAT($datosGenerales,$detalleComprobante['detalle'],$ordenServicio->tipoMoneda);
            }else{
                $respuestaComprobante = $rapifact->generarComprobanteExtrangeroSUNAT($datosGenerales,$detalleComprobante['detalle'],$ordenServicio->tipoMoneda);
            }
            if(isset($respuestaComprobante->cdr) && isset($respuestaComprobante->xml_pdf)){
                $numeroComprobante = explode("-",$respuestaComprobante->xml_pdf->Mensaje);
                Comprobantes::create([
                    'id_os_servicio' => $ordenServicio->id,
                    'id_comprobante_rapifac' => $respuestaComprobante->xml_pdf->IDComprobante,
                    'repositorio' => $respuestaComprobante->xml_pdf->IDRepositorio,
                    'numero_comprobante' => $numeroComprobante[1] . '-' . $numeroComprobante[2],
                    'estado' => 1,
                    'tipo_moneda' => $ordenServicio->tipoMoneda,
                    'monto_total' => $detalleComprobante['importeTotal'],
                    'fecha_emision' => $request->fechaEmision,
                    'tipo_comprobante' => $tipoComprobante
                ]);
                DB::commit();
                return response()->json(['success' => $respuestaComprobante->cdr->Mensaje,'urlPdf' => $rapifact->urlPdfComprobantes .'?key=' . $respuestaComprobante->xml_pdf->IDRepositorio]);
            }
            return response()->json(['error' => 'No se puede procesar el comprobante']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function eliminarOrdenServicio(ModelsOrdenServicio $ordenServicio){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        if($ordenServicio->comprobantes()->count() > 0){
            return response()->json(['alerta' => 'Para eliminar la orden de servicio debe primero anular los comprobantes y/o guías de remisión asociados a ella']);
        }
        $this->devolverEstadosCotizacion($ordenServicio->id);
        $ordenServicio->update(['estado' => -1]);
        return response()->json(['success' => 'orden de servicio eliminada correctamente']);
    }
    public function anularComprobanteInterno(Comprobantes $comprobante){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $comprobanteInterno = $comprobante->comprobanteInterno;
        if(empty($comprobanteInterno)){
            return response()->json(['alerta' => 'No se encontró el comprobante interno']);
        }
        $comprobante->update(['estado' => 0]);
        $comprobanteInterno->update(['estado' => 0]);
        return response()->json(['success' => 'comprobante interno anulado correctamente','comprobantesSunat' => $comprobante->ordenServicio->comprobantes, 'urlSunat' => (new RapiFac)->urlPdfComprobantes]);
    }
    public function comprobanteInterno(Comprobantes $comprobante) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $comprobanteInterno = $comprobante->comprobanteInterno;
        if(empty($comprobanteInterno)){
            return abort(404);
        }
        $configuracion = Configuracion::whereIn('descripcion',['direccion','razon_social_largo','ruc','razon_social'])->get();
        $numeroPago = str_pad($comprobanteInterno->id,4,'0',STR_PAD_LEFT);
        $titulo = "GUIA INTERNA - " . $numeroPago;
        $strFechaPago = strtotime($comprobanteInterno->fecha_emision);
        return Pdf::loadView('facturacion.comprobanteInterno',compact("comprobanteInterno","configuracion","numeroPago","titulo","strFechaPago"))->setPaper('A4','landscape')->stream($titulo.'.pdf');
    }
    public function obtenerCotizacionCliente($cliente,Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOSAgregar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(['detalleCotizacion' => Cotizacion::obtenerCotizacionesAprobadas($cliente,$request->tipoMoneda,$request->conIgv)]);
    }
    public function indexMisOs()  {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $firmasUsuarios = User::firmasHabilitadas();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $fechaFin = date('Y-m-d');
        $fechaInicio = date('Y-m-d',strtotime($fechaFin . ' - 90 days'));
        return view("ordenesServicio.misOrdenes",compact("modulos","clientes","fechaFin","fechaInicio","firmasUsuarios","tiposDocumentos"));
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
                            $costoServicio = $this->obtenerCostoServicio($servicio->id);
                            $datos = OrdenServicioCotizacionServicio::updateOrCreate([
                                'id_orden_servicio' => $request->idOrdenServicio,
                                'id_cotizacion_servicio' => $servicio->id,
                                'costo_total' => $costoServicio,
                                'orden' => $key + 1
                            ],['estado' => 1]);
                            $nombreDetalle = $servicio->servicios->servicio;
                            CotizacionServicio::find($servicio->id)->update(['estado' => 2]);
                        }else{
                            $costoProducto = $this->obtenerCostoProducto($servicio->id);
                            $datos = OrdenServicioCotizacionProducto::updateOrCreate([
                                'id_orden_servicio' => $request->idOrdenServicio,
                                'id_cotizacion_producto' => $servicio->id,
                                'costo_total' => $costoProducto,
                                'orden' => $key + 1
                            ],['estado' => 1]);
                            $nombreDetalle = $servicio->productos->nombreProducto;
                            CotizacionProductos::find($servicio->id)->update(['estado' => 2]);
                        }
                        $respuesta[] = [
                            'cantidad' => $servicio->cantidad,
                            'descuento' => $servicio->descuento,
                            'idCotizacionServicio' => $servicio->id,
                            'precio' => $servicio->precio,
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
    public function devolverEstadosCotizacion($idOrdenServicio) {
        $cotizacionesServicios = OrdenServicioCotizacionServicio::where('id_orden_servicio',$idOrdenServicio)->get();
        $cotizacionesProductos = OrdenServicioCotizacionProducto::where('id_orden_servicio',$idOrdenServicio)->get();
        foreach ($cotizacionesServicios as $cotizacionServicio) {
            $cotizacionServicio->cotizacionServicio()->update(['estado' => 1]);
            $cotizacionServicio->cotizacionServicio->cotizacion->update(['estado' => 2]);
        }
        foreach ($cotizacionesProductos as $cotizacionProducto) {
            $cotizacionProducto->cotizacionOsProductos()->update(['estado' => 1]);
            $cotizacionProducto->cotizacionOsProductos->cotizacion->update(['estado' => 2]);
        }
    }
    function actualizarMontosOrdenServicio($idOrdenServicio){
        $cotizacionesServicios = OrdenServicioCotizacionServicio::mostrarServiciosOrdenServicio($idOrdenServicio);
        $cotizaciones = OrdenServicioCotizacionProducto::mostrarProductosOrdenServicio($cotizacionesServicios,$idOrdenServicio);
        $calculosTotales = [
            'importe' => 0,
            'descuento' => 0,
            'igv' => 0,
            'adicional' => 0,
            'total' => 0,
            'costo_total' => 0,
            'gasto_caja' => $this->calcularGastosCajaChica($idOrdenServicio)
        ];
        foreach ($cotizaciones as $cotizacion) {
            $calculosTotales['importe'] += $cotizacion->importe;
            $calculosTotales['descuento'] += $cotizacion->descuento;
            $calculosTotales['igv'] += $cotizacion->igv;
            $calculosTotales['total'] += $cotizacion->total;
            $calculosTotales['costo_total'] += $cotizacion->costo_total;
        }
        $adicionales = OrdenServicioAdicional::where('id_orden_servicio',$idOrdenServicio)->get();
        foreach ($adicionales as $adicional) {
            $calculosTotales['adicional'] += $adicional->total;
        }
        $calculosTotales['total'] = $calculosTotales['total'] + $calculosTotales['igv'];
        $calculosTotales['utilidad'] = $calculosTotales['total'] - $calculosTotales['gasto_caja'] - $calculosTotales['adicional'] - $calculosTotales['costo_total'];
        ModelsOrdenServicio::find($idOrdenServicio)->update($calculosTotales);
    }
    public function obtenerOrdenServicio(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $ordenesServicios = ModelsOrdenServicio::misOrdeneseServicio($request->fecha_inicio,$request->fecha_fin,$request->cliente,$request->estado);
        return DataTables::of($ordenesServicios)->toJson();
    }
    public function reporteEntregaActa(EntregaActa $entregaActa) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        if($entregaActa->estado === 0){
            return abort(404); 
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
            $entregaActa = EntregaActa::create(['id_orden_servicio' => $ordenServicio->id,'estado' => 0]);
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
            $nombreArchivo = 'firma_' . time() . '.jpeg';
            $decoded_image = base64_decode($encoded_image);
            file_put_contents(storage_path('/app/firmaEntregaActas/'.$nombreArchivo), $decoded_image);
            $rutaOriginal = storage_path('app/firmaEntregaActas/'.$nombreArchivo);
            //Proceso para cortar una imagen
            $imagen = Image::make($rutaOriginal);
            $imagenRecortada = $imagen->trim();
            $nombreArchivoCortado = 'firma_cortado_' . (time() + 1) . '.jpeg';
            $rutaRecortada = storage_path('/app/firmaEntregaActas/'.$nombreArchivoCortado);
            $imagenRecortada->save($rutaRecortada);
            $datos['firma_representante'] = $nombreArchivo;
            $datos['firma_representante_cortado'] = $nombreArchivoCortado;
            $datos['estado'] = 1;
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
        $ordenServicio->listaServicios = Cotizacion::obtenerCotizacionesAprobadas($ordenServicio->id_cliente,$ordenServicio->tipoMoneda,$ordenServicio->incluir_igv,true);
        $ordenServicio->nombreCliente = $ordenServicio->cliente->nombreCliente;
        return response()->json(['ordenServicio' => $ordenServicio->makeHidden("cliente","fechaActualizada","fechaCreada")]);
    }
    public function obtenerCostoServicio($idCotizacionServicio){
        $cotizacionServicio = CotizacionServicio::find($idCotizacionServicio);
        $productos = $cotizacionServicio->productos;
        $costoTotal = 0;
        foreach ($productos as $producto) {
            $costoCompra = $producto->producto->precioCompra;
            $costoTotal += $producto->cantidad * $costoCompra;
        }
        return $costoTotal;
    }
    public function obtenerCostoProducto($idCotizacionProducto){
        $cotizacionProducto = CotizacionProductos::find($idCotizacionProducto);
        $costoCompra = $cotizacionProducto->productos->precioCompra;
        return $cotizacionProducto->cantidad * $costoCompra;
    }
    public function misComprobantes(ModelsOrdenServicio $ordenServicio){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloOsMostrar);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(['comprobantesSunat' => $ordenServicio->comprobantes, 'urlSunat' => (new RapiFac)->urlPdfComprobantes]);
    }
    public function calcularGastosCajaChica($idOrdenServicio) {
        $ordenServicio = ModelsOrdenServicio::find($idOrdenServicio);
        $gastosCajaChica = 0;
        foreach ($ordenServicio->cajaChicaCostos as $costoCajaChica) {
            if($ordenServicio->tipoMoneda !== $costoCajaChica->tipo_moneda){
                $gastosCajaChica += $ordenServicio->tipoMoneda === 'PEN' ? round($costoCajaChica->monto_total * $costoCajaChica->tipo_cambio,2) : round($costoCajaChica->monto_total/$costoCajaChica->tipo_cambio,2);
            }else{
                $gastosCajaChica += $costoCajaChica->monto_total;
            }
        }
        return $gastosCajaChica;
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
            $ordenServicioDatos['incluir_igv'] = !$request->has('incluirIGV') ? false : ($request->incluirIGV === "0" ? false : true);
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
                'total' => 0,
                'costo_total' => 0,
                'utilidad' => 0
            ];
            foreach ($listaServiciosProductos as $key => $servicio) {
                $calculosTotales['importe'] += $servicio['total'];
                $calculosTotales['descuento'] += $servicio['descuento'];
                if ($ordenServicioDatos['incluir_igv']) {
                    $calculosTotales['igv'] += $servicio['total'] * 0.18;
                }
                if($servicio['tipoServicioProducto'] === "servicio"){
                    $costoServicio = $this->obtenerCostoServicio($servicio['idCotizacionServicio']);
                    OrdenServicioCotizacionServicio::create([
                        'id_orden_servicio' => $ordenServicio->id,
                        'id_cotizacion_servicio' => $servicio['idCotizacionServicio'],
                        'costo_total' => $costoServicio,
                        'orden' => $key + 1,
                        'estado' => 1
                    ]);
                    $calculosTotales['costo_total'] += $costoServicio;
                    CotizacionServicio::find($servicio['idCotizacionServicio'])->update(['estado' => 2]);
                    continue;
                }
                $costoProducto = $this->obtenerCostoProducto($servicio['idCotizacionServicio']);
                OrdenServicioCotizacionProducto::create([
                    'id_orden_servicio' => $ordenServicio->id,
                    'id_cotizacion_producto' => $servicio['idCotizacionServicio'],
                    'costo_total' => $costoProducto,
                    'orden' => $key + 1,
                    'estado' => 1
                ]);
                $calculosTotales['costo_total'] += $costoProducto;
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
            $calculosTotales['total'] = $calculosTotales['importe'] - $calculosTotales['descuento'] + $calculosTotales['igv'] - $calculosTotales['adicional'];
            $calculosTotales['utilidad'] = $calculosTotales['total'] - $calculosTotales['costo_total'];
            $ordenServicio->update($calculosTotales);
            DB::commit();
            return response()->json(['success' => 'Orden de servicio generada de manera correcta']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(),'line' => $th->getLine()],400);
        }
    }
}
