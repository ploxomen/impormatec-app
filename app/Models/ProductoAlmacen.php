<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoAlmacen extends Model
{
    protected $table = 'productos_almacen';
    public $timestamps = false;
    protected $fillable = ['id_producto','id_almacen','precioVenta','stock','estado'];

    public function scopeObtenerAlmacen($query,$idProducto){
        return $query->select("productos_almacen.id_almacen","productos_almacen.stock","productos_almacen.precioVenta","almacenes.nombre")->join("almacenes","almacenes.id","=","productos_almacen.id_almacen")
        ->where(['productos_almacen.estado' => 1, 'productos_almacen.id_producto' => $idProducto])->get();
    }
    public function scopeObtenerAlmacenProducto($query,$idProducto) {
        return $query->select("almacenes.id AS idAlmacen","productos_almacen.stock AS stockAlmacen","almacenes.nombre AS nombreAlmacen")
        ->join("almacenes","productos_almacen.id_almacen","=","almacenes.id")
        ->where(['productos_almacen.id_producto' => $idProducto, 'productos_almacen.estado' => 1])->get();
    }
}
