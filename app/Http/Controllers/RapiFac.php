<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class RapiFac extends Controller
{
    private $urlRapifacAutenticacion = "https://wsoauth-exp.rapifac.com";
    private $urlRapifacEmision = "https://wsventas-exp.rapifac.com";
    private $urlRapifacComprobantes = "https://wscomprobante-exp.rapifac.com";
    private $urlAutenticacionPrueba;
    private $urlComprobante;
    private $urlComprobanteGuiaRemision;
    private $urlListaComprobantes;
    private $urlAnularComprobante;
    public $urlPdfComprobantes;
    public function __construct() {
        if(env('API_RAPIFAC_PRODUCTION') == 'true'){
            $this->urlRapifacAutenticacion = 'https://wsoauth.rapifac.com';
            $this->urlRapifacEmision = 'https://wsventas.rapifac.com';
            $this->urlRapifacComprobantes = 'https://wscomprobante.rapifac.com';
        }
        $this->urlAutenticacionPrueba = $this->urlRapifacAutenticacion . '/oauth2/token';
        $this->urlComprobante = $this->urlRapifacEmision . '/v0/comprobantes?IncluirCDR=1';
        $this->urlComprobanteGuiaRemision = $this->urlRapifacEmision . '/v0/comprobantes';
        $this->urlListaComprobantes = $this->urlRapifacEmision . '/v0/comprobantes';
        $this->urlAnularComprobante = $this->urlRapifacEmision . '/v0/comprobantes/anular?IncluirCDR=1';
        $this->urlPdfComprobantes = $this->urlRapifacComprobantes . '/v0/comprobantes/pdf';
    }
    function obtenerToken()  {
        $cliente = new Client();
        $cabeceras = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $parametros = [
                'username' => env('API_RAPIFAC_USERNAME'),
                'password' => env('API_RAPIFAC_PASSWORD'),
                'client_id' => env('API_RAPIFAC_CLIENT_ID'),
                'grant_type' => 'password'
        ];
        $response = $cliente->get($this->urlAutenticacionPrueba,[
            'headers' => $cabeceras,
            'form_params' => $parametros
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    function refrescarToken($token){
        $cliente = new Client();
        $cabeceras = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $parametros = [
            'client_id' => env('API_RAPIFAC_CLIENT_ID'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $token
        ];
        $response = $cliente->get($this->urlAutenticacionPrueba,[
            'headers' => $cabeceras,
            'form_params' => $parametros
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    function listaDetallesGuiaRemision($detalles){
        $nuevoDetalle = [];
        foreach ($detalles as $key => $detalle){
            $nuevoDetalle[] = [
                "Cantidad" => $detalle['cantidad'],
                "CantidadReferencial" => $detalle['cantidad'],
                "CantidadUnidadMedida" => 1,
                "Cargo" => 0,
                "CargoCargoCodigo" => "",
                "CargoIndicador" => 0,
                "CargoItem" => 0,
                "CargoNeto" => 0,
                "CargoPorcentaje" => 0,
                "CargoTotal" => 0,
                "CodigoCategoria" => 0,
                "ComprobanteID" => 0,
                "Control" => 0,
                "Descripcion" => $detalle['descripcion'],
                "Descuento" => 0,
                "DescuentoBase" => 0,
                "DescuentoCargo" => 0,
                "DescuentoCargoCodigo" => "00",
                "DescuentoCargoGravado" => 0,
                "DescuentoGlobal" => 0,
                "DescuentoIndicador" => 0,
                "DescuentoMonto" => 0,
                "DescuentoPorcentaje" => 0,
                "EsAnticipo" => false,
                "Ganancia" => 0,
                "ICBPER" => 0,
                "ICBPERItem" => 0,
                "ICBPERSubTotal" => 0,
                "ID" => 0,
                "IGV" => 0,
                "IGVNeto" => 0,
                "ISC" => 0,
                "ISCMonto" => 0,
                "ISCNeto" => 0,
                "ISCPorcentaje" => 0,
                "ISCUnitario" => 0,
                "Importado" => false,
                "ImporteTotal" => 0,
                "ImporteTotalReferencia" => 1,
                "Item" => $key + 1,
                "MontoTributo" => 0,
                "Observacion" => "",
                "Peso" => 0,
                "PesoTotal" => 0,
                "Peso_BASE" => 0,
                "PrecioUnitario" => 0,
                "PrecioUnitarioItem" => 0,
                "PrecioUnitarioNeto" => 0,
                "PrecioVenta" => 0,
                "PrecioVentaCodigo" => "01",
                "ProductoCodigo" => 'P000'.($key + 1),
                "ProductoCodigoCliente" => 'P000'.($key + 1),
                "ProductoCodigoSUNAT" => "",
                "TipoAfectacionIGVCodigo" => "10",
                "TipoProductoCodigo" => "1",
                "TipoSistemaISCCodigo" => "00",
                "UnidadMedidaCodigo" => $detalle['unidad'],
                "ValorUnitario" => 0,
                "ValorUnitarioNeto" => 0,
                "ValorVenta" => 0,
                "ValorVentaItem" => 0,
                "ValorVentaItemXML" => 0,
                "ValorVentaNeto" => 0,
                "ValorVentaNetoXML" => 0
            ];
        }
        return $nuevoDetalle;
    }
    function generarComprobanteExtrangeroSUNAT($datosGenerales,$detalleComprobante,$tipoMoneda){
        list($detalles,$montoTotal) = $this->detalleComprobanteExtrangeroSUNAT($detalleComprobante);
        $cretido = !isset($datosGenerales['tipoFactura']) ? 'Contado' : $datosGenerales['tipoFactura'];
        $fechaEmision = date('d/m/Y',strtotime($datosGenerales['fechaEmision']));
        $parametros = [
            "CargoGlobalMonto" => 0,
            "CargoGlobalMontoBase" => $montoTotal,
            "ClienteDireccion" => empty($datosGenerales['direccionCliente']) ? '' : $datosGenerales['direccionCliente'],
            "ClienteNombreRazonSocial" => $datosGenerales['nombreCliente'],
            "ClienteNumeroDocIdentidad" => $datosGenerales['numeroDocumentoCliente'],
            "ClientePaisDocEmisor" => "US",
            "ClienteTipoDocIdentidadCodigo" => $datosGenerales['tipoDocumentoCliente'],
            "CondicionPago" => $cretido,
            "Correlativo" => 2999,
            "CorrelativoModificado" => "",
            "CorreoElectronicoPrincipal" => "jeanpi.jpct@gmail.com",
            "CreditoTotal" => $cretido === 'Credito' ? $montoTotal : 0,
            "DescuentoGlobal" => 0,
            "DescuentoGlobalMontoBase" => 0,
            "DescuentoGlobalNGMonto" => 0,
            "DescuentoGlobalNGMontoBase" => $montoTotal,
            "DescuentoGlobalPorcentaje" => 0,
            "DescuentoGlobalValor" => 0,
            "Exonerada" => 0,
            "ExoneradaXML" => 0,
            "Exportacion" => $montoTotal,
            "ExportacionXML" => $montoTotal,
            "FechaConsumo" => $fechaEmision,
            "FechaEmision" => $fechaEmision,
            "FechaIngresoEstablecimiento" => $fechaEmision,
            "FechaIngresoPais" => $fechaEmision,
            "Gratuito" => 0,
            "GratuitoGravado" => 0,
            "Gravado" => 0,
            "ICBPER" => 0,
            "ID" => 0,
            "IGV" => 0,
            "IGVPorcentaje" => 18,
            "ISC" => 0,
            "ISCBase" => 0,
            "IdRepositorio" => 0,
            "ImporteTotalTexto" => $this->numeroAPalabras($montoTotal,$tipoMoneda),
            "ImpuestoTotal" => 0,
            "ImpuestoVarios" => 0,
            "Inafecto" => 0,
            "InafectoXML" => 0,
            "ListaDetalles" => $detalles,
            "ListaMovimientos" => [],
            "MonedaCodigo" => $tipoMoneda,
            "Observacion" => empty($datosGenerales['observaciones']) ? '' : $datosGenerales['observaciones'],
            "OperacionNoGravada" => $montoTotal,
            "OrigenSistema" => 0,
            "PendientePago" => number_format($montoTotal,2),
            "Serie" => "E001",
            "SerieModificado" => "",
            "Sucursal" => env('API_RAPIFAC_SUCURSAL_ID'),
            "TipoCambio" => "3.919",
            "TipoDocumentoCodigo" => "01",
            "TipoDocumentoCodigoModificado" => "01",
            "TipoNotaCreditoCodigo" => "01",
            "TipoNotaDebitoCodigo" => "01",
            "TipoOperacionCodigo" => "0200",
            "TotalAnticipos" => 0,
            "TotalCuotas" => 0,
            "TotalDescuentos" => 0,
            "TotalImporteVenta" => $montoTotal,
            "TotalImporteVentaCelular" => $montoTotal,
            "TotalImporteVentaReferencia" => 0,
            "TotalOtrosCargos" => 0,
            "TotalPago" => $montoTotal,
            "TotalPrecioVenta" => $montoTotal,
            "TotalRetencion" => 0,
            "TotalValorVenta" => $montoTotal,
            "Ubigeo" => "",
            "Usuario" => env('API_RAPIFAC_USER'),
            "Vendedor" => env('API_RAPIFAC_USER'),
            "VendedorNombre" => Auth::user()->nombres
        ];
        if(isset($datosGenerales['cuotasFacturaFecha']) && $datosGenerales['tipoFactura'] === 'Credito' && $datosGenerales['tipoComprobante'] == '01'){
            $listaCuotas = $this->cuotasComprobantes($datosGenerales['cuotasFacturaFecha'],$datosGenerales['cuotasFacturaMonto'],$datosGenerales['fechaEmision']);
            $parametros['ListaCuotas'] = $listaCuotas;
            $parametros['PermitirCuotas'] = count($listaCuotas);
        }
        try {
            $token = $this->obtenerToken();
            $client = new Client();
            $headers = [
                'Authorization' => 'bearer ' . $token->access_token,
                'Content-Type' => 'application/json'
            ];
            $body = json_encode($parametros);
            dd($body);
            $response = $client->post($this->urlComprobante,[
                'headers' => $headers,
                'body' => $body
            ]);
            $data = $response->getBody()->getContents();
            $nuevaData = json_decode($data);
            $nuevaData->MontoTotal = $montoTotal;
            return $nuevaData;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $e->getMessage();
        }
    }
    function detalleComprobanteExtrangeroSUNAT($detallesComprobante) {
        $detalles = [];
        $total = 0;
        foreach ($detallesComprobante as $key => $detalle) {
            $detalles[] = [
                "Cantidad"=> $detalle['cantidad'],
                "CantidadUnidadMedida"=> 1,
                "Cargo"=> 0,
                "CargoCargoCodigo"=> 0,
                "CargoIndicador"=> 0,
                "CargoItem"=> 0,
                "CargoNeto"=> 0,
                "CargoPorcentaje"=> 0,
                "CargoTotal"=> 0,
                "CodigoCategoria"=> 0,
                "ComprobanteID"=> 0,
                'Descuento' => $detalle['descuento'],
                "Descripcion"=> $detalle['servicio'],
                "DescuentoBase"=> $detalle['total'],
                'DescuentoMonto' => $detalle['descuento'],
                'DescuentoPorcentaje' => round(($detalle['descuento']/$detalle['importe'])*100,2),
                "DescuentoCargoCodigo"=> "01",
                "DescuentoIndicador"=> 1,
                "ICBPER"=> 0,
                "ICBPERItem"=> 0,
                "ICBPERSubTotal"=> 0,
                "ID"=> 0,
                "IGV"=> 0,
                "IGVNeto"=> 0,
                "ISC"=> 0,
                "ISCMonto"=> 0,
                "ISCNeto"=> 0,
                "ISCPorcentaje"=> 0,
                "ISCUnitario"=> 0,
                "ProductoCodigo" => mb_strtoupper(substr($detalle['tipoServicioProducto'],0,1)) . $detalle['idOsCotizacion'],
                "ProductoCodigoSUNAT" => "",
                "ImporteTotal"=> $detalle['total'],
                "Item"=> $key + 1,
                "MontoTributo"=> 0,
                "Observacion"=> "",
                "PrecioUnitario"=> $detalle['precio'],
                "PrecioUnitarioItem"=> $detalle['precio'],
                'PrecioUnitarioNeto' => round($detalle['total']/$detalle['cantidad'],2),
                "PrecioVenta"=> $detalle['total'],
                "PrecioVentaCodigo"=> "01",
                "ProductoCodigoSUNAT"=> "",
                "TipoAfectacionIGVCodigo"=> "40",
                "TipoProductoCodigo"=> "",
                "TipoSistemaISCCodigo"=> "00",
                "UnidadMedidaCodigo"=> 'NIU',
                "ValorUnitario"=> $detalle['precio'],
                "ValorUnitarioNeto"=> $detalle['precio'],
                "ValorVenta"=> $detalle['importe'],
                "ValorVentaItem"=> $detalle['total'],
                "ValorVentaItemXML"=> $detalle['total'],
                "ValorVentaNeto"=> $detalle['total'],
                "ValorVentaNetoXML"=> $detalle['total']
            ];
            $total += $detalle['total'];
        }
        return [$detalles,$total];
    }
    function numeroAPalabras($numero,$moneda) {
        $fmt = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        // Convierte el número en palabras
        $palabras = $fmt->format($numero);
        if(strpos($palabras,"coma") !== false){
            $palabras = substr($palabras,0,strpos($palabras,"coma") - 1);
        }
        // Si hay decimales, agregarlos
        $decimal = "00";
        if (strpos($numero, '.') !== false) {
            list($entero, $decimal) = explode('.', $numero);
            $decimal = ltrim($decimal, '0'); // Eliminar ceros a la izquierda
        }
        $tiposMonedas = ['PEN' => 'SOLES','USD' => 'DOLARES'];
        $palabras .= ' Y ' . $decimal . '/100 ' . $tiposMonedas[$moneda];
        return mb_strtoupper($palabras,'UTF-8');
    }
    function listarComprobantes($documento = "00",$desde,$hasta,$busqueda="",$pagina) {
        $client = new Client();
        $token = $this->obtenerToken();
        $headers = [
            'Authorization' => 'bearer ' . $token->access_token
        ];
        $urlPost = $this->urlListaComprobantes . "?documento=" . $documento . "&desde=" . date('d/m/Y',strtotime($desde)) . '&hasta='. date('d/m/Y',strtotime($hasta)) . '&busqueda='.$busqueda . '&pagina='. $pagina .'&sucursal=' . env('API_RAPIFAC_SUCURSAL_ID') . '&usuario=' . env('API_RAPIFAC_USER');
        $response = $client->get($urlPost,[
            'headers' => $headers,
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    function recuperarComprobante($idDocumento,$tipoDocumento,$serie,$correlativo){
        $client = new Client();
        $token = $this->obtenerToken();
        $headers = [
            'Authorization' => 'bearer ' . $token->access_token,
            'Content-Type' => 'application/json'
        ];
        $parametros = [
            'Id' => $idDocumento,
            'TipoDocumento' => $tipoDocumento,
            'Serie' =>  $serie,
            'Correlativo' => $correlativo,
            'Sucursal' => env('API_RAPIFAC_SUCURSAL_ID'),
            'Usuario' =>  env('API_RAPIFAC_USER'),
        ];
        $body = json_encode($parametros);
        $url = $this->urlListaComprobantes . "?Id=" . $idDocumento . "&TipoDocumento=" . $tipoDocumento . "&Serie=" . $serie ."&Correlativo=" . $correlativo .'&Sucursal=' . env('API_RAPIFAC_SUCURSAL_ID') . '&Usuario=' . env('API_RAPIFAC_USER') . '&Detalles=1&Adicionales=1&Movimientos=0';
        $response = $client->get($url,[
            'headers' => $headers,
        ]);
        $data = $response->getBody()->getContents();
        dd(json_decode($data));
        
    }
    function anularComprobante($idDocumento,$tipoDocumento,$serie,$correlativo,$motivoBaja,$fecha) {
        $client = new Client();
        $token = $this->obtenerToken();
        $headers = [
            'Authorization' => 'bearer ' . $token->access_token,
            'Content-Type' => 'application/json'
        ];
        $parametros = [
            'Id' => $idDocumento,
            'TipoDocumentoCodigo' => $tipoDocumento,
            'Serie' =>  $serie,
            'Correlativo' => $correlativo,
            'FechaEmision' => $fecha,
            'MotivoBaja' => empty($motivoBaja) ? "Error en el registro de la factura" : $motivoBaja,
            "ListaDetalles" => [],
            "ListaMovimientos" => []
        ];
        $body = json_encode($parametros);
        try {
            $response = $client->put($this->urlAnularComprobante,[
                'headers' => $headers,
                'body' => $body
            ]);
            return ['success' => $response->getBody()->getContents()];
        } catch (\Throwable $th) {
            return ['error' => json_decode(explode("\n",$th->getMessage())[1])];
        }
    }
    function generarGuiaRemision($datosFactura,$productos){
        $detallesFacturacion = $this->listaDetallesGuiaRemision($productos);
        $parametros = [
            "AgentePercepcion" => false,
            "AlojamientoNombreRazonSocial" => "",
            "AlojamientoNumeroDocIdentidad" => "",
            "AlojamientoPaisDocEmisor" =>  "AF",
            "AlojamientoTipoDocIdentidadCodigo" => "1",
            "BienServicioCodigo" => "027",
            "Bultos" => count($detallesFacturacion),
            "BultosCelular" => count($detallesFacturacion),
            "ClienteDireccion" => $datosFactura['ClienteDireccion'],
            "ClienteNombreRazonSocial" => $datosFactura['ClienteNombreRazonSocial'],
            "ClienteNumeroDocIdentidad" => $datosFactura['ClienteNumeroDocIdentidad'],
            "ClientePaisDocEmisor" => "PE",
            "ClienteTelefono" => "",
            "ClienteTipoDocIdentidadCodigo" => "6",
            "ClienteTipoSunat" => 1,
            "ClienteUbigeo" => "150106",
            "ConductorLicencia" => $datosFactura['ConductorLicencia'],
            "ConductorNumeroDocIdentidad" => $datosFactura['ConductorNumeroDocIdentidad'],
            "ConductorTipoDocIdentidadCodigo" => $datosFactura['ConductorTipoDocIdentidadCodigo'],
            "Correlativo" => 144,
            "CorreoElectronicoPrincipal" => "no-send@rapifac.com",
            "CreditoTotal" => 0,
            "DUADAMCodigo" => "",
            "DescuentoGlobalMonto" => 0,
            "DescuentoNGMonto" => 0,
            "DiasPermanencia" => 0,
            "DireccionLlegada" => $datosFactura['DireccionLlegada'],
            "DireccionPartida" => $datosFactura['DireccionPartida'],
            "DocAdicionalCodigo" => 1,
            "DocAdicionalDetalle" => "",
            "ExoneradaXML" => 0,
            "Exportacion" => 0,
            "ExportacionXML" => 0,
            "FechaConsumo" => $datosFactura['FechaEmision'],
            "FechaEmision" => $datosFactura['FechaEmision'],
            "FechaIngresoEstablecimiento" => $datosFactura['FechaEmision'],
            "FechaIngresoPais" => $datosFactura['FechaEmision'],
            "FechaSalidaEstablecimiento" => "",
            "FechaTraslado" => $datosFactura['FechaTraslado'],
            "FormatoPDF" => 0,
            "GratuitoGravado" => 0,
            "Gravado" => 0,
            "GuiaNumero" => "",
            "ICBPER" => 0,
            "ID" => 0,
            "IGV" => 0,
            "IGVPorcentaje" => 18,
            "ISC" => 0,
            "ISCBase" => 0,
            "IdRepositorio" => 0,
            "ImporteTotalTexto" => "CERO CON 00/100 DOLARES",
            "ImpuestoTotal" => 0,
            "ImpuestoVarios" => 0,
            "Inafecto" => 0,
            "InafectoXML" => 0,
            "ListaDetalles" => $detallesFacturacion,
            "ListaDocumentosRelacionados" => isset($datosFactura['ListaDocumentosRelacionados']) ? $datosFactura['ListaDocumentosRelacionados'] : [],
            "ListaMovimientos" => [],
            "ModalidadTrasladoCodigo" => "02", //MODALIDAD DE TRASNPORTE PUBLICO 01 O PRIBADO 02
            "MonedaCodigo" => "USD",
            "MontoRetencion" => 0,
            "MotivoTrasladoCodigo" => $datosFactura['MotivoTrasladoCodigo'],
            // "MotivoTrasladoDescripcion" => "VENTA",
            "NOMBRE_UBIGEOLLEGADA" => $datosFactura['NOMBRE_UBIGEOLLEGADA'],
            "NOMBRE_UBIGEOPARTIDA" => $datosFactura['NOMBRE_UBIGEOPARTIDA'],
            "Observacion" => $datosFactura['Observacion'],
            "PaisResidencia" => "AF",
            "PendientePago" => "0.00",
            "PercepcionFactor" => 0,
            "PercepcionRegimen" => "",
            "PercepcionTotal" => 0,
            "PermitirCuotas" => 1,
            "Peso" => 0,
            "PesoTotal" => $datosFactura['PesoTotal'],
            "PesoTotalCelular" => $datosFactura['PesoTotal'],
            "RemitenteNombreRazonSocial" => "",
            "RemitenteNumeroDocIdentidad" => "",
            "RemitenteTipoDocIdentidadCodigo" => "",
            "RetencionPorcentaje" => 0,
            "Serie" => "T002",
            "SerieModificado" => "",
            "SituacionPagoCodigo" => 2,
            "Sucursal" => env('API_RAPIFAC_SUCURSAL_ID'),
            "TipoCambio" => "3.919",
            "TipoDocumentoCodigo" => "09",
            "TipoGuiaRemisionCodigo" => "",
            "TipoOperacionCodigo" => "0101",
            "TotalImporteVenta" => 0,
            "TotalImporteVentaReferencia" => 0,
            "TotalOtrosCargos" => 0,
            "TotalPago" => 0,
            "TotalPrecioVenta" => 0,
            "TotalValorVenta" => 0,
            //CAMBIAR ESTE VALOR CUANDO ES TRASNPORTE PUBLICO EL VALOR DE DE LA EMPRESA QUIEN TRANSPORTA, SI ES PRIVADO VA LOS DATOS DEL CONDUCTOR, EN ESTE CASO COMO ES PRIBADO VA LAS DEL CONDUCTOR 
            "TransportistaNombreRazonSocial" => $datosFactura['ConductorNombreApeCompleto'],
            // "TransportistaNombreRazonSocial2" => "",
            "TransportistaNumeroDocIdentidad" => $datosFactura['ConductorNumeroDocIdentidad'],
            // "TransportistaNumeroDocIdentidad2" => "",
            "TransportistaTipoDocIdentidadCodigo" => $datosFactura['ConductorTipoDocIdentidadCodigo'],
            // "TransportistaTipoDocIdentidadCodigo2" => "",
            "Ubigeo" => "150115",
            "UbigeoLlegada" => "070101",
            "UbigeoPartida" => "150115",
            "Usuario" => env('API_RAPIFAC_USER'),
            "VehiculoAutorizacion" => "",
            "VehiculoAutorizacion2" => "",
            "VehiculoCertificado" => $datosFactura['VehiculoCertificado'],
            "VehiculoCertificado2" => $datosFactura['VehiculoCertificado2'],
            "VehiculoConfiguracion" => "",
            "VehiculoConfiguracion2" => "",
            "VehiculoPlaca" => $datosFactura['VehiculoPlaca'],
            "VehiculoPlaca2" => $datosFactura['VehiculoPlaca2'],
            "VehiculoRegistrado" => "",
            "Vendedor" => env('API_RAPIFAC_USER'),
            "VendedorNombre" => $datosFactura['VendedorNombre']
        ];
        $token = $this->obtenerToken();
        $client = new Client();
        $headers = [
            'Authorization' => 'bearer ' . $token->access_token,
            'Content-Type' => 'application/json'
        ];
        $body = json_encode($parametros);
        // dd($body);
        $response = $client->post($this->urlComprobanteGuiaRemision,[
            'headers' => $headers,
            'body' => $body
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    function cuotasComprobantes($fechas,$montos,$fechaEmision){
        $detalleCuotas = [];
        $fecha1 = new DateTime($fechaEmision);
        for ($i=0; $i < count($fechas); $i++) { 
            $fecha2 = new DateTime($fechas[$i]);
            $direncia = $fecha2->diff($fecha1);
            $detalleCuotas[] = [
                'FechaVencimientoCuota' => date('d/m/Y',strtotime($fechas[$i])),
                'MontoCuota' => floatval($montos[$i]),
                'PlazoDiasCuota' => $direncia->days,
            ];
        }
        return $detalleCuotas;
    }
    function detalleComprobanteAgrabadoSUNAT($detallesComprobante){
        $detalles = [];
        $item = 0;
        $totalGeneral = 0;
        $decimales = 2;
        $impuestoTotal = 0;
        $totalDescuentos = 0;
        $descuentoMontoBase = 0;
        foreach ($detallesComprobante as $detalle) {
            $item++;
            // dd($detalle);
            $precioUnitario = round($detalle['precio'] * 1.18,$decimales);
            $valorUnitario = round($precioUnitario/1.18,$decimales);
            // dd($precioUnitario,$valorUnitario);
            $valorVenta = round($detalle['cantidad'] * $valorUnitario,$decimales);
            $porcentajeDescuento = round(($detalle['descuento']/$valorVenta)*100,$decimales);
            $valorVentaItem = round($valorVenta - $detalle['descuento'],$decimales);
            $igv = round($valorVentaItem*0.18,$decimales);
            $totalPrecioVentaItem = round($valorVentaItem + $igv,$decimales);
            $precioUnitarioNeto = round($totalPrecioVentaItem/$detalle['cantidad'],$decimales);
            $detalles[] = [
                'Item' => $item,
                "ProductoCodigo" => mb_strtoupper(substr($detalle['tipoServicioProducto'],0,1)) . $detalle['idOsCotizacion'],
                "ProductoCodigoSUNAT" => "",
                'TipoSistemaISCCodigo' => '00',
                'UnidadMedidaCodigo' => $detalle['tipoServicioProducto'] == 'servicio' ? 'ZZ' : 'NIU',
                'DescripcionUnidadMedida' => $detalle['tipoServicioProducto'] == 'servicio' ? 'SERVICIO' : 'BIEN',
                'PrecioUnitarioItem' => $precioUnitarioNeto,
                'PrecioVentaCodigo' => '01',
                'ICBPER' => 0,
                'DescuentoCargoCodigo' => '00',
                'Control' => '0',
                'ImporteTotalReferencia' => 0,
                'CantidadUnidadMedida' => 1,
                'CantidadReferencial' => 1,
                'PrecioUnitarioNeto' => $precioUnitarioNeto,
                'DescuentoGlobal' => 0,
                'Descuento' => $detalle['descuento'],
                'ValorUnitario' => $valorUnitario,
                'ValorUnitarioNeto' => $valorVentaItem,
                'ValorVentaItem' => $valorVentaItem,
                'ValorVentaItemXML' => $valorVentaItem,
                'ValorVentaNeto' => $valorVentaItem,
                'ValorVentaNetoXML' => 0,
                'ISCUnitario' => 0,
                'ISCNeto' => 0,
                'ISC' => 0,
                'IGV' => $igv,
                'ICBPERItem' => 0,
                'ICBPERSubTotal' => 0,
                'DescuentoBase' => $detalle['importe'],
                'PrecioVenta' => $totalPrecioVentaItem,
                'MontoTributo' => $igv,
                'ISCPorcentaje' => 0,
                'ISCMonto' => 0,
                'Descripcion' => $detalle['servicio'],
                'Observacion' => '',
                'Cantidad' => $detalle['cantidad'],
                'PrecioUnitario' => $precioUnitario,
                'DescuentoMonto' => $detalle['descuento'],
                'DescuentoPorcentaje' => $porcentajeDescuento,
                'TipoAfectacionIGVCodigo' => '10',
                'ValorVenta' => $valorVenta,
                'Ganancia' => '',
                'IGVNeto' => $igv,
                'ImporteTotal' => $totalPrecioVentaItem,
            ];
            $descuentoMontoBase += $valorVentaItem;
            $totalGeneral += $totalPrecioVentaItem;
            $impuestoTotal += $igv;
            $totalDescuentos += $detalle['descuento'];
        }
        // $totalSinIgv = round($totalGeneral - $impuestoTotal,$decimales);
        return [$detalles,$descuentoMontoBase,round($totalGeneral,$decimales),round($impuestoTotal,$decimales),$totalDescuentos];
    }
    function generarComprobanteAgrabadoSUNAT($datosGenerales,$detalleComprobante,$tipoMoneda){
        list($detalles,$descuentoMontoBase,$totalGeneral,$impuestoTotal,$totalDescuentos) = $this->detalleComprobanteAgrabadoSUNAT($detalleComprobante);
        $tipoFactura = !isset($datosGenerales['tipoFactura']) ? 'Contado' : $datosGenerales['tipoFactura'];
        $fechaEmision = date('d/m/Y',strtotime($datosGenerales['fechaEmision']));
        $parametros = [
            // "EsPrueba" => ""
            "Usuario" => env('API_RAPIFAC_USER'),
            "Sucursal" => env('API_RAPIFAC_SUCURSAL_ID'),
            "IGVPorcentaje" => 18,
            "CantidadDecimales" => 2,
            "CanalVenta" => 2,
            // "OrigenSistema" => 7,
            "Vendedor" => env('API_RAPIFAC_USER'),
            "CondicionPago" => $tipoFactura,
            "SituacionPagoCodigo" => 2,
            "Ubigeo" => "150135",
            "ClienteNumeroDocIdentidad" => $datosGenerales['numeroDocumentoCliente'],
            // "ClienteUbigeo" => "150135",
            "ClientePaisDocEmisor" => "PE",
            // "TotalDescuentos" => $totalDescuentos,
            "CorreoElectronicoSecundario" => "prueba@gmail.com",
            'ClienteTipoDocIdentidadCodigo' => $datosGenerales['tipoDocumentoCliente'],
            "FechaConsumo" => $fechaEmision,
            "ListaDetalles" => $detalles,
            "ImporteTotalTexto" => $this->numeroAPalabras($totalGeneral,$tipoMoneda),
            "DescuentoGlobalMontoBase" => $descuentoMontoBase,
            "CargoGlobalMontoBase" => $descuentoMontoBase,
            "TotalPrecioVenta" => $totalGeneral,
            "TotalValorVenta" => $descuentoMontoBase,
            "NOMBRE_UBIGEOLLEGADA" => " -  - ",
            "NOMBRE_UBIGEOPARTIDA" => "LIMA - LIMA - LA SAN MARTIN",
            "CONTADOR_CLICKEMITIR" => 1,
            "paginasFiltroProducto" => 1,
            "ClasePrecioCodigo" => 1,
            "TipoDocumentoCodigo" => $datosGenerales['tipoComprobante'], //cambiar a comodidad xd
            "Serie" => $datosGenerales['tipoComprobante'] === "03" ? "B001" : "F001",
            "Correlativo" => 30,
            "MonedaCodigo" => $tipoMoneda,
            "FechaEmision" => $fechaEmision,
            "TipoDocumentoCodigoModificado" => "01",
            "TipoNotaCreditoCodigo" => "01",
            "TipoNotaDebitoCodigo" => "01",
            "ListaMovimientos" => [],
            "TipoOperacionCodigo" => "0101",
            "FechaIngresoPais" => $fechaEmision,
            "FechaIngresoEstablecimiento" => $fechaEmision,
            "TipoCambio" => "3.919",
            "Observacion" => empty($datosGenerales['observaciones']) ? '' : $datosGenerales['observaciones'],
            "MotivoTrasladoCodigo" => "01",
            "ClienteNombreRazonSocial" => $datosGenerales['nombreCliente'],
            "ClienteDireccion" => empty($datosGenerales['direccionCliente']) ? '' : $datosGenerales['direccionCliente'],
            "CorreoElectronicoPrincipal" => "no-send@rapifac.com",
            "Gravado" => $descuentoMontoBase,
            "IGV" => $impuestoTotal,
            "ImpuestoTotal" => $impuestoTotal,
            "TotalImporteVenta" => $totalGeneral,
            "TotalImporteVentaCelular" => $totalGeneral,
            "TotalPago" => $totalGeneral,
            "PesoTotal" => 1,
            "PesoTotalCelular" => 1,
            "Bultos" => 1,
            "BultosCelular" => 1,
            "DocAdicionalCodigo" => 1,
        ];
        if(isset($datosGenerales['cuotasFacturaFecha']) && $tipoFactura === 'Credito' && $datosGenerales['tipoComprobante'] == '01'){
            $listaCuotas = $this->cuotasComprobantes($datosGenerales['cuotasFacturaFecha'],$datosGenerales['cuotasFacturaMonto'],$datosGenerales['fechaEmision']);
            $parametros['ListaCuotas'] = $listaCuotas;
            $parametros['PermitirCuotas'] = count($listaCuotas);
            $parametros['CreditoTotal'] = $totalGeneral;
            $parametros['TotalCuotas'] = $totalGeneral;
            $parametros["PendientePago"] = $totalGeneral;
        }
        if($datosGenerales['tipoComprobante'] == '01' && isset($datosGenerales['incluirDetraccion'])){
            $parametros['TipoOperacionCodigo'] = '1001';
            $parametros["DetraccionTipoOperacion"] = "01";
            $parametros["DetraccionMedioPago"] = "001";
            $parametros['BienServicioCodigo'] = $datosGenerales['BienServicioCodigo'];
            $parametros['DetraccionPorcentaje'] = intval($datosGenerales['DetraccionPorcentaje']);
            $parametros['DetraccionCuenta'] = $datosGenerales['DetraccionCuenta'];
            $parametros['Leyenda'] = "1";
            $parametros['Detraccion'] = round($datosGenerales['DetraccionPorcentaje'] / 100 * $totalGeneral);
            $parametros["PendientePago"] =  $totalGeneral - $parametros['Detraccion'];
        }
        try {
            $token = $this->obtenerToken();
            $client = new Client();
            $headers = [
                'Authorization' => 'bearer ' . $token->access_token,
                'Content-Type' => 'application/json'
            ];
            $body = json_encode($parametros);
            // dd($body);
            $response = $client->post($this->urlComprobante,[
                'headers' => $headers,
                'body' => $body
            ]);
            $data = $response->getBody()->getContents();
            $nuevaData = json_decode($data);
            return $nuevaData;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $e->getMessage();
        }
    }
}
