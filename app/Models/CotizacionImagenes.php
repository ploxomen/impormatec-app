<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionImagenes extends Model
{
    protected $table = 'cotizacion_pre_imagenes';
    protected $fillable = ['id_pre_cotizacion','url_imagen','descripcion','estado','nombre_original_imagen'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    
}
