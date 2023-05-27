<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientesContactos extends Model
{
    public $table = "clientes_contactos";
    protected $fillable = ['idCliente','nombreContacto','numeroContacto'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function cliente()
    {
        return $this->belongsTo(Clientes::class,'idCliente');
    }

}
