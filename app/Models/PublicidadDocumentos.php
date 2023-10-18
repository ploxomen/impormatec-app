<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicidadDocumentos extends Model
{
    protected $table = 'publicidad_documentos';
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_publicidad','nombre_real_documento','nombre_sistema_documento'];
}
