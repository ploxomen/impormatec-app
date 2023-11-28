<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{

    public $table = "usuarios";
    const UPDATED_AT = "fechaActualizada";
    const CREATED_AT = "fechaCreada";
    protected $rememberTokenName = 'recordarToken';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'cip',
        'password',
        'correo',
        'tipoDocumento',
        'nroDocumento',
        'telefono',
        'celular',
        'direccion',
        'urlAvatar',
        'fechaCumple',
        'sexo',
        'firma',
        'estado'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'recordarToken',
    ];


    public function roles()
    {
        return $this->belongsToMany(Rol::class,'usuario_rol','usuarioFk','rolFk')->withPivot('activo')->withTimestamps();
    }
    public function documento()
    {
        return $this->belongsTo(TipoDocumento::class,'tipoDocumento');
    }
    public function cliente()
    {
        return $this->hasOne(Clientes::class,'id_usuario');
    }
    public function tecnico()
    {
        return $this->hasOne(Tecnico::class,'idUsuario');
    }
    public static function obtenerUsuariosNoSonClientes(){
        return User::whereNotIn('id',function($q){
            $q->select("id_usuario")->from("clientes");
        })->where('estado','>=',1)->get();
    }
    public static function validarTecnico(array $idRoles)
    {
        return DB::table("rol")->where('nombreRol','tecnico')->whereIn('id',$idRoles)->count();
    }
    public static function firmasHabilitadas()
    {
        return User::select("id","nombres","apellidos","firma")->whereNotNull("firma")->where('estado','>',0)->get();
    }
    
}
