<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenServicio extends Model
{
    public $table = "orden_servicio";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_cliente','tipoMoneda','facturacion_externa','fecha','observaciones','incluir_igv','importe','descuento','igv','adicional','total','estado'];
    
    public function cliente()
    {
        return $this->belongsTo(Clientes::class,'id_cliente');
    }
    public function scopeOrdenServiciosCliente($query,$idCliente)
    {
        return $query->select("orden_servicio.id")->selectRaw("LPAD(orden_servicio.id,5,'0') AS nroOs")
        ->join("orden_servicio_cotizacion_servicio","orden_servicio_cotizacion_servicio.id_orden_servicio","=","orden_servicio.id")
        ->where(['id_cliente'=>$idCliente,'orden_servicio.estado' => 1])->groupBy("orden_servicio.id")->get();
    }
    public function scopeMisOrdeneseServicio($query) {
        return $query->select("orden_servicio.id","clientes.nombreCliente","tipoMoneda","orden_servicio.importe","orden_servicio.descuento","orden_servicio.igv","orden_servicio.adicional","orden_servicio.total","orden_servicio.estado")->selectRaw("DATE_FORMAT(orden_servicio.fecha,'%d/%m/%Y') AS fechaOs,LPAD(orden_servicio.id,5,'0') AS nroOs")
        ->join("clientes","orden_servicio.id_cliente","=","clientes.id")
        ->get();
    }
    public function servicios() {
        return $this->hasMany(OrdenServicioCotizacionServicio::class,'id_orden_servicio');
    }
    public function costosAdicionales() {
        return $this->hasMany(OrdenServicioAdicional::class,'id_orden_servicio');
    }
    
}
