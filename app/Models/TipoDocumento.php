<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    public $table = "tipo_documento";
    protected $fillable = ['documento','longitud','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    // public function clientes()
    // {
    //     return $this->hasMany(Clientes::class,'tipoDocumento');
    // }
    public function usuarios()
    {
        return $this->hasMany(User::class,'tipoDocumento');
    }
    // public function proveedores()
    // {
    //     return $this->hasMany(Proveedores::class,'tipo_documento');
    // }
}