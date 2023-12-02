<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenServicioCotizacionProducto extends Model
{
    public $table = "orden_servicio_cotizacion_producto";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_orden_servicio','id_cotizacion_producto','costo_total','orden','estado'];

    public static function mostrarProductosOrdenServicio($serviciosOS,$idOrdenServico) {
        return OrdenServicioCotizacionProducto::select("orden_servicio_cotizacion_producto.id AS idOsCotizacion","productos.nombreProducto AS servicio","cotizacion_productos.cantidad","cotizacion_productos.importe","orden_servicio_cotizacion_producto.costo_total","cotizacion_productos.descuento","cotizacion_productos.igv","cotizacion_productos.precio","cotizacion_productos.total","cotizacion_productos.id AS idCotizacionServicio","orden_servicio_cotizacion_producto.orden","orden_servicio_cotizacion_producto.id_cotizacion_producto AS idCotizacionDetalle")
        ->selectRaw("LPAD(cotizacion_productos.id_cotizacion,5,'0') AS nroCotizacion,'producto' AS tipoServicioProducto")
        ->join("cotizacion_productos","cotizacion_productos.id","=","orden_servicio_cotizacion_producto.id_cotizacion_producto")
        ->join("productos","cotizacion_productos.id_producto","=","productos.id")
        ->where('id_orden_servicio',$idOrdenServico)->union($serviciosOS)->orderBy("orden")->get();
    }
    public function cotizacionOsProductos() {
        return $this->belongsTo(CotizacionProductos::class,'idCotizacionDetalle');
    }
    //CASO EXCEPCIONAL YA QUE SE CONVINAN - SE USA EN EL PDF DE COTIZACION PARA EL DETALLE
    public function cotizacionOsServicios() {
        return $this->belongsTo(CotizacionServicio::class,'idCotizacionDetalle');
    }
}
