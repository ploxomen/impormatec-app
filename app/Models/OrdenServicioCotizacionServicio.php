<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenServicioCotizacionServicio extends Model
{
    public $table = "orden_servicio_cotizacion_servicio";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_orden_servicio','id_cotizacion_servicio','orden','fecha_termino','objetivos','acciones','descripcion','conclusiones_recomendaciones','estado'];
    public function secciones() {
        return $this->hasMany(InformeServicioSecciones::class,'id_os_servicio');
    }
    public static function obtenerServicio($idOSservicio,$idServicio) {
       return self::mostrarServiciosOrdenServicio($idOSservicio)->where('orden_servicio_cotizacion_servicio.id',$idServicio)->select("servicios.acciones","servicios.descripcion","servicios.servicio","servicios.objetivos")->first();
    }
    public function cotizacionServicio()
    {
        return $this->belongsTo(CotizacionServicio::class,'id_cotizacion_servicio');
    }
    public static function mostrarServiciosOrdenServicio($idOrdenServicio) {
        return OrdenServicioCotizacionServicio::select("orden_servicio_cotizacion_servicio.id AS idOsCotizacion","servicios.servicio","cotizacion_servicios.cantidad","cotizacion_servicios.importe","cotizacion_servicios.descuento","cotizacion_servicios.total","cotizacion_servicios.id AS idCotizacionServicio","orden_servicio_cotizacion_servicio.orden","orden_servicio_cotizacion_servicio.id_cotizacion_servicio AS idCotizacionDetalle")
        ->selectRaw("LPAD(cotizacion_servicios.id_cotizacion,5,'0') AS nroCotizacion,'servicio' AS tipoServicioProducto")
        ->join("cotizacion_servicios","cotizacion_servicios.id","=","orden_servicio_cotizacion_servicio.id_cotizacion_servicio")
        ->join("servicios","cotizacion_servicios.id_servicio","=","servicios.id")
        ->where('id_orden_servicio',$idOrdenServicio);
    }
}
