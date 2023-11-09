<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreCotizacionSeccionImagen extends Model
{
    protected $table = 'cotizacion_pre_secciones_img';
    protected $fillable = ['id_pre_cotizacion_seccion','url_imagen','descripcion','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
