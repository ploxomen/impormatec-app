<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PreCotizaion extends Model
{
    public $table = "cotizacion_pre";
    protected $fillable = ['id_cliente','fecha_hr_visita','columnas','formato_visita_pdf','detalle','estado','usuario_creado','usuario_modificado','html_primera_visita'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public static function obtenerFechasParaFiltroTecnico($idTecnico)
    {
        return DB::table("cotizacion_pre AS cp")
        ->selectRaw("DATE_FORMAT(cp.fecha_hr_visita,'%d/%m/%Y') AS fecha,DATE_FORMAT(cp.fecha_hr_visita,'%Y-%m-%d') AS fechaNormal,COUNT(*) AS nroVisitas")
        ->join("cotizacion_pre_tecnicos AS cpt","cp.id","=","cpt.id_pre_cotizacion")
        ->where(['cpt.id_tecnico'=>$idTecnico,'cp.estado' => 1])->groupByRaw("DATE_FORMAT(cp.fecha_hr_visita,'%d/%m/%Y')")->orderBy("cp.fecha_hr_visita")->get();
    }
    public static function obtenerPreCotizacionEditar($idPreCotizacion){
        $preCotizacion = DB::table("cotizacion_pre AS cp")
        ->select("cp.detalle","cp.id","cp.id_cliente","u.correo","u.tipoDocumento","u.nroDocumento","u.telefono","u.celular","u.direccion","cp.estado")
        ->selectRaw("DATE_FORMAT(cp.fecha_hr_visita,'%Y-%m-%d %H:%i') AS fechaHrProgramada")
        ->join("clientes AS c","c.id","=","cp.id_cliente")
        ->join("usuarios AS u","u.id","=","c.id_usuario")
        ->where('cp.id',$idPreCotizacion)->first();
        if(!empty($preCotizacion)){
            $preCotizacion->contactos = ClientesContactos::where('idCliente',$preCotizacion->id_cliente)->get();
            $preCotizacion->contactosAsignados = PreCotizaionContacto::where('id_cotizacion_pre',$preCotizacion->id)->get();
            $preCotizacion->tecnicos = PreCotizaionTecnico::where('id_pre_cotizacion',$preCotizacion->id)->get();
        }
        return $preCotizacion;

    }
    public static function obtenerPreCotizacion($idPreCotizacion,$idTecnico){
        $preCotizacion = PreCotizaionTecnico::select("cotizacion_pre.id","formato_visita_pdf","html_primera_visita")->join("cotizacion_pre","cotizacion_pre.id","=","cotizacion_pre_tecnicos.id_pre_cotizacion")->where(['id_pre_cotizacion' => $idPreCotizacion]);
        if(!is_null($idTecnico)){
            $preCotizacion = $preCotizacion->where(['id_tecnico' => $idTecnico,'responsable' => 1,'cotizacion_pre.estado' => 1]);
        }
        $preCotizacion = $preCotizacion->first();
        if(empty($preCotizacion)){
            return ['alerta' => 'No se encontró la pre-cotización'];
        }
        $preCotizacion->secciones = CotizacionPreSecciones::select("id AS idSeccion","titulo","columnas")->where('id_pre_cotizacion',$preCotizacion->id)->get();
        foreach ($preCotizacion->secciones as $key => $seccion) {
            $seccion->listaImagenes = PreCotizacionSeccionImagen::select("id AS idImagen","id_pre_cotizacion_seccion AS idSeccion","url_imagen","descripcion")->where('id_pre_cotizacion_seccion',$seccion->id)->get();
        }
        $preCotizacion->servicios = PreCotizacionServicios::select("id_servicios")->where('id_pre_cotizacion',$preCotizacion->id)->get();
        return ['success' => $preCotizacion];
    }
    public static function obtenerPreCotizaciones()
    {
        return DB::table("cotizacion_pre AS cp")
        ->select("cp.id","c.nombreCliente","cp.formato_visita_pdf","u.nombres AS nombreTecnico","u.apellidos AS aspellidosTecnico","cp.estado")
        ->selectRaw("DATE_FORMAT(cp.fecha_hr_visita,'%d/%m/%Y %h:%i %p') AS fechaHrProgramada,LPAD(cp.id,5,'0') AS nroPreCotizacion")
        ->join("clientes AS c","c.id","=","cp.id_cliente")
        ->join("cotizacion_pre_tecnicos AS cpt",function($join){
            $join->on("cp.id","=","cpt.id_pre_cotizacion")
            ->where('cpt.responsable','=',1);
        })->join("tecnicos AS t","t.id","=","cpt.id_tecnico")
        ->join("usuarios AS u","u.id","=","t.idUsuario");
    }
    
    public static function validarPrecotizacionResponsable($idPreCotizacion,$idTecnico,$estado)
    {
        return DB::table("cotizacion_pre AS cp")
        ->join("cotizacion_pre_tecnicos AS cpt","cp.id","=","cpt.id_pre_cotizacion")
        ->where(['cpt.id_tecnico'=>$idTecnico,'cp.estado' => $estado,'cp.id' => $idPreCotizacion, 'cpt.responsable' => 1])->count();
    }
    public static function obtenerDatosPreCotizacion($idPreCotizacion) {
        $preCotizacion = DB::table("cotizacion_pre AS cp")
        ->select("cp.id","cp.id_cliente","u.direccion")
        ->join("clientes AS c","c.id","=","cp.id_cliente")
        ->join("usuarios AS u","u.id","=","c.id_usuario")
        ->where(['cp.id'=>$idPreCotizacion,'cp.estado' => 2])->first();
        if(!empty($preCotizacion)){
            $preCotizacion->contactos = ClientesContactos::select("id","nombreContacto","numeroContacto")->where('idCliente',$preCotizacion->id_cliente)->get();
            $preCotizacion->servicios = PreCotizacionServicios::preCotizacionServicios($preCotizacion->id);
            foreach ($preCotizacion->servicios as $servicio) {
                $servicio->productos = ServicioProducto::obtenerProductos($servicio->id);
            }
        }
        return $preCotizacion;
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
    public function cliente()
    {
        return $this->belongsTo(Clientes::class,'id_cliente');
    }
    public function contactos()
    {
        return $this->hasMany(ContactoCotizacionPre::class,'id_cotizacion_pre');
    }
    public function tecnicoResponsable()
    {
        return $this->belongsToMany(Tecnico::class,'cotizacion_pre_tecnicos','id_pre_cotizacion','id_tecnico')->wherePivot('responsable',1);
    }
}
