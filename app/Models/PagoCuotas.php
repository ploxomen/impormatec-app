<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCuotas extends Model
{
    protected $table = 'pagos_cuotas';
    protected $fillable = ['id_orden_servicio','id_firmante_pago','nro_cuota','fecha_vencimiento','monto_pagar','fecha_pagada','monto_pagado','descripcion_pagada','comprobante_unico','comprobante_nombre','estado'];
    const CREATED_AT = 'fecha_creada';
    const UPDATED_AT = 'fecha_actualizada';

    public static function obtenerCuotasOrdenServicio($idOrdenServicio,$tipoMoneda) {
        return PagoCuotas::select("id","monto_pagar AS montoPagar","estado","monto_pagado AS montoPagado")->selectRaw("LPAD(nro_cuota,2,'0') AS numeroCuota,IF(descripcion_pagada IS NOT NULL,descripcion_pagada,'') AS descripcion,DATE_FORMAT(fecha_vencimiento,'%d/%m/%Y') AS fechaVencimiento,IF(fecha_pagada IS NOT NULL,DATE_FORMAT(fecha_pagada,'%d/%m/%Y'),'') AS fechaPago,? AS tipoMoneda",[$tipoMoneda])->where('id_orden_servicio',$idOrdenServicio)->orderBy('nro_cuota','asc')->get();
    }
    public static function obtenerCuota($idOrdenServicio,$idCuota) {
        return PagoCuotas::select("id","monto_pagar AS montoPagar","fecha_vencimiento AS fechaVencimiento","estado","monto_pagado AS montoPagado","descripcion_pagada AS descripcionPagada","fecha_pagada AS fechaPagada","id_firmante_pago AS firmatePagado","comprobante_nombre AS comprobanteNombre")->selectRaw("LPAD(nro_cuota,2,'0') AS numeroCuota")->where(['id_orden_servicio' => $idOrdenServicio, 'id' => $idCuota])->orderBy('nro_cuota','asc')->first();
    }
    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class,'id_orden_servicio');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class,'id_firmante_pago');
    }
}