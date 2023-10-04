<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformeServicioSecciones extends Model
{
    public $table = "informe_orden_servicio_secciones";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_os_servicio','titulo','filas','estado'];
    public function imagenes() {
        return $this->hasMany(InformeServicioSeccionesImg::class,'id_informe_os_secciones');
    }
}
