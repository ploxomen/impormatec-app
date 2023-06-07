<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Usuario;
use App\Models\Clientes as ModelsClientes;
use App\Models\ClientesContactos;
use App\Models\Rol;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Models\UsuarioRol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class Clientes extends Controller
{
    private $usuarioController;
    private $moduloCliente = "admin.ventas.clientes.index";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        return view("ventas.clientes",compact("modulos","tiposDocumentos"));
    }
    public function listar(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $clientes = ModelsClientes::obenerClientes();
        return DataTables::of($clientes)->toJson();
    }
    public function store(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $repetidos = User::where(['correo' => $request->correo])->count();
        if ($repetidos > 0) {
            return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
        }
        $rolCliente = Rol::where('nombreRol','cliente')->first();
        if(empty($rolCliente)){
            return response()->json(['alerta' => 'Para crear una cuenta de cliente se necesita el rol Cliente por favor registre el rol']);
        }
        $datosUsuario = $request->only("correo","password","tipoDocumento","nroDocumento","telefono","celular","direccion");
        $datosUsuario['password'] = Hash::make($datosUsuario['password']);
        $datosUsuario['estado'] = 2;
        $datosUsuario['nombres'] = $request->nombreCliente;
        DB::beginTransaction();
        try {
            $usuario = User::create($datosUsuario);
            UsuarioRol::create(['rolFk' => $rolCliente->id,'usuarioFk' => $usuario->id]);
            $cliente = ModelsClientes::create(['id_usuario' => $usuario->id,'nombreCliente' => $request->nombreCliente,'estado' => 1]);
            if(isset($request->contactoNombres)){
                for ($i=0; $i < count($request->contactoNombres); $i++) {
                    $contactos = [
                        'idCliente' => $cliente->id,
                        'nombreContacto' => isset($request->contactoNombres[$i]) ? $request->contactoNombres[$i] : null,
                        'numeroContacto' => isset($request->contactoNumero[$i]) ? $request->contactoNumero[$i] : null
                    ];
                    ClientesContactos::create($contactos);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Cliente creado correctamente, recuerde que su contraseña temporal es ' . $request->password]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(),'codigo' => $th->getCode()]);
        }
    }
    public function show($cliente)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        return response()->json(['cliente' => ModelsClientes::obenerCliente($cliente) ]);
    }
    public function update(ModelsClientes $cliente, Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        DB::beginTransaction();
        try {
            $datosUsuario = $request->only("tipoDocumento","nroDocumento","telefono","celular","direccion");
            $datosUsuario['nombres'] = $request->nombreCliente;
            $datosCliente = $request->only("nombreCliente");
            $datosCliente['estado'] = $request->has("estado") ? 1 : 0;
            User::where('id',$cliente->id_usuario)->update($datosUsuario);
            $cliente->update($datosCliente);
            if(isset($request->contactoNombres)){
                for ($i=0; $i < count($request->contactoNombres); $i++) {
                    $contactos = [
                        'nombreContacto' => isset($request->contactoNombres[$i]) ? $request->contactoNombres[$i] : null,
                        'numeroContacto' => isset($request->contactoNumero[$i]) ? $request->contactoNumero[$i] : null
                    ];
                    if(isset($request->idContacto[$i])){
                        ClientesContactos::where(['id' => $request->idContacto[$i],'idCliente' => $cliente->id])->update($contactos);
                    }else{
                        $contactos['idCliente'] = $cliente->id;
                        ClientesContactos::create($contactos);
                    }
                }
            }
            
            DB::commit();
            return response()->json(['success' => 'Cliente actualizado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function eliminarContacto(ClientesContactos $contacto)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $contacto->delete();
        return response()->json(['success' => 'contacto eliminado correctamente']);
    }
    public function destroy(ModelsClientes $cliente)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        DB::beginTransaction();
        try {
            $cliente->delete();
            DB::commit();
            return response()->json(['success' => 'cliente eliminado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
