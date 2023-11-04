<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    public $table = "tecnicos";
    const UPDATED_AT = "fechaActualizada";
    const CREATED_AT = "fechaCreada";
    protected $fillable = ['idUsuario'];
    public function scopeObtenerTecnicosActivos($query)
    {
        return $query->select("usuarios.nombres","usuarios.apellidos","tecnicos.id")->join("usuarios","usuarios.id","=","tecnicos.idUsuario")->where('tecnicos.estado',1)->get();
    }
    public function usuario()
    {
        return $this->belongsTo(User::class,'idUsuario');
    }
}
