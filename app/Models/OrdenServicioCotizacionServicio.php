<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenServicioCotizacionServicio extends Model
{
    public $table = "orden_servicio_cotizacion_servicio";
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_orden_servicio','id_firma_profesional','id_cotizacion_servicio','orden','fecha_termino','objetivos','acciones','descripcion','conclusiones_recomendaciones','estado','fecha_fin_garantia','responsable_usuario'];
    public function secciones() {
        return $this->hasMany(InformeServicioSecciones::class,'id_os_servicio');
    }
    public static function obtenerServicio($idOSservicio,$idServicio) {
       return self::mostrarServiciosOrdenServicio($idOSservicio)->where('orden_servicio_cotizacion_servicio.id',$idServicio)->select("servicios.acciones","servicios.descripcion","servicios.servicio","servicios.objetivos")->first();
    }
    public function usuario()
    {
        return $this->belongsTo(User::class,'id_firma_profesional');
    }
    public function cotizacionServicio()
    {
        return $this->belongsTo(CotizacionServicio::class,'id_cotizacion_servicio');
    }
    public static function obtenerInformesGenerados(){
        return OrdenServicioCotizacionServicio::select("clientes.nombreCliente","orden_servicio_cotizacion_servicio.estado","orden_servicio_cotizacion_servicio.id","orden_servicio_cotizacion_servicio.id_orden_servicio","clientes.id AS idCliente")
        ->selectRaw("LPAD(id_orden_servicio,5,'0') AS nroOrdenServicio,LPAD(orden_servicio_cotizacion_servicio.id,5,'0') AS nroInforme,DATE_FORMAT(orden_servicio_cotizacion_servicio.fechaCreada,'%d/%m/%Y') AS fechaEmision,DATE_FORMAT(fecha_termino,'%d/%m/%Y') AS fechaTermino,DATE_FORMAT(fecha_fin_garantia,'%d/%m/%Y') AS fechaFinGarantia,CONCAT(usuarios.nombres,' ',usuarios.apellidos) AS responsable")
        ->join("orden_servicio","orden_servicio.id","=","orden_servicio_cotizacion_servicio.id_orden_servicio")
        ->join("clientes","clientes.id","=","orden_servicio.id_cliente")
        ->leftJoin("usuarios","usuarios.id","=","orden_servicio_cotizacion_servicio.responsable_usuario")
        ->get();
    }
    public static function mostrarServiciosOrdenServicio($idOrdenServicio) {
        return OrdenServicioCotizacionServicio::select("orden_servicio_cotizacion_servicio.id AS idOsCotizacion","servicios.servicio","cotizacion_servicios.cantidad","cotizacion_servicios.importe","cotizacion_servicios.descuento","cotizacion_servicios.total","cotizacion_servicios.id AS idCotizacionServicio","orden_servicio_cotizacion_servicio.orden","orden_servicio_cotizacion_servicio.id_cotizacion_servicio AS idCotizacionDetalle")
        ->selectRaw("LPAD(cotizacion_servicios.id_cotizacion,5,'0') AS nroCotizacion,'servicio' AS tipoServicioProducto")
        ->join("cotizacion_servicios","cotizacion_servicios.id","=","orden_servicio_cotizacion_servicio.id_cotizacion_servicio")
        ->join("servicios","cotizacion_servicios.id_servicio","=","servicios.id")
        ->where('id_orden_servicio',$idOrdenServicio);
    }
}
