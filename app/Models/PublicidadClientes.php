<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicidadClientes extends Model
{
    protected $table = 'publicidad_clientes';
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_publicidad','id_cliente'];

    public static function obtenerDatosClientes($idPublicidad){
        return PublicidadClientes::select("clientes.*","usuarios.correo")
        ->join("clientes","clientes.id","=","publicidad_clientes.id_cliente")
        ->join("usuarios","usuarios.id","=","clientes.id_usuario")->where('publicidad_clientes.id_publicidad',$idPublicidad)->get();
    }
}
