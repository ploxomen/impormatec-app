<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobanteInterno extends Model
{
    public $table = "comprobantes_internos";
    protected $fillable = ['id_comprobante','fecha_emision','tipo_moneda','cliente','tipo_documento','numero_documento','direccion','observaciones','subtotal','descuento','igv_total','total','monto_letras','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function comprobante()
    {
        return $this->belongsTo(Comprobantes::class,'id_comprobante');
    }
    public function detalleComprobantes()
    {
        return $this->hasMany(ComprobanteInternoDetalle::class,'id_comprobante_interno');
    }
}
