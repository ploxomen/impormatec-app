<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionServicioProducto extends Model
{
    public $table = "cotizacion_servicio_productos";
    protected $fillable = ['id_cotizacion_servicio','id_almacen','id_producto','costo','cantidad','importe','descuento','total'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function producto() {
        return $this->belongsTo(Productos::class,'id_producto');
    }
    public function scopeObtenerProductosAprobar($query,$idServicio,$esIntangible){
        $condicional = $esIntangible ? ['productos.esIntangible' => 1] : ['productos.esIntangible' => 0];
        $condicional['cotizacion_servicio_productos.id_cotizacion_servicio'] = $idServicio;
        return $query->select("cotizacion_servicio_productos.id_producto AS idProducto","cotizacion_servicio_productos.cantidad AS cantidadUsada","cotizacion_servicio_productos.costo AS precioVenta","cotizacion_servicio_productos.descuento AS descuentoProducto","productos.nombreProducto","productos.urlImagen","cotizacion_servicio_productos.id_almacen as idAlmacen")
        ->join("productos","productos.id","=","cotizacion_servicio_productos.id_producto")
        ->where($condicional)->get();
    }
}
