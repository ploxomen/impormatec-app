<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioProducto extends Model
{
    protected $table = 'servicios_productos';
    public $timestamps = false;
    protected $fillable = ['id_producto','id_servicio','cantidadUsada','estado'];

    public function scopeObtenerProductos($query,$idServicio){
        return $query->select("servicios_productos.id_servicio","servicios_productos.cantidadUsada","productos.precioVenta","productos.nombreProducto","productos.id AS idProducto","productos.urlImagen")
        ->join("productos","productos.id","=","servicios_productos.id_producto")
        ->where(['servicios_productos.estado' => 1, 'servicios_productos.id_servicio' => $idServicio])->get();
    }
}
