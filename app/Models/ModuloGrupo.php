<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuloGrupo extends Model
{
    public $table = "modulo_grupo";
    public function modulos()
    {
        return $this->hasMany(Modulo::class, 'grupoFk');
    }
}
