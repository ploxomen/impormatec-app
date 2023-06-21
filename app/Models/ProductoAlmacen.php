<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoAlmacen extends Model
{
    protected $table = 'productos_almacen';
    public $timestamps = false;
    protected $fillable = ['id_producto','id_almacen','stock','estado'];

    public function scopeObtenerAlmacen($query,$idProducto){
        return $query->select("productos_almacen.id_almacen","productos_almacen.stock","almacenes.nombre")->join("almacenes","almacenes.id","=","productos_almacen.id_almacen")
        ->where(['productos_almacen.estado' => 1, 'productos_almacen.id_producto' => $idProducto])->get();
    }
}
