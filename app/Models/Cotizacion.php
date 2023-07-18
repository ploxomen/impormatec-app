<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    public $table = "cotizacion";
    protected $fillable = ['id_cliente','id_pre_cotizacion','fechaCotizacion','fechaFinCotizacion','tipoMoneda','referencia','representanteCliente','direccionCliente','importeTotal','descuentoTotal','igvTotal','total','cotizadorUsuario','reportePreCotizacion','documento','reporteDetallado','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    
    public function scopeObtenerCotizacion($query){
        return $query->select("cotizacion.id","clientes.nombreCliente","cotizacion.importeTotal","cotizacion.descuentoTotal","cotizacion.igvTotal","cotizacion.total","cotizacion.estado")
        ->selectRaw("DATE_FORMAT(cotizacion.fechaCotizacion,'%d/%m/%Y') AS fechaCotizada,DATE_FORMAT(cotizacion.fechaFinCotizacion,'%d/%m/%Y') AS fechaFinCotizada,LPAD(cotizacion.id,5,'0') AS nroCotizacion,IF(cotizacion.id_pre_cotizacion IS NULL,'SIN PRE - COTIZACIÓN',LPAD(cotizacion.id_pre_cotizacion,5,'0')) AS nroPreCotizacion,CONCAT(usuarios.nombres,' ',usuarios.apellidos) AS atendidoPor")
        ->leftjoin("cotizacion_pre","cotizacion.id_pre_cotizacion","=","cotizacion_pre.id")
        ->join("clientes","cotizacion.id_cliente","=","clientes.id")
        ->join("usuarios","cotizacion.cotizadorUsuario","=","usuarios.id")
        ->get();
    }
    public function scopeObtenerServiciosProductos($query, $idCotizacion){
        $servicios = CotizacionServicio::select("cotizacion_servicio.id","servicios.servicio")
        ->join("servicios","cotizacion_servicio.id_servicio","=","servicios.id")
        ->where(['cotizacion_servicio.id_cotizacion' => $idCotizacion])->get();
        foreach ($servicios as $servicio) {
            $servicio->productos = CotizacionServicioProducto::obtenerProductosAprobar($servicio->id);
            foreach ($servicio->productos as $producto) {
                $producto->listaAlmacenes = ProductoAlmacen::obtenerAlmacenProducto($producto->idProducto);
            }
        }
        return $servicios;
    }
    public function scopeObtenerCotizacionesAprobadas($query,$idCliente){
        return $query->select("cotizacion.id AS idCotizacion","servicios.servicio","cotizacion_servicio.cantidad","cotizacion_servicio.importe","cotizacion_servicio.descuento","cotizacion_servicio.igv","cotizacion_servicio.total")
        ->selectRaw("LPAD(cotizacion.id,5,'0') AS nroCotizacion")
        ->join("cotizacion_servicio","cotizacion.id","=","cotizacion_servicio.id_cotizacion")
        ->join("servicios","cotizacion_servicio.id_servicio","=","servicios.id")
        ->where(['cotizacion.id_cliente' => $idCliente,'cotizacion.estado' => 2])->get();
    }
}
