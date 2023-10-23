<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaChicaDetalle extends Model
{
    public $table = "caja_chica_detalles";
    protected $fillable = ['id_caja_chica','id_os','fecha_gasto','tipo_comprobante','nro_comprobante','responsable_pago','proveedor','proveedor_ruc','area_costo','descripcion_producto','tipo_moneda','tipo_cambio','monto_total','igv','monto_total_cambio'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
