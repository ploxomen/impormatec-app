<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionProductos extends Model
{
    public $table = "cotizacion_productos";
    protected $fillable = ['id_cotizacion','id_producto','orden','cantidad','precio','importe','descuento','igv','total','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function productos() {
        return $this->belongsTo(Productos::class,'id_producto');
    }
    //CASO EXCEPCIONAL YA QUE SE CONVINAN - SE USA EN EL PDF DE COTIZACION PARA EL DETALLE
    public function servicios() {
        return $this->belongsTo(Servicio::class,'id_producto');
    }
    public function productosCotizacion() {
        return $this->hasMany(CotizacionServicioProducto::class,'id_cotizacion_servicio','id');
    }
}
