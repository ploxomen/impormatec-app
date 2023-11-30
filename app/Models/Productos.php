<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Productos extends Model
{
    protected $table = 'productos';
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['nombreProducto','nombreProveedor','tipoMoneda','esIntangible','descripcion','stockMin','precioVenta','utilidad','precioCompra','urlImagen','estado'];
    
    public function almacenes()
    {
        return $this->belongsToMany(Almacen::class, 'productos_almacen', 'id_producto', 'id_almacen')->withPivot(['stock']);
    }
}
