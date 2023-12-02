<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ComprobanteInternoDetalle extends Model
{
    public $table = "comprobantes_internos_detalle";
    protected $fillable = ['id_comprobante_interno','descripcion','cantidad','precio','descuento','igv','total'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
