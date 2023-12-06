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
    public static function actascClientes($fechaInicio,$fechaFin,$idCliente){
        return EntregaActa::select("entrega_actas.id","dni_representante AS dniRepresentante","nombre_representante AS representante","entrega_actas.estado")
        ->selectRaw("DATE_FORMAT(entrega_actas.fecha_entrega,'%d/%m/%Y') AS fechaEntrega,LPAD(entrega_actas.id_orden_servicio,5,'0') AS nroOs,LPAD(entrega_actas.id,5,'0') AS nroActa,CONCAT(usuarios.nombres,' ',usuarios.apellidos) AS responsable")
        ->join("orden_servicio","orden_servicio.id","=","entrega_actas.id_orden_servicio")
        ->join("usuarios","usuarios.id","=","entrega_actas.id_responsable_firmante")
        ->where(['entrega_actas.estado' => 1, 'orden_servicio.id_cliente' => $idCliente])
        ->where('orden_servicio.estado','>',0)
        ->whereBetween('entrega_actas.fecha_entrega',[$fechaInicio,$fechaFin])->get();
    }
}
