<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    public $table = "tipo_documento";
    protected $fillable = ['documento','valor','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    public function usuarios()
    {
        return $this->hasMany(User::class,'tipoDocumento');
    }
}