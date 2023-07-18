<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionServicio extends Model
{
    public $table = "cotizacion_servicio";
    protected $fillable = ['id_cotizacion','id_servicio','costo','cantidad','importe','descuento','igv','total'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function servicios() {
        return $this->belongsTo(Servicio::class,'id_servicio');
    }
    public function productos() {
        return $this->hasMany(CotizacionServicioProducto::class,'id_cotizacion_servicio');
    }
}
