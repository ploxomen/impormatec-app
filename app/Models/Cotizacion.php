<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    public $table = "cotizacion";
    protected $fillable = ['id_cliente','id_pre_cotizacion','fechaCotizacion','fechaFinCotizacion','conversionMoneda','textoNota','tipoMoneda','referencia','representanteCliente','direccionCliente','importeTotal','descuentoTotal','igvTotal','total','cotizadorUsuario','reportePreCotizacion','documento','reporteDetallado','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    
    public function scopeObtenerCotizacion($query){
        return $query->select("cotizacion.id","cotizacion.tipoMoneda","clientes.nombreCliente","cotizacion.importeTotal","cotizacion.descuentoTotal","cotizacion.igvTotal","cotizacion.total","cotizacion.estado")
        ->selectRaw("DATE_FORMAT(cotizacion.fechaCotizacion,'%d/%m/%Y') AS fechaCotizada,DATE_FORMAT(cotizacion.fechaFinCotizacion,'%d/%m/%Y') AS fechaFinCotizada,LPAD(cotizacion.id,5,'0') AS nroCotizacion,IF(cotizacion.id_pre_cotizacion IS NULL,'SIN PRE - COTIZACIÓN',LPAD(cotizacion.id_pre_cotizacion,5,'0')) AS nroPreCotizacion,CONCAT(usuarios.nombres,' ',usuarios.apellidos) AS atendidoPor")
        ->leftjoin("cotizacion_pre","cotizacion.id_pre_cotizacion","=","cotizacion_pre.id")
        ->join("clientes","cotizacion.id_cliente","=","clientes.id")
        ->join("usuarios","cotizacion.cotizadorUsuario","=","usuarios.id")
        ->get();
    }
    public function scopeObtenerServiciosProductos($query, $idCotizacion,$incluirAlmacen = true){
        $servicios = CotizacionServicio::select("cotizacion_servicios.id","servicios.servicio","cotizacion_servicios.id_servicio","cotizacion_servicios.precio","cotizacion_servicios.cantidad","cotizacion_servicios.descuento","cotizacion_servicios.total")
        ->join("servicios","cotizacion_servicios.id_servicio","=","servicios.id")
        ->where(['cotizacion_servicios.id_cotizacion' => $idCotizacion])->get();
        foreach ($servicios as $servicio) {
            $servicio->productos = CotizacionServicioProducto::obtenerProductosAprobar($servicio->id,false);
            if(!$incluirAlmacen){
                continue;
            }
            foreach ($servicio->productos as $producto) {
                $producto->listaAlmacenes = ProductoAlmacen::obtenerAlmacenProducto($producto->idProducto);
            }
        }
        return $servicios;
    }
    public function scopeObtenerCotizacionesAprobadas($query,$idCliente,$tipoMoneda,$soloCotizacion = false){
        $cotizaciones = $query->select("id","importeTotal","descuentoTotal","igvTotal","total")
        ->selectRaw("LPAD(id,5,'0') AS nroCotizacion")
        ->where(['id_cliente' => $idCliente,'tipoMoneda' => $tipoMoneda])
        ->whereIn('estado',[2,3])->get();
        if($soloCotizacion){
            return $cotizaciones;
        }
        foreach ($cotizaciones as $cotizacion) {
            $servicios = CotizacionServicio::mostrarServiciosConProductos($cotizacion->id);
            $cotizacion->detalleCotizacion = CotizacionProductos::productosServicios($servicios,$cotizacion->id)->where('estado',1);
        }
        return $cotizaciones;
    }
    public function cotizacionSerivicios()
    {
        return $this->hasMany(CotizacionServicio::class,'id_cotizacion');
    }
    public function cotizacionProductos()
    {
        return $this->hasMany(CotizacionProductos::class,'id_cotizacion');
    }
}
