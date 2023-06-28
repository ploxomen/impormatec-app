<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    public $table = "servicios";
    const UPDATED_AT = "fechaActualizada";
    const CREATED_AT = "fechaCreada";
    protected $fillable = ['servicio','descripcion','estado'];
    public function scopeObtenerServicios($query)
    {
        return $query->select("id","servicio","descripcion","estado")
        ->selectRaw("LPAD(id,5,'0') AS nroServicio")
        ->get();
    }
    public function scopeObtenerServicioProductos($query,$idServicio){
        $servicio = $query->select("id","servicio","descripcion")
        ->where(['id' => $idServicio,'estado' => 1])->first();
        if(!empty($servicio)){
            $servicio->productos = ServicioProducto::obtenerProductos($servicio->id);
        }
        return $servicio;
    } 
}
