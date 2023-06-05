<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MisProductos;
use App\Models\Rol;
use App\Models\Tecnico;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Models\UsuarioRol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class Usuario extends Controller
{
    public $errorPeticion = ["error" => "solicitud invalida"];
    private $moduloUsuario = "admin.usuario.index";
    public function index()
    {
        $modulos = $this->obtenerModulos();
        $roles = User::find(Auth::id())->roles()->where(['activo' => 1])->first();
        return view("usuario.intranetInicio", compact("modulos"));
    }
    public function loginView()
    {
        if (isset($_COOKIE['login_first'])) {
            return redirect(route('restaurarContra'));
        }
        return view('usuario.acceso');
    }
    public function retaurarContra()
    {
        if (isset($_COOKIE['login_first'])) {
            return view('usuario.cambioContra');
        }
        return redirect(route('login'));
    }
    public function salirLoginFirst()
    {
        if (isset($_COOKIE['login_first'])) {
            unset($_COOKIE['login_first']);
            setcookie('login_first',"", time() - 3600, '/');
        }
        return redirect(route('login'));
    }
    public function logoauth(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('login'));
    }
    public function autenticacion(Request $request)
    {
        if(!$request->ajax()){
            return response()->json(['error' => 'No se pudo procesar la petición']);
        }
        $user = User::select("password")->where(['correo' => $request->correo, 'estado' => 2])->first();
        if (!empty($user)) {
            if (Hash::check($request->password, $user->password)) {
                setcookie('login_first', $request->correo, time() + 3600, '/');
                $message = ['success' => true];
                return response()->json($message);
            }
        }
        $credenciales = $request->only("correo","password");
        $credenciales['estado'] = 1;
        $message = [];
        if (Auth::attempt($credenciales,$request->has('recordar') ? true : false)) {
            $request->session()->regenerate();
            $message = ['success' => true];
        } else {
            $message = ['not_user' => true];
        }
        return response()->json($message);
    }
    public function validarXmlHttpRequest($urlModulo)
    {
        $idUsuario = Auth::id();
        $usuario = User::where(['estado' => 1,'id' => $idUsuario])->first();
        if(empty($usuario)){
            return ['session' => 'usuario no autorizado'];
        }
        $rol = User::find($idUsuario)->roles()->where('activo',1)->first();
        if(empty($rol)){
            return ['session' => 'rol no activo'];
        }
        if(empty($rol->modulos()->where('url',$urlModulo)->first())){
            return ['session' => 'acceso denegado'];
        }
        return ['success' => 'usuario habilitado'];
    }
    public function obtenerModulos()
    {
        if(!Auth::check()){
            return redirect()->route("login");
        }
        $idUsuario = Auth::id();
        $roles = User::find($idUsuario);
        if(!$roles->roles()->where('activo',1)->count()){
            $rol = $roles->roles()->first();
            $roles->roles()->where('rolFk', $rol->id)->update(['activo' => 1]);
        }else{
            $rol = $roles->roles()->where('activo',1)->first();
        }
        return Rol::find($rol->id)->modulos()->where('estado',1)->orderBy('grupoFk')->orderBy("id")->get();
    }
    public function cambioRol(Rol $rol)
    {
        if (!Auth::check()) {
            return redirect()->route("login");
        }
        $idUsuario = Auth::id();
        $usuarioRol = User::find($idUsuario);
        if(!$usuarioRol->roles()->where('rolFk',$rol->id)->count()){
            return abort(403);
        }
        $usuarioRol->roles()->update(['activo' => 0]);
        $rol->usuarios()->where('usuarioFk',$idUsuario)->update(['activo' => 1]);
        return redirect()->route('home');
    }
    public function cambioContrasena(Request $request)
    {
        $accessModulo = $this->validarXmlHttpRequest($this->moduloUsuario);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        User::find($request->usuario)->update(['password' => Hash::make($request->password_temp) ,'estado' => 2]);
        return response()->json(['success' => 'contraseña restaurada correctamente']);
    }
    public function miPerfil()
    {
        $modulos = $this->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado', 1)->get();
        return view("usuario.miPerfil",compact("modulos", "tiposDocumentos"));
    }
    public function actualizarPerfil(Request $request, MisProductos $productController)
    {
        if(!$request->ajax()){
            return response()->json(['error' => 'error en la petición']);
        }
        $datos = $request->only("tipoDocumento","nroDocumento","direccion","telefono","celular","fechaCumple","sexo");
        DB::beginTransaction();
        try {
            if($request->has("avatar")){
                $urlAvatar = Auth::user()->urlAvatar;
                if(!empty($urlAvatar) && Storage::disk('avatars')->exists($urlAvatar)){
                    Storage::disk('avatars')->delete($urlAvatar);
                }
                $datos['urlAvatar'] = $productController->guardarArhivo($request,'avatar',"avatars");
            }
            User::find(Auth::id())->update($datos);
            DB::commit();
            return response()->json(['success' => 'datos actualizados correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function usuarioAccion(Request $request)
    {
        if(!$request->ajax()){
            return response()->json(['error' => 'error en la petición']);
        }
        $accessModulo = $this->validarXmlHttpRequest($this->moduloUsuario);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        switch ($request->acciones) {
            case 'agregar':
                $repetidos = User::where(['correo' => $request->correo])->count();
                if ($repetidos > 0) {
                    return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
                }
                $datos = $request->all();
                $datos['password'] = Hash::make($datos['password']);
                $datos['estado'] = 2;
                unset($datos['acciones']);
                DB::beginTransaction();
                try {
                    $usuario = User::create($datos);
                    foreach($datos['roles'] as $rol){
                        UsuarioRol::create(['rolFk' => $rol,'usuarioFk' => $usuario->id]);
                    }
                    $validarTecnico = User::validarTecnico($datos['roles']);
                    if($validarTecnico){
                        Tecnico::create(['idUsuario' => $usuario->id]);
                    }
                    DB::commit();
                    return response()->json(['success' => 'Usuario creado correctamente, recuerde que su contraseña temporal es ' . $request->password]);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(['error' => $th->getMessage(),'codigo' => $th->getCode()]);
                }
            break;
            case 'obtener' :
                $usuarios = new User();
                if($request->rol != 'todos'){
                    $usuarios = Rol::find($request->rol)->usuarios()->select("nombres","apellidos","celular","estado","correo","usuarios.id");
                }else{
                    $usuarios = User::select("nombres","apellidos","celular","estado","correo","usuarios.id");
                }
                return DataTables::of($usuarios->get())->addColumn('apellidosNombres',function(User $usuario){
                    return $usuario->nombres . ' ' . $usuario->apellidos;
                })->toJson();
            break;
            case 'mostrarEditar':
                $usuario = User::with('roles:id')->select("nombres", "apellidos", "celular", "estado", "correo", "usuarios.id","tipoDocumento","nroDocumento","telefono","celular","direccion","fechaCumple","sexo")->where("usuarios.id", $request->idUsuario)->first();
                return response()->json(['success' => $usuario]);
            break;
            case 'editar':
                $usuario = User::find($request->idUsuario);
                DB::beginTransaction();
                try {
                    $usuario->roles()->detach();
                    foreach ($request->roles as $rol) {
                        $usuario->roles()->attach($rol);
                    }
                    $datos = $request->all();
                    unset($datos['acciones']);
                    unset($datos['roles']);
                    $usuario->update($datos);
                    DB::commit();
                    return response()->json(['success' => 'Usuario actualizado con éxito']);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(['error' => $th->getMessage(), 'codigo' => $th->getCode()]);
                }
            break;
            case 'eliminar':
                $usuario = User::find($request->idUsuario);
                DB::beginTransaction();
                try {
                    $usuario->roles()->detach();
                    $usuario->delete();
                    DB::commit();
                    return response()->json(['success' => 'Usuario eliminado con éxito']);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(['error' => $th->getMessage(), 'codigo' => $th->getCode()]);
                }
            break;
        }
    }
    public function listarUsuarios()
    {
        $verif = $this->validarXmlHttpRequest($this->moduloUsuario);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->obtenerModulos();
        $roles = Rol::all();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        return view('usuario.lista',compact("roles","modulos","tiposDocumentos"));
    }
    public function restaurarContrasena(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'No se pudo procesar la petición']);
        }
        if (!isset($_COOKIE['login_first'])) {
            return response()->json(['noExistCookie' => true]);
        }
        if($request->password !== $request->password2){
            return response()->json(['error' => 'Las contraseñas no coinciden']);
        }
        if(strlen($request->password) < 8){
            return response()->json(['error' => 'La contraseña debe tener al menos 8 caracteres']);
        }
        $usuario = User::where('correo', $_COOKIE['login_first'])->first();
        $usuario->update(['password' => Hash::make($request->password),'estado' => 1]);
        if (Auth::attempt(['correo' => $_COOKIE['login_first'], 'password' => $request->password],true)) {
            $request->session()->regenerate();
        }
        unset($_COOKIE['login_first']);
        setcookie('login_first', null, time() - 3600, '/');
        return response()->json(['success' => 'Contraseña restaurada con éxito']);
    }
    public function validarAutenticacionView()
    {
        $resultado = $this->validarAutenticacion();
        if(isset($resultado['sessionFirst'])){
            return redirect(route('restaurarContra'));
        }
        if(isset($resultado['session'])){
            return redirect(route('login'));
        }
    }
}
