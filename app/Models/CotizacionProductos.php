<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionProductos extends Model
{
    public $table = "cotizacion_productos";
    protected $fillable = ['id_cotizacion','id_producto','orden','cantidad','precio','importe','descuento','igv','total','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
