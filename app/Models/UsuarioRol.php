<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioRol extends Model
{
    protected $table = 'usuario_rol';
    const UPDATED_AT = "fechaActualizada";
    const CREATED_AT = "fechaCreada";
    protected $fillable = ['usuarioFk','rolFk'];

   
}