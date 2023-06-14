<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PreCotizaion extends Model
{
    public $table = "cotizacion_pre";
    protected $fillable = ['id_cliente','fecha_hr_visita','detalle','estado','usuario_creado','usuario_modificado','html_primera_visita'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public static function obtenerFechasParaFiltroTecnico($idTecnico)
    {
        return DB::table("cotizacion_pre AS cp")
        ->selectRaw("DATE_FORMAT(cp.fecha_hr_visita,'%d/%m/%Y') AS fecha,DATE_FORMAT(cp.fecha_hr_visita,'%Y-%m-%d') AS fechaNormal,COUNT(*) AS nroVisitas")
        ->join("cotizacion_pre_tecnicos AS cpt","cp.id","=","cpt.id_pre_cotizacion")
        ->where(['cpt.id_tecnico'=>$idTecnico,'cp.estado' => 1])->groupByRaw("DATE_FORMAT(cp.fecha_hr_visita,'%d/%m/%Y')")->orderBy("cp.fecha_hr_visita")->get();
    }
    public static function validarPrecotizacionResponsable($idPreCotizacion,$idTecnico,$estado)
    {
        return DB::table("cotizacion_pre AS cp")
        ->join("cotizacion_pre_tecnicos AS cpt","cp.id","=","cpt.id_pre_cotizacion")
        ->where(['cpt.id_tecnico'=>$idTecnico,'cp.estado' => $estado,'cp.id' => $idPreCotizacion, 'cpt.responsable' => 1])->count();
    }
    public static function obtenerPreCotizacionPorTecnicoFecha($idTecnico,$fecha)
    {
        $visitas = DB::table("cotizacion_pre AS cp")
        ->select("cp.id","c.nombreCliente","u.celular","u.telefono","u.direccion","cp.detalle")
        ->selectRaw("DATE_FORMAT(cp.fecha_hr_visita,'%d/%m/%Y %h:%i %p') AS fechaHrVisita,LPAD(cp.id,5,'0') AS nroVisita")
        ->join("cotizacion_pre_tecnicos AS cpt","cp.id","=","cpt.id_pre_cotizacion")
        ->join("clientes AS c","c.id","=","cp.id_cliente")
        ->join("usuarios AS u","u.id","=","c.id_usuario")
        ->where(['cpt.id_tecnico'=>$idTecnico,'cp.estado' => 1])->whereRaw("DATE_FORMAT(cp.fecha_hr_visita,'%Y-%m-%d') = ?",[$fecha])->groupBy("cp.id")->orderBy("cp.fecha_hr_visita")->get();
        foreach ($visitas as $v) {
            $v->contactos = DB::table("cotizacion_pre_contacto AS ccp")
            ->select("cc.nombreContacto","cc.numeroContacto")
            ->join("clientes_contactos AS cc","cc.id","=","ccp.id_cliente_contacto")
            ->where("ccp.id_cotizacion_pre",$v->id)->get();
            $v->tecnicos = DB::table("cotizacion_pre_tecnicos AS cpt")
            ->select("u.nombres","u.apellidos","cpt.responsable")
            ->selectRaw("IF(t.id = ?,1,0) AS activo",[$idTecnico])
            ->join("tecnicos AS t","t.id","=","cpt.id_tecnico")
            ->join("usuarios AS u","u.id","=","t.idUsuario")
            ->where("cpt.id_pre_cotizacion",$v->id)->get();
        }
        return $visitas;
    }
}
