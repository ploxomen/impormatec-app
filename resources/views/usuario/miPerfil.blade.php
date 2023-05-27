@extends('helper.index')
@section('head')
    <script src="{{asset('general.js')}}"></script>
    <link rel="stylesheet" href="{{asset('usuario/miPerfil.css')}}">
    <script src="{{asset('usuario/miPerfil.js')}}"></script>
    <title>Mi Perfil</title>
@endsection
@section('body')
    <main class="container">
        <section class="mt-5">
            <form id="formUpdatePerfil" class="border p-3 form-row bg-white m-auto position-relative" style="max-width: 800px;">
                <div class="avatar-form">
                    @php
                        $usuario = auth()->user();
                        $urlPerfil = $usuario->sexo == "F" ? "mujer" : "hombre";
                        if(!empty($usuario->urlAvatar)){
                            $urlPerfil = route("urlImagen",["avatars",$usuario->urlAvatar]);
                        }else{
                            $urlPerfil = asset('img/modulo/perfil_' . $urlPerfil .'.png');
                        }
                    @endphp
                    <img src="{{$urlPerfil}}" class="avatar-menu-img" width="80px" height="80px" alt="Imagen de avatar" id="previewAvatar">
                    <button class="btn btn-light border btn-sm btn-add-img" type="button" id="btnCargarAvatar">
                        <span class="material-icons text-secondary" style="font-size: 20px">
                            photo_camera
                        </span>
                    </button>
                    <input name="avatar" type="file" hidden id="file-avatar" accept="image/*">
                </div>
                <div class="form-group col-12 col-md-6 mt-5">
                    <label for="idTipoDocumento">
                        <span class="material-icons">
                            badge
                        </span>
                        <span>Tipo Documento</span>
                    </label>
                    <select name="tipoDocumento" id="idTipoDocumento" class="form-control select2-simple">
                        <option value="">Ninguno</option>
                        @foreach ($tiposDocumentos as $tipoDocumento)
                            <option value="{{$tipoDocumento->id}}" {{$tipoDocumento->id == auth()->user()->tipoDocumento ? 'selected' : ''}}>{{$tipoDocumento->documento}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 mt-md-5">
                    <label for="idNumeroDoc">
                        <span class="material-icons">
                            assignment_ind
                        </span>
                        <span>N° Documento</span>
                    </label>
                    <input type="text" name="nroDocumento" class="form-control" id="idNumeroDoc" maxlength="20" value="{{auth()->user()->nroDocumento}}">
                </div>
                <div class="form-group col-6 col-lg-4">
                    <label for="idNombres">
                        <span class="material-icons">
                            person
                        </span>
                        <span>Nombres</span>
                    </label>
                    <input type="text" readonly class="form-control" id="idNombres" required value="{{auth()->user()->nombres}}">
                </div>
                <div class="form-group col-6 col-lg-4">
                    <label for="idApellidos">
                        <span class="material-icons">
                            person
                        </span>
                        <span>Apellidos</span>
                    </label>
                    <input type="text" readonly class="form-control" id="idApellidos" required value="{{auth()->user()->apellidos}}">
                </div>
                <div class="form-group col-12 col-lg-4 col-md-6">
                    <label for="idCorreo">
                        <span class="material-icons">
                            alternate_email
                        </span>
                        <span>Correo</span>
                    </label>
                    <input type="email" readonly class="form-control" id="idCorreo" required value="{{auth()->user()->correo}}">
                </div>
                <div class="form-group col-12">
                    <label for="idDireccion">
                        <span class="material-icons">
                            home
                        </span>
                        <span>Dirección</span>
                    </label>
                    <input type="text" class="form-control" name="direccion" id="idDireccion" maxlength="255" value="{{auth()->user()->direccion}}">
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="idTelefono">
                        <span class="material-icons">
                            call
                        </span>
                        <span>Teléfono</span>
                    </label>
                    <input type="tel" class="form-control" id="idTelefono" name="telefono" maxlength="20" value="{{auth()->user()->telefono}}">
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="idCelular">
                        <span class="material-icons">
                            phone_iphone
                        </span>
                        <span>Celular</span>
                    </label>
                    <input type="tel" class="form-control" id="idCelular" name="celular" maxlength="20" value="{{auth()->user()->celular}}">
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="idFechaCumple">
                        <span class="material-icons">
                            calendar_month
                        </span>
                        <span>Fecha Nacimiento</span>
                    </label>
                    <input type="date" class="form-control" id="idFechaCumple" name="fechaCumple" value="{{auth()->user()->fechaCumple}}">
                </div>
                <div class="form-group col-6 col-lg-3">
                    <label for="idSexo">
                        <span class="material-icons">
                            man
                        </span>
                        <span>Sexo</span>
                    </label>
                    <select name="sexo" id="idSexo" class="form-control select2-simple">
                        <option value="">Ninguno</option>
                        <option value="M" {{auth()->user()->sexo == "M" ? "selected" : ""}}>Masculino</option>
                        <option value="F" {{auth()->user()->sexo == "F" ? "selected" : ""}}>Femenino</option>
                    </select>
                </div>
                <div class="form-group col-12 text-center">
                    <button class="btn btn-outline-primary" id="btnActualizar">
                        <i class="far fa-save"></i>
                        <span>
                            Actualizar
                        </span>
                    </button>
                </div>
            </form>

        </section>
    </main>
@endsection