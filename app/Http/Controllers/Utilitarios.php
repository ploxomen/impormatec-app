<?php

namespace App\Http\Controllers;

class Utilitarios extends Controller
{
    public function obtenerNombresMeses() {
        return ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","setiembre","octubre","noviembre","diciembre"];
    }
    public function obtenerNombreMes($fechaTime) {
        $meses = $this->obtenerNombresMeses();
        return $meses[date('n',$fechaTime) - 1];
    }
    public function obtenerFechaLarga($fechaTime) {
        $meses = $this->obtenerNombresMeses();
        $dias = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
        return $dias[date("w",$fechaTime)] . ', ' . date("d",$fechaTime) . ' de ' . $meses[date('n',$fechaTime) - 1] . ' del ' . date('Y',$fechaTime);
    }
}
