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
}
