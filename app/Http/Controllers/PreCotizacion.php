<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\ClientesContactos;
use App\Models\PreCotizaion;
use App\Models\PreCotizaionContacto;
use App\Models\PreCotizaionTecnico;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\Tecnico;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Models\UsuarioRol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PreCotizacion extends Controller
{
    private $usuarioController;
    private $moduloPreCotizacion = "cotizacion.precotizacion.nueva";
    private $moduloMisPreCotizacion = "cotizacion.precotizacion.lista";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function indexNuevaPreCotizacion()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = Clientes::obenerClientesActivos();
        $modulos = $this->usuarioController->obtenerModulos();
        $tecnicos = Tecnico::obtenerTecnicosActivos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        return view("preCotizacion.nuevaPreCotizacion",compact("modulos","clientes","tecnicos","tiposDocumentos"));
    }
    public function indexMisPreCotizaciones()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisPreCotizacion);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("preCotizacion.misPreCotizaciones",compact("modulos"));
    }
    public function accionesPreCotizacion(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloPreCotizacion);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $resultado = [];
        switch ($request->acciones) {
            case 'obtener-cliente':
                $resultado = ["cliente" => Clientes::obenerCliente($request->cliente)];
            break;
            case 'agregar-precotizacion':
                $cliente = $request->only("tipoDocumento","nroDocumento","correo","celular","telefono","direccion");
                $clienteId = $request->id_cliente;
                $idUsuario = Auth::id();
                $listaContactos = [];
                DB::beginTransaction();
                try {
                    if($request->nuevo == "true"){
                        $repetidos = User::where(['correo' => $request->correo])->count();
                        if ($repetidos > 0) {
                            return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
                        }
                        $rolCliente = Rol::where('nombreRol','cliente')->first();
                        if(empty($rolCliente)){
                            return response()->json(['alerta' => 'Para crear una cuenta de cliente se necesita el rol Cliente por favor registre el rol']);
                        }
                        $cliente['nombre'] = $clienteId;
                        $cliente['password'] = Hash::make("sistema".date('Y')."@");
                        $cliente['estado'] = 2;
                        $usuario = User::create($cliente);
                        UsuarioRol::create(['rolFk' => $rolCliente->id,'usuarioFk' => $usuario->id]);
                        $clienteModel = Clientes::create(['id_usuario' => $usuario->id,'nombreCliente' => $clienteId,'estado' => 1]);

                        if($request->has("id_cliente_contacto")){
                            for ($i=0; $i < count($request->id_cliente_contacto); $i++) {
                                $txtNombreContacto = explode("-",trim($request->id_cliente_contacto[$i]));
                                $contactos = [
                                    'idCliente' => $clienteModel->id,
                                    'nombreContacto' => isset($txtNombreContacto[0]) ? trim($txtNombreContacto[0]) : null,
                                    'numeroContacto' => isset($txtNombreContacto[1]) ? trim($txtNombreContacto[1]) : null
                                ];
                                $ccontacto = ClientesContactos::create($contactos);
                                $listaContactos[] = $ccontacto->id;
                            }
                        }

                        $clienteId = $clienteModel->id;
                    }else{
                        if($request->has("id_cliente_contacto")){
                            $listaContactos = $request->id_cliente_contacto;
                        }
                        $repetidos = Clientes::verificarCorreo($clienteId,$request->correo);
                        if ($repetidos > 0) {
                            return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
                        }
                        Clientes::find($clienteId)->usuario()->update($cliente);
                    }
                    $preCotizacion = $request->only("fecha_hr_visita","detalle");
                    $preCotizacion['id_cliente'] = $clienteId;
                    $preCotizacion['usuario_creado'] = $idUsuario;
                    $preCotizacion['usuario_modificado'] = $idUsuario;
                    $preCotizacion['estado'] = 1;
                    $modelPreCoti = PreCotizaion::create($preCotizacion);
                    foreach ($listaContactos as $vListaCont) {
                        PreCotizaionContacto::create([
                            'id_cotizacion_pre' => $modelPreCoti->id,
                            'id_cliente_contacto' => $vListaCont
                        ]);
                    }
                    $insertarTecnicos = [
                        'id_pre_cotizacion' => $modelPreCoti->id,
                        'id_tecnico' => $request->cbTecnicoResponsable,
                        'responsable' => 1
                    ];
                    PreCotizaionTecnico::create($insertarTecnicos);
                    if($request->has("cbOtrosTecnicos")){
                        for ($i=0; $i < count($request->cbOtrosTecnicos); $i++) {
                            $insertarTecnicos = [
                                'id_pre_cotizacion' => $modelPreCoti->id,
                                'id_tecnico' => $request->cbOtrosTecnicos[$i],
                                'responsable' => 0
                            ];
                            PreCotizaionTecnico::create($insertarTecnicos);
                        }
                    }
                    DB::commit();
                    return response()->json(['success' => 'Pre - Cotización generada con éxito']);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return ['error' => $th->getMessage(),'code' => $th->getCode()];
                }
                // dd($request->all());
            break;
        }
        return response()->json($resultado);
    }
}
