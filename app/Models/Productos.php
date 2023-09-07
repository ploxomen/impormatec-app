<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Productos extends Model
{
    protected $table = 'productos';
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['nombreProducto','tipoMoneda','esIntangible','descripcion','stockMin','precioVenta','precioCompra','urlImagen','estado'];

    // public function marca()
    // {
    //     return $this->belongsTo(Marca::class,'marcaFk');
    // }
    // public function categoria()
    // {
    //     return $this->belongsTo(Categoria::class,'categoriaFk');
    // }
    // public function presentacion()
    // {
    //     return $this->belongsTo(Presentacion::class,'presentacionFk');
    // }
    // public function perecederos()
    // {
    //     return $this->hasMany(Perecedero::class,'productoFk');
    // }
    public function almacenes()
    {
        return $this->belongsToMany(Almacen::class, 'productos_almacen', 'id_producto', 'id_almacen');
    }
    // public function cotizacion()
    // {
    //     return $this->belongsToMany(Cotizacion::class, 'cotizacion_detalle', 'productoFk', 'cotizacionFk')->withTimestamps();
    // }
    // public function scopeCantidadMaximaPerecedero($query,$id,$cantidad,$idPerecedero = null)
    // {
    //     $producto = $query->where('id',$id);
    //     if(empty($producto->first())){
    //         return ['error' => 'no se encontro el producto'];
    //     }
    //     $producto = $producto->with("presentacion")->withSum(["perecederos" => function($sub) use($idPerecedero){
    //         $sub->where('estado',1);
    //         if(!empty($idPerecedero)){
    //             $sub->where('id','!=',$idPerecedero);
    //         }
    //     }],"cantidad")->first();
    //     $cantidadMax = intval($producto->perecederos_sum_cantidad) + intval($cantidad);
    //     $cantidadPermitida = $producto->cantidad - intval($producto->perecederos_sum_cantidad);
    //     if($cantidadPermitida <= 0){
    //         return ["error" => "Se superó el limite de cantidad permitida, si desea agregar más perecederos, intente ampliar el stock del producto"];
    //     }
    //     if($cantidadMax > $producto->cantidad){
    //         return ['error' => 'La cantidad máxima para el producto ' . $producto->nombreProducto . ' es de ' . $cantidadPermitida. ' '. $producto->presentacion->siglas .', por favor intente ingresando la cantidad máxima o inferior.'];
    //     }
    //     return ['success' => true];
    // }
    // public function scopeProductosMasVendidos($query,int $limites)
    // {
    //     return DB::table($this->table . ' AS p')->select("p.nombreProducto")
    //     ->selectRaw("SUM(vd.cantidad) AS total")
    //     ->join("ventas_detalle AS vd","vd.productoFk","=","p.id")
    //     ->join("ventas AS v","vd.ventaFk","=","v.id")
    //     ->where(["p.estado"=>1,"v.estado" => 1])
    //     ->whereYear('v.fechaVenta',date('Y'))
    //     ->groupBy("p.id")->orderByRaw("SUM(vd.cantidad) DESC")->limit($limites)->get();
    // }
    // public function scopeProductosPorVencer($query)
    // {
    //     return DB::table($this->table . ' AS p')->select("p.nombreProducto")
    //     ->selectRaw("SUM(pe.cantidad) AS cantidad,DATE_FORMAT(pe.vencimiento,'%d/%m/%Y') AS fechaVencimiento,DATEDIFF(pe.vencimiento,CURDATE()) AS diasPasados")
    //     ->join("perecederos AS pe","pe.productoFk","=","p.id")
    //     ->where(["p.estado"=>1,"pe.estado" => 1])
    //     ->whereRaw("DATE_ADD(CURDATE(), INTERVAL 15 DAY) >= pe.vencimiento")
    //     ->groupByRaw("pe.productoFk,pe.vencimiento")->orderByRaw("DATEDIFF(CURDATE(),pe.vencimiento) ASC")->get();
    // }
}
