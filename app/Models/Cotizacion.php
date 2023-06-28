<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    public $table = "cotizacion";
    protected $fillable = ['id_cliente','id_pre_cotizacion','fechaCotizacion','fechaFinCotizacion','tipoMoneda','referencia','representanteCliente','direccionCliente','importeTotal','descuentoTotal','igvTotal','total','cotizadorUsuario','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    
    public function scopeObtenerCotizacion($query){
        return $query->select("cotizacion.id","clientes.nombreCliente","cotizacion.importeTotal","cotizacion.descuentoTotal","cotizacion.igvTotal","cotizacion.total","cotizacion.estado")
        ->selectRaw("DATE_FORMAT(cotizacion.fechaCotizacion,'%d/%m/%Y') AS fechaCotizada,DATE_FORMAT(cotizacion.fechaFinCotizacion,'%d/%m/%Y') AS fechaFinCotizada,LPAD(cotizacion.id,5,'0') AS nroCotizacion,IF(cotizacion.id_pre_cotizacion IS NULL,'SIN PRE - COTIZACIÓN',LPAD(cotizacion.id_pre_cotizacion,5,'0')) AS nroPreCotizacion,CONCAT(usuarios.nombres,' ',usuarios.apellidos) AS atendidoPor")
        ->leftjoin("cotizacion_pre","cotizacion.id_pre_cotizacion","=","cotizacion_pre.id")
        ->join("clientes","cotizacion.id_cliente","=","clientes.id")
        ->join("usuarios","cotizacion.cotizadorUsuario","=","usuarios.id")
        ->get();
    }
}
