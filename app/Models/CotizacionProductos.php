<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionProductos extends Model
{
    public $table = "cotizacion_productos";
    protected $fillable = ['id_cotizacion','id_producto','fecha_fin_garantia','id_almacen','orden','cantidad','precio','importe','descuento','igv','total','estado'];
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

    public function scopeProductosCotizacionAprobar($query,$idCotizacion,$tangibles = null){
        $condicionales = ['id_cotizacion' => $idCotizacion];
        if(!is_null($tangibles)){
            $condicionales['esIntangible'] = $tangibles;
        }
        $productos = $query->select("cotizacion_productos.id","productos.nombreProducto","cotizacion_productos.id_almacen AS idAlmacen","cotizacion_productos.cantidad AS cantidadUsada","productos.urlImagen","productos.id AS idProducto")
        ->join("productos","cotizacion_productos.id_producto","=","productos.id")
        ->where($condicionales)->get();
        foreach ($productos as $producto) {
            $producto->listaAlmacenes = ProductoAlmacen::obtenerAlmacenProducto($producto->idProducto);
        }
        return $productos;
    }
    public static function productosServicios($servicios,$idCotizacion) {
        return CotizacionProductos::select("cotizacion_productos.id","cotizacion_productos.id_cotizacion","cotizacion_productos.id_producto","cotizacion_productos.orden","cotizacion_productos.precio","cotizacion_productos.cantidad","cotizacion_productos.importe","cotizacion_productos.descuento","cotizacion_productos.igv","cotizacion_productos.total","productos.nombreProducto AS nombreDescripcion","cotizacion_productos.estado")->selectRaw("'producto' AS tipo,null AS detalleProductos")
        ->join("productos","cotizacion_productos.id_producto","=","productos.id")
        ->where('id_cotizacion',$idCotizacion)->union($servicios)->orderBy("orden")->get();
    }
}
