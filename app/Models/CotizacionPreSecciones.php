<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionPreSecciones extends Model
{
    protected $table = 'cotizacion_pre_secciones';
    protected $fillable = ['id_pre_cotizacion','titulo','columnas','estado','nombre_original_imagen'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    public function imagenes() {
        return $this->hasMany(PreCotizacionSeccionImagen::class,'id_pre_cotizacion_seccion');
    }
}
