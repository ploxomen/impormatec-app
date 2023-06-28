<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionServicioProducto extends Model
{
    public $table = "cotizacion_servicio_productos";
    protected $fillable = ['id_cotizacion_servicio','id_producto','costo','cantidad','importe','descuento','total'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
