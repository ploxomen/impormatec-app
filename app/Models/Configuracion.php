<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    public $table = "configuracion";
    protected $fillable = ['descripcion', 'valor'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    
    public static function obtener() {
        return Configuracion::all();
    }
}
