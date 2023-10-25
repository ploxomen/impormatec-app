<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    public function guardarImagenesEditorTexto(Request $request) {
        $image = $request->file('file');
        $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('imagenesEditor'), $imageName);
        $imageUrl = asset('imagenesEditor/' . $imageName);
        return response()->json(['location' => $imageUrl]);
    }
    public function guardarImagenesEditorTextoCotizacion(Request $request) {
        $image = $request->file('file');
        $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('imagenesEditorCotizacion'), $imageName);
        $imageUrl = asset('imagenesEditorCotizacion/' . $imageName);
        return response()->json(['location' => $imageUrl]);
    }
    public function guardarImagenesEditorTextoOs(Request $request) {
        $image = $request->file('file');
        $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('imagenesEditorOs'), $imageName);
        $imageUrl = asset('imagenesEditorOs/' . $imageName);
        return response()->json(['location' => $imageUrl]);
    }
    public function guardarImagenesEditorTextoConfiguracion(Request $request) {
        $image = $request->file('file');
        $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('imagenesEditorConfiguracion'), $imageName);
        $imageUrl = asset('imagenesEditorConfiguracion/' . $imageName);
        return response()->json(['location' => $imageUrl]);
    }
}
