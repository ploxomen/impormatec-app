<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreCotizaionTecnico extends Model
{
    public $table = "cotizacion_pre_tecnicos";
    protected $fillable = ['id_pre_cotizacion','id_tecnico','responsable'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
