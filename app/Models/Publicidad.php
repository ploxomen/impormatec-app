<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicidad extends Model
{
    protected $table = 'publicidad';
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['asunto','ultimo_envio','enviar_todos_clientes','cuerpo_publicidad','id_responsable'];

    public static function obtenerPublicidad(){
        return Publicidad::select("asunto","publicidad.id")
        ->selectRaw("DATE_FORMAT(ultimo_envio,'%d/%m/%Y %h:%i %p') AS fechaHrUltimoEnvio,LPAD(publicidad.id,5,'0') AS nroPublicidad,DATE_FORMAT(publicidad.fechaCreada,'%d/%m/%Y %h:%i %p') AS fechaHrCreada,CONCAT(usuarios.nombres,' ',usuarios.apellidos) AS creadoPor")
        ->join("usuarios","usuarios.id","=","publicidad.id_responsable")
        ->get();
    }
}
