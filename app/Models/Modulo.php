<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    public $table = "modulo";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    public function roles()
    {
        return $this->belongsToMany(Rol::class,'modulo_roles','moduloFk','rolFk')->withTimestamps();
    }
    public function grupos()
    {
        return $this->belongsTo(ModuloGrupo::class, 'grupoFk');
    }
}
