<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadesDeMedida extends Model
{
    public $table = "unidades_medida";
    protected $fillable = ['codigo','descripcion'];
    public $timestamps = false;
}
