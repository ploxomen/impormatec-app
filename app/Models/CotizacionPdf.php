<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionPdf extends Model
{
    public $table = "cotizacion_pdf";
    protected $fillable = ['id_cotizacion','nombre_archivo','nombre_archivo_servidor','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
