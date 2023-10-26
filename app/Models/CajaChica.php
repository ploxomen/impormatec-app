<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaChica extends Model
{
    public $table = "caja_chica";
    protected $fillable = ['fecha_inicio','fecha_fin','tipo_moneda','monto_abonado','monto_gastado','responsable_caja','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    
    public function reponsable()
    {
        return $this->belongsTo(User::class,'responsable_caja');
    }
}
