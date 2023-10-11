<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuariosActvidades extends Model
{
    public $table = "usuarios_actividades";
    protected $fillable = ['id_usuario','fecha_hr_inicio','fecha_hr_fin','tarea'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public static function obtenerActividades($fechaInicio,$fechaFin,$responsable){
        $visitas = PreCotizaion::obtenerPreCotizaciones()->select("cp.id","u.nombres","u.apellidos")->selectRaw("DATE_FORMAT(cp.fecha_hr_visita,'%d/%m/%Y %h:%i %p') AS fecha_hr_inicio,NULL AS fecha_hr_fin,CONCAT('Visita al cliente(a) ',c.nombreCliente) AS tarea, 'VISITA' AS tipo")->where("cp.fecha_hr_visita",'>=',$fechaInicio)->where("cp.fecha_hr_visita",'<=',$fechaFin)->where('cp.estado',1);
        if($responsable !== 'todos'){
            $visitas = $visitas->where('u.id',$responsable);
        }
        $actividades = UsuariosActvidades::select("usuarios_actividades.id","nombres","apellidos")->selectRaw("DATE_FORMAT(fecha_hr_inicio,'%d/%m/%Y %h:%i %p') AS fecha_hr_inicio,DATE_FORMAT(fecha_hr_fin,'%d/%m/%Y %h:%i %p') AS fecha_hr_fin,tarea,'ACTIVIDAD' AS tipo")
        ->join("usuarios","usuarios_actividades.id_usuario","=","usuarios.id")
        ->where('fecha_hr_inicio','>=',$fechaInicio)->where('fecha_hr_inicio','<=',$fechaFin);
        if($responsable !== 'todos'){
            $actividades = $actividades->where('usuarios_actividades.id_usuario',$responsable);
        }
        return $actividades->union($visitas)->orderBy("fecha_hr_inicio")->get();
    }
}
