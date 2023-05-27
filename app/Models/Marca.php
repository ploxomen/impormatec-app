<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marca';
    protected $fillable = ['nombreMarca','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    
    protected function serializeDate($date)
    {
        return $date->format('d/m/Y h:i a');
    }
    public function productos()
    {
        return $this->hasMany(Productos::class,'marcaFk');
    }
    
}
