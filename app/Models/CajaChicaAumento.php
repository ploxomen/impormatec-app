<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaChicaAumento extends Model
{
    public $table = "caja_chica_aumentos";
    protected $fillable = ['id_caja_chica','fecha_deposito','banco','nro_operacion','monto_abonado','principal'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
