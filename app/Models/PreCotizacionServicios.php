<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PreCotizacionServicios extends Model
{
    public $table = "cotizacion_pre_servicios";
    protected $fillable = ['id_pre_cotizacion','id_servicios'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public static function preCotizacionServicios($idPreCotizacion) {
        return DB::table("cotizacion_pre_servicios AS cp")
        ->select("s.id","s.servicio","s.descripcion")
        ->join("servicios AS s","cp.id_servicios","=","s.id")
        ->where(['cp.id_pre_cotizacion' => $idPreCotizacion,'s.estado' => 1])->get();
    }
}
