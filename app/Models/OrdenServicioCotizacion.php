<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenServicioCotizacion extends Model
{
    public $table = "orden_servicio_cotizacion";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_orden_servicio','id_cotizacion_servicio','estado'];
}
