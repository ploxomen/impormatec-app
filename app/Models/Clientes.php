<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Clientes extends Model
{
    public $table = "clientes";
    protected $fillable = ['nombreCliente','id_usuario','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    // public function tipoDocumento()
    // {
    //     return $this->belongsTo(TipoDocumento::class,'tipoDocumento');
    // }
    // public function ventas()
    // {
    //     return $this->hasMany(Ventas::class, 'clienteFk');
    // }
    public function usuario()
    {
        return $this->belongsTo(User::class,'id_usuario');
    }
    public function scopeObenerClientes($query)
    {
        return $query->select("clientes.id","tipo_documento.documento","usuarios.nroDocumento","usuarios.correo","clientes.nombreCliente","usuarios.celular","usuarios.telefono","usuarios.direccion","clientes.estado")
        ->join("usuarios","usuarios.id","=",'clientes.id_usuario')
        ->join("tipo_documento","usuarios.tipoDocumento","=","tipo_documento.id","left")->get();
    }
    public function scopeObenerClientesActivos($query)
    {
        return $query->select("clientes.id","tipo_documento.documento","usuarios.nroDocumento","usuarios.correo","clientes.nombreCliente","usuarios.celular","usuarios.telefono","usuarios.direccion","clientes.estado")
        ->join("usuarios","usuarios.id","=",'clientes.id_usuario')
        ->join("tipo_documento","usuarios.tipoDocumento","=","tipo_documento.id","left")->where('clientes.estado',1)->get();
    }
    public function scopeObenerCliente($query,$idCliente)
    {
        $cliente = $query->select("clientes.id","usuarios.correo","usuarios.tipoDocumento","usuarios.nroDocumento","clientes.nombreCliente","usuarios.celular","usuarios.telefono","usuarios.direccion","clientes.estado")
        ->join("usuarios","usuarios.id","=",'clientes.id_usuario')
        ->join("tipo_documento","usuarios.tipoDocumento","=","tipo_documento.id","left")
        ->where(['clientes.id' => $idCliente])->first();
        if(!empty($cliente)){
            $cliente->contactos = DB::table('clientes_contactos')->select("id","nombreContacto","numeroContacto")->where('idCliente',$cliente->id)->get();
        }
        return $cliente;
    }
    public function scopeVerificarCorreo($query,$idCliente,$correo)
    {
        $cliente = $this->obtenerIdUsuario($idCliente); 
        return DB::table("usuarios")->where(['correo' => $correo])->where('id','!=',$cliente->id_usuario)->count();
    }
    public function obtenerIdUsuario($idCliente)
    {
        return DB::table($this->table)->where('id',$idCliente)->first();
    }
    
    public function contactos()
    {
        return $this->hasMany(ClientesContactos::class,'idCliente');
    }
    
    
}
