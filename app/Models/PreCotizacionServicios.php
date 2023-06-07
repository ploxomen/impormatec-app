<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreCotizacionServicios extends Model
{
    public $table = "cotizacion_pre_servicios";
    protected $fillable = ['id_pre_cotizacion','id_servicios'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
