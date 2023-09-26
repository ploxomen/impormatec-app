<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionProductos extends Model
{
    public $table = "cotizacion_productos";
    protected $fillable = ['id_cotizacion','id_producto','orden','cantidad','precio','importe','descuento','igv','total','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function productos() {
        return $this->belongsTo(Productos::class,'id_producto');
    }
    //CASO EXCEPCIONAL YA QUE SE CONVINAN - SE USA EN EL PDF DE COTIZACION PARA EL DETALLE
    public function servicios() {
        return $this->belongsTo(Servicio::class,'id_producto');
    }
    public function productosCotizacion() {
        return $this->hasMany(CotizacionServicioProducto::class,'id_cotizacion_servicio','id');
    }
    public function scopeProductos($query,$idCotizacion,$estado = null){
        $condicionales = ['id_cotizacion' => $idCotizacion];
        return $query->select("cotizacion_productos.id","productos.nombreProducto","cotizacion_productos.cantidad","productos.urlImagen")
        ->join("productos","cotizacion_productos.id_producto","=","productos.id")
        ->where($condicionales)->get();
    }
    public static function productosServicios($servicios,$idCotizacion) {
        return CotizacionProductos::select("cotizacion_productos.id","cotizacion_productos.id_cotizacion","cotizacion_productos.id_producto","cotizacion_productos.orden","cotizacion_productos.precio","cotizacion_productos.cantidad","cotizacion_productos.importe","cotizacion_productos.descuento","cotizacion_productos.igv","cotizacion_productos.total","productos.nombreProducto AS nombreDescripcion")->selectRaw("'producto' AS tipo,null AS detalleProductos")
        ->join("productos","cotizacion_productos.id_producto","=","productos.id")
        ->where('id_cotizacion',$idCotizacion)->union($servicios)->orderBy("orden")->get();
    }
}
