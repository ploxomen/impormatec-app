<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenServicio extends Model
{
    public $table = "orden_servicio";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_cliente','tipoMoneda','facturacion_externa','fecha','observaciones','incluir_igv','importe','descuento','igv','adicional','costo_total','utilidad','gasto_caja','total','estado'];
    
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
    public static function misOrdeneseServicio($fechaInicio,$fechaFin,$cliente,$estado) {
        $ordenesServicios = OrdenServicio::select("orden_servicio.id","clientes.nombreCliente","orden_servicio.utilidad","orden_servicio.costo_total","orden_servicio.gasto_caja","tipoMoneda","orden_servicio.importe","orden_servicio.descuento","orden_servicio.igv","orden_servicio.adicional","orden_servicio.total","orden_servicio.estado")->selectRaw("DATE_FORMAT(orden_servicio.fecha,'%d/%m/%Y') AS fechaOs,LPAD(orden_servicio.id,5,'0') AS nroOs")
        ->join("clientes","orden_servicio.id_cliente","=","clientes.id")->whereBetween('orden_servicio.fecha',[$fechaInicio,$fechaFin]);
        if($cliente !== 'TODOS'){
            $ordenesServicios = $ordenesServicios->where('orden_servicio.id_cliente',$cliente);
        }
        if($estado !== 'TODOS'){
            $ordenesServicios = $ordenesServicios->where('orden_servicio.estado',$estado);
        }
        return $ordenesServicios->get();
    }
    public function cajaChicaCostos()
    {
        return $this->hasMany(CajaChicaDetalle::class,'id_os');
    }
    public function servicios() {
        return $this->hasMany(OrdenServicioCotizacionServicio::class,'id_orden_servicio');
    }
    public function costosAdicionales() {
        return $this->hasMany(OrdenServicioAdicional::class,'id_orden_servicio');
    }
    public function pagoCuotas()
    {
        return $this->hasMany(PagoCuotas::class,'id_orden_servicio');
    }
    public function comprobantes()
    {
        return $this->hasMany(Comprobantes::class,'id_os_servicio');
    }
    
}
