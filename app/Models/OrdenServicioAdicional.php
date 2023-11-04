<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenServicioAdicional extends Model
{
    public $table = "orden_servicio_adicional";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_orden_servicio','descripcion','precio','cantidad','total','estado'];
}
