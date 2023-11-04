<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformeServicioSeccionesImg extends Model
{
    public $table = "informe_orden_servicio_secciones_img";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_informe_os_secciones','url_imagen','descripcion','estado'];
}
