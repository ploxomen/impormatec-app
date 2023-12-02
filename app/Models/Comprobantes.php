<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobantes extends Model
{
    public $table = "comprobantes";
    protected $fillable = ['id_os_servicio','tipo_moneda','id_comprobante_rapifac','monto_total','fecha_emision','repositorio','numero_comprobante','tipo_comprobante','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    public function comprobanteInterno()
    {
        return $this->hasOne(ComprobanteInterno::class,'id_comprobante');
    }
    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class,'id_os_servicio');
    }
    public static function comprobantesClientes($fechaInicio,$fechaFin,$idCliente){
        $cuotas = PagoCuotas::select("pagos_cuotas.id")
        ->selectRaw("DATE_FORMAT(pagos_cuotas.fecha_pagada,'%d/%m/%Y') AS fechaPagada,CONCAT('CU001-',LPAD(pagos_cuotas.id,5,'0')) AS nroComprobante,orden_servicio.tipoMoneda,pagos_cuotas.monto_pagado AS pagado,'10' AS tipoComprobante,'cuota' AS tipo")
        ->join("orden_servicio","orden_servicio.id","=","pagos_cuotas.id_orden_servicio")
        ->where(['pagos_cuotas.estado' => 2, 'orden_servicio.id_cliente' => $idCliente])
        ->whereBetween('pagos_cuotas.fecha_pagada',[$fechaInicio,$fechaFin]);
        return Comprobantes::select("comprobantes.id")
        ->selectRaw("DATE_FORMAT(comprobantes.fecha_emision,'%d/%m/%Y') AS fechaPagada,comprobantes.numero_comprobante,comprobantes.tipo_moneda,comprobantes.monto_total AS pagado,comprobantes.tipo_comprobante AS tipoComprobante,'facturacion' AS tipo")
        ->join("orden_servicio","orden_servicio.id","=","comprobantes.id_os_servicio")
        ->where(['comprobantes.estado' => 1, 'orden_servicio.id_cliente' => $idCliente])
        ->whereIn('comprobantes.tipo_comprobante',["00","03","01"])
        ->whereBetween('comprobantes.fecha_emision',[$fechaInicio,$fechaFin])->union($cuotas)->get();
    }
}
