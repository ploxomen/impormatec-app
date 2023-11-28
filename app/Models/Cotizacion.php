<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cotizacion extends Model
{
    public $table = "cotizacion";
    protected $fillable = ['id_cliente','fecha_fin_garantia','id_pre_cotizacion','fechaCotizacion','fechaFinCotizacion','conversionMoneda','textoNota','tipoMoneda','referencia','representanteCliente','direccionCliente','importeTotal','descuentoTotal','igvTotal','total','mesesGarantia','incluirIGV','cotizadorUsuario','reportePreCotizacion','porcentaje_actual','documento','reporteDetallado','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    
    public function scopeObtenerCotizacion($query){
        return $query->select("cotizacion.id","cotizacion.tipoMoneda","porcentaje_actual","usuarios.celular","fechaCotizacion","fecha_fin_garantia","clientes.nombreCliente","cotizacion.importeTotal","cotizacion.descuentoTotal","cotizacion.igvTotal","cotizacion.total","cotizacion.estado")
        ->selectRaw("DATE_FORMAT(cotizacion.fechaCotizacion,'%d/%m/%Y') AS fechaCotizada,DATE_FORMAT(cotizacion.fecha_fin_garantia,'%d/%m/%Y') AS fechaFinGarantia,DATE_FORMAT(cotizacion.fechaFinCotizacion,'%d/%m/%Y') AS fechaFinCotizada,LPAD(cotizacion.id,5,'0') AS nroCotizacion,IF(cotizacion.id_pre_cotizacion IS NULL,'SIN PRE - COTIZACIÓN',LPAD(cotizacion.id_pre_cotizacion,5,'0')) AS nroPreCotizacion,CONCAT(usuarios.nombres,' ',usuarios.apellidos) AS atendidoPor")
        ->leftjoin("cotizacion_pre","cotizacion.id_pre_cotizacion","=","cotizacion_pre.id")
        ->join("clientes","cotizacion.id_cliente","=","clientes.id")
        ->join("usuarios","cotizacion.cotizadorUsuario","=","usuarios.id");
    }
    public static function obtenerGarantiasFechas($mes,$year,$cliente) {
        $productos = CotizacionProductos::select("cotizacion_productos.id","cotizacion_productos.fecha_fin_garantia","clientes.nombreCliente","nombreProducto","cotizacion_productos.cantidad")
        ->selectRaw("DATE_FORMAT(cotizacion_productos.fecha_fin_garantia,'%d/%m/%Y') AS fechaFinGarantia,LPAD(cotizacion.id,5,'0') AS nroCotizacion,LPAD(orden_servicio_cotizacion_producto.id_orden_servicio,5,'0') AS nroOs,'Producto' AS tipo")
        ->join("cotizacion","cotizacion.id","=","cotizacion_productos.id_cotizacion")
        ->join("productos","productos.id","=","cotizacion_productos.id_producto")
        ->join("clientes","cotizacion.id_cliente","=","clientes.id")
        ->leftjoin("orden_servicio_cotizacion_producto","orden_servicio_cotizacion_producto.id_cotizacion_producto","=","cotizacion_productos.id");
        if($cliente !== '0'){
            $productos = $productos->where('clientes.id',$cliente);
        }
        $productos = $productos->whereRaw("YEAR(cotizacion_productos.fecha_fin_garantia) = ?",[$year]);
        if($mes !== '0'){
            $productos = $productos->whereRaw('MONTH(cotizacion_productos.fecha_fin_garantia) = ?',[$mes]);
        }
        $productos = $productos->groupBy("cotizacion_productos.id");
        $servicios = OrdenServicioCotizacionServicio::select("orden_servicio_cotizacion_servicio.id","orden_servicio_cotizacion_servicio.fecha_fin_garantia","clientes.nombreCliente","servicio","cotizacion_servicios.cantidad")
        ->selectRaw("DATE_FORMAT(orden_servicio_cotizacion_servicio.fecha_fin_garantia,'%d/%m/%Y') AS fechaFinGarantia,LPAD(cotizacion.id,5,'0') AS nroCotizacion,LPAD(orden_servicio_cotizacion_servicio.id_orden_servicio,5,'0') AS nroOs,'Servicio' AS tipo")
        ->join("cotizacion_servicios","cotizacion_servicios.id","=","orden_servicio_cotizacion_servicio.id_cotizacion_servicio")
        ->join("cotizacion","cotizacion.id","=","cotizacion_servicios.id_cotizacion")
        ->join("servicios","servicios.id","=","cotizacion_servicios.id_servicio")
        ->join("clientes","cotizacion.id_cliente","=","clientes.id");
        if($cliente !== '0'){
            $servicios = $servicios->where('clientes.id',$cliente);
        }
        $servicios = $servicios->whereRaw("YEAR(orden_servicio_cotizacion_servicio.fecha_fin_garantia) = ?",[$year]);
        if($mes !== '0'){
            $servicios = $servicios->whereRaw('MONTH(orden_servicio_cotizacion_servicio.fecha_fin_garantia) = ?',[$mes]);
        }
        return $servicios->groupBy("orden_servicio_cotizacion_servicio.id")->union($productos)->get();
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
    public function scopeObtenerCotizacionesAprobadas($query,$idCliente,$tipoMoneda,$conIgv,$soloCotizacion = false){
        $cotizaciones = $query->select("id","importeTotal","descuentoTotal","igvTotal","total")
        ->selectRaw("LPAD(id,5,'0') AS nroCotizacion")
        ->where(['id_cliente' => $idCliente,'tipoMoneda' => $tipoMoneda,'incluirIgv' => $conIgv])
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
    public function representantes()
    {
        return $this->belongsTo(ClientesContactos::class,'representanteCliente');
    }
    public function cliente()
    {
        return $this->belongsTo(Clientes::class,'id_cliente');
    }
    public function cotizacionSerivicios()
    {
        return $this->hasMany(CotizacionServicio::class,'id_cotizacion');
    }
    public function cotizacionProductos()
    {
        return $this->hasMany(CotizacionProductos::class,'id_cotizacion');
    }
    public function historialSeguimientos()
    {
        return $this->hasMany(CotizacionSeguimiento::class,'id_cotizacion');
    }
}
