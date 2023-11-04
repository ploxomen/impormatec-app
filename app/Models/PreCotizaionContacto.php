<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreCotizaionContacto extends Model
{
    public $table = "cotizacion_pre_contacto";
    protected $fillable = ['id_cotizacion_pre','id_cliente_contacto'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
