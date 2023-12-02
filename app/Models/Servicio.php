<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    public $table = "servicios";
    const UPDATED_AT = "fechaActualizada";
    const CREATED_AT = "fechaCreada";
    protected $fillable = ['servicio','descripcion','acciones','objetivos','estado'];
    public function scopeObtenerServicios($query)
    {
        return $query->select("id","servicio","descripcion","estado")
        ->selectRaw("LPAD(id,5,'0') AS nroServicio")
        ->where('estado','>=',0)->get();
    }
    public function scopeObtenerServicioProductos($query,$idServicio){
        $servicio = $query->select("id","servicio","descripcion")
        ->where(['id' => $idServicio,'estado' => 1])->first();
        if(!empty($servicio)){
            $servicio->productos = ServicioProducto::obtenerProductos($servicio->id);
        }
        return $servicio;
    }
    public function cotizacionServicio() {
        return $this->hasOne(CotizacionServicio::class,'id_servicio');
    }
    public function productos()
    {
        return $this->belongsToMany(Productos::class,'servicios_productos','id_servicio','id_producto');
    }
}
