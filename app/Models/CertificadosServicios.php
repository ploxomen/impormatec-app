<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CertificadosServicios extends Model
{
    public $table = "certificados_servicios";
    protected $fillable = ['id_os_cotizacion_servicio','fecha','lugar','asunto','descripcion','usuario_generado','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function ordenServicioCotizacion()
    {
        return $this->belongsTo(OrdenServicioCotizacionServicio::class,'id_os_cotizacion_servicio');
    }
    public static function obtenerCertificados($fechaInicio = null,$fechaFin = null){
        $certificados = CertificadosServicios::select('certificados_servicios.asunto','certificados_servicios.estado','certificados_servicios.id')
        ->selectRaw("LPAD(certificados_servicios.id,5,'0') AS nroCertificado,LPAD(orden_servicio_cotizacion_servicio.id,5,'0') AS nroInforme,DATE_FORMAT(certificados_servicios.fecha,'%d/%m/%Y') AS fechaEmision,DATE_FORMAT(orden_servicio_cotizacion_servicio.fecha_fin_garantia,'%d/%m/%Y') AS fechaFinGarantia")
        ->join('orden_servicio_cotizacion_servicio','orden_servicio_cotizacion_servicio.id','=','certificados_servicios.id_os_cotizacion_servicio')
        ->join("orden_servicio","orden_servicio.id","=","orden_servicio_cotizacion_servicio.id_orden_servicio");
        if(!is_null($fechaInicio) && !is_null($fechaFin)){
            $certificados = $certificados->whereBetween('certificados_servicios.fecha',[$fechaInicio,$fechaFin]);
        }
        return $certificados;
    }
}
