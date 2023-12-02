<?php

namespace App\Http\Controllers;

use App\Models\Comprobantes;
use Illuminate\Http\Request;

class FacturacionElectronica extends Controller
{
    private $moduloFactura = "admin.comprobantes.sunat";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexFactura(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloFactura);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $hoy = date('Y-m-d');
        $modulos = $this->usuarioController->obtenerModulos();
        $rapifac = new RapiFac();
        $documento = $request->has('documento') ? $request->documento : "00";
        $desde = $request->has('desde') ? $request->desde : date('Y-m-d',strtotime($hoy . " - 90 days"));
        $hasta = $request->has('hasta') ? $request->hasta : $hoy;
        $busqueda = $request->has('busqueda') ? $request->busqueda : "";
        $pagina = $request->has('pagina') ? $request->pagina : "1";
        $facturas = $rapifac->listarComprobantes($documento,$desde,$hasta,$busqueda,$pagina);
        if($pagina > $facturas->TotalPaginas){
            $pagina = $facturas->TotalPaginas;
        }else if($pagina <= 0){
            $pagina = 1;
        }
        $urlPdfFactura = $rapifac->urlPdfComprobantes;
        return view("facturacion.factura",compact("modulos","facturas","urlPdfFactura","documento","desde","hasta","busqueda","pagina"));
    }
    function eliminarFacturaElectronica(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloFactura);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $rapiFac = new RapiFac();
        $datos = json_decode($request->comprobante);
        $anulacion = $rapiFac->anularComprobante($datos->id,$datos->codigoDocumento,$datos->serie,$datos->correlativo,$request->motivo,$datos->fecha);
        if(isset($anulacion['success'])){
            Comprobantes::where('id_comprobante_rapifac',$datos->id)->update(['estado' => 0]);
            return response()->json(['success' => 'Comprobante anulado correctamente']);
        }
        return response()->json(['error' => $anulacion['error']->message]);
    }
}
