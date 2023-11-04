<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionSeguimiento extends Model
{
    public $table = "cotizacion_seguimiento";
    protected $fillable = ['id_cotizacion','id_usuario','porcentaje','descripcion'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function usuario()
    {
        return $this->belongsTo(User::class,'id_usuario');
    }
}
