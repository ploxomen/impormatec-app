<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaActa extends Model
{
    public $table = "entrega_actas";
    protected $fillable = ['id_orden_servicio','id_responsable_firmante','fecha_entrega','firma_representante_cortado','nombre_representante','dni_representante','firma_representante','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    public function reponsableFirmante()
    {
        return $this->belongsTo(User::class,'id_responsable_firmante');
    }
    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class,'id_orden_servicio');
    }

}
