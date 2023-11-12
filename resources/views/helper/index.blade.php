<!DOCTYPE html>
<html lang="es">
<head>
    @include('helper.meta')
    <script src="/home.js"></script>
    @yield('head')
</head>
<body>
    <div class="menu-activos" id="salirMenuNavegacion"></div>
    <div class="contenedor">
        <div class="navegacion" id="menuNavegacion">
            <div class="mi-logo text-right">
            </div>
            <div class="mi-perfil d-flex p-3" style="gap: 10px;">
                @php
                    $usuario = auth()->user();
                    $urlPerfil = $usuario->sexo == "F" ? "mujer" : "hombre";
                    if(!empty($usuario->urlAvatar)){
                        $urlPerfil = route("urlImagen",["avatars",$usuario->urlAvatar]);
                    }else{
                        $urlPerfil = '/img/usuario/perfil_' . $urlPerfil .'.png';
                    }
                @endphp
                <div>
                    <img src="{{$urlPerfil}}" class="avatar-menu-img" width="70px" height="70px">
                </div>
                <div class="info-perfil">
                    <span class="d-block pt-3 pb-1">{{auth()->user()->nombres}}</span>
                    <span class="d-block">{{auth()->user()->roles()->where('activo',1)->first()->nombreRol}}</span>
                </div>
            </div>
            <ul class="menu" id="menuIntranet">
                <li class="hover-menu {{Request::route()->getName() == 'home' ? 'activesub' : ''}}">
                    <a href="{{route('home')}}" class="menu-prin">
                        <span class="icono material-icons">home</span>
                        <span class="title">Inicio</span>
                    </a>
                </li>
                @php
                    $grupo = null;
                @endphp
                @foreach ($modulos as $k => $modulo)
                    @if($grupo != $modulo->grupos->id)
                        <li class="hover-menu display-submenu" role="button" data-toggle="collapse" data-target="#menuCollapse{{$modulo->grupos->id}}" aria-expanded="{{Request::route()->getName() == $modulo->url ? 'true' : 'false'}}" aria-controls="collapse{{$modulo->grupos->id}}">
                            <a href="javascript:void(0)" class="active-panel">
                                <span class="icono material-icons">{{$modulo->grupos->icono}}</span>
                                <span class="title">{{$modulo->grupos->grupo}}</span>
                                <span class="material-icons">expand_more</span>
                            </a>
                        </li>
                        <ul class="sub-menu collapse" id="menuCollapse{{$modulo->grupos->id}}" data-parent="#menuIntranet">
                        @php
                            $grupo = $modulo->grupos->id;
                        @endphp
                    @endif
                    <li class="hover-menu {{Request::route()->getName() == $modulo->url ? 'activeli' : ''}}">
                        <a href="{{route($modulo->url)}}">
                            <span class="icono material-icons">{{$modulo->icono}}</span>
                            <span class="title">{{$modulo->titulo}}</span>
                        </a>
                    </li>
                    @if (isset($modulos[$k+1]) && $modulo->grupos->id != $modulos[$k+1]->grupos->id || $modulos->count() == ($k + 1))
                        </ul>
                    @endif
                      
                @endforeach
                <li class="hover-menu">
                    <a href="{{route('cerrarSesion')}}">
                        <span class="icono material-icons">logout</span>
                        <span class="title">Cerrar sesión</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="panel">
            <div class="box-serch" hidden>
                <input type="search">
            </div>
            <div class="info-person">
                <div class="btn-menu">
                    <button class="btn-info-menu" type="button" id="menuDesplegable">
                        <span class="material-icons">menu</span>
                    </button>
                    
                </div>
                <div class="box-my">
                    <button class="btn-info-menu">
                        <span class="material-icons">search</span>
                    </button>
                    <div>
                        <button class="btn-info-menu">
                            <span class="material-icons">notifications</span>
                        </button>
                    </div>
                    <div class="info-perfil">
                        <span class="d-block">{{auth()->user()->nombres}}</span>
                    </div>
                    <div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-light dropdown-toggle toggle-split" data-toggle="dropdown" aria-expanded="false">
                                <img src="{{$urlPerfil}}" class="avatar-menu-img" width="30px" height="30px">
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <h6 class="dropdown-header" id="lista_roles_index">Roles</h6>
                                @foreach (auth()->user()->roles as $role)
                                    <a class="dropdown-item {{$role->pivot->activo == 1 ? "active" : ""}}" href="{{route('cambiarRol',['rol' => $role->id])}}">{{$role->nombreRol}}</a>
                                @endforeach
                                <div class="dropdown-divider"></div>
                                <a href="{{route('miPerfil')}}" class="dropdown-item"><span>Mi perfil</span></a>
                                <a href="{{route('cerrarSesion')}}" class="dropdown-item"><span>Cerrar sesión</span></a>
                            </div>
                          </div>
                    </div>
                    
                    
                </div>
               
            </div>
        </div>
        <div class="cuerpo-panel">
            @yield('body')
        </div>
    </div>
    <div class="cargar-general" id="banerCargando" hidden>
        <span class="loader-baner"></span>
    </div>
</body>
</html>