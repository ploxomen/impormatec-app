<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCuotasImg extends Model
{
    protected $table = 'pagos_cuotas_img';
    protected $fillable = ['id_pago_cuota','url_imagen','nombre_imagen'];
    const CREATED_AT = 'fecha_creada';
    const UPDATED_AT = 'fecha_actualizada';

    public static function obtenerImagenes($cuota) {
        return PagoCuotasImg::select("url_imagen AS url","nombre_imagen AS nombre","id")->where('id_pago_cuota',$cuota)->get();
    }
}
