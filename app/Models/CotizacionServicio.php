<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionServicio extends Model
{
    public $table = "cotizacion_servicios";
    protected $fillable = ['id_cotizacion','id_servicio','orden','precio','cantidad','importe','descuento','igv','total','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function servicios() {
        return $this->belongsTo(Servicio::class,'id_servicio');
    }
    public function productos() {
        return $this->hasMany(CotizacionServicioProducto::class,'id_cotizacion_servicio');
    }
    public static function mostrarServiciosConProductos($idCotizacion) {
        return CotizacionServicio::select("cotizacion_servicios.id","cotizacion_servicios.id_cotizacion","cotizacion_servicios.id_servicio","cotizacion_servicios.orden","cotizacion_servicios.precio","cotizacion_servicios.cantidad","cotizacion_servicios.importe","cotizacion_servicios.descuento","cotizacion_servicios.igv","cotizacion_servicios.total","servicios.servicio AS nombreDescripcion")->selectRaw("'servicio' AS tipo,null AS detalleProductos")
        ->join("servicios","cotizacion_servicios.id_servicio","=","servicios.id")
        ->where('id_cotizacion',$idCotizacion);
    }
    
}
