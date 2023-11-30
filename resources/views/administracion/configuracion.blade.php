@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/administrador/configuracion.js?v1.1"></script>
    <title>Mi configuración</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/proceso.png" alt="Imagen de configuración" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Configurar Negocio</h4>
            </div>
        </div>
        <form id="configuracionMiNegocio" class="form-row">
            <div class="form-group col-12 col-md-6">
                <fieldset class="bg-white col-12 px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos del Negocio</legend>
                        <div class="col-12 form-group">
                            <label for="razon_social_id" class="col-form-label col-form-label-sm">Razón social larga</label>
                            <input type="text" value="{{$configuracion[16]->valor}}" class="form-control form-control-sm" id="razon_social_id" name="razon_social" maxlength="255" required>
                        </div>
                        <div class="col-12 form-group">
                            <label for="razon_social_id" class="col-form-label col-form-label-sm">Razón social corta</label>
                            <input type="text" value="{{$configuracion[0]->valor}}" class="form-control form-control-sm" id="razon_social_id" name="razon_social" maxlength="255" required>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 form-group">
                            <label for="id_ruc" class="col-form-label col-form-label-sm">RUC</label>
                            <input type="text" maxlength="11" value="{{$configuracion[1]->valor}}" name="ruc" id="id_ruc" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 form-group">
                            <label for="id_telefono" class="col-form-label col-form-label-sm">Teléfono</label>
                            <input type="tel" maxlength="15" value="{{$configuracion[3]->valor}}" name="telefono" id="id_telefono" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 form-group">
                            <label for="id_celular" class="col-form-label col-form-label-sm">Celular</label>
                            <input type="tel" maxlength="15" value="{{$configuracion[2]->valor}}" name="celular" id="id_celular" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label for="id_correo" class="col-form-label col-form-label-sm">Correo</label>
                            <input type="email" maxlength="255" value="{{$configuracion[4]->valor}}" name="correo" id="id_correo" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label for="id_pagina_web" class="col-form-label col-form-label-sm">Página Web</label>
                            <input type="url" maxlength="255" value="{{$configuracion[5]->valor}}" name="pagina_web" id="id_pagina_web" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 form-group">
                            <label for="id_direccion" class="col-form-label col-form-label-sm">Dirección</label>
                            <input type="text" maxlength="255" value="{{$configuracion[6]->valor}}" name="direccion" id="id_direccion" class="form-control form-control-sm">
                        </div>               
                </fieldset>
                <fieldset class="bg-white col-12 px-3 border form-row mb-3">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Redes Sociales</legend>
                    <div class="col-12 col-md-6 form-group">
                        <label for="id_red_social_facebook" class="col-form-label col-form-label-sm">Facebook</label>
                        <input type="url" maxlength="255" value="{{$configuracion[10]->valor}}" name="red_social_facebook" id="id_red_social_facebook" class="form-control form-control-sm">
                    </div>
                    <div class="col-12 col-md-6 form-group">
                        <label for="id_red_social_instagram" class="col-form-label col-form-label-sm">Instagram</label>
                        <input type="url" maxlength="255" value="{{$configuracion[11]->valor}}" name="red_social_instagram" id="id_red_social_instagram" class="form-control form-control-sm">
                    </div>
                    <div class="col-12 col-md-6 form-group">
                        <label for="id_red_social_tiktok" class="col-form-label col-form-label-sm">TikTok</label>
                        <input type="url" maxlength="255" value="{{$configuracion[12]->valor}}" name="red_social_tiktok" id="id_red_social_tiktok" class="form-control form-control-sm">
                    </div>
                    <div class="col-12 col-md-6 form-group">
                        <label for="id_red_social_twitter" class="col-form-label col-form-label-sm">Twitter</label>
                        <input type="url" maxlength="255" value="{{$configuracion[13]->valor}}" name="red_social_twitter" id="id_red_social_twitter" class="form-control form-control-sm">
                    </div>
                    <div class="col-12 col-md-6 form-group">
                        <label for="id_red_social_whatsapp" class="col-form-label col-form-label-sm">WhatsApp</label>
                        <input type="tel" value="{{$configuracion[14]->valor}}" name="red_social_whatsapp" id="id_red_social_whatsapp" class="form-control form-control-sm">
                    </div>
                    
                </fieldset>
            </div>
            <div class="form-group col-12 col-md-6">
                <fieldset class="bg-white col-12 px-3 border form-row mb-3">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos del Propietario</legend>
                        <div class="col-6 form-group">
                            <label for="id_propietario_tipo_documento" class="col-form-label col-form-label-sm">Tipo Documento</label>
                            <select id="id_propietario_tipo_documento" name="propietario_tipo_documento" class="form-control form-control-sm select2-simple">
                                <option value=""></option>
                                @foreach ($tiposDocumentos as $tipoDocumento)
                                    <option value="{{$tipoDocumento->id}}" {{$tipoDocumento->id == $configuracion[7]->valor ? 'selected' : ''}}>{{$tipoDocumento->documento}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 form-group">
                            <label for="id_propietario_nro_documento" class="col-form-label col-form-label-sm">N° Documento</label>
                            <input type="tel" value="{{$configuracion[8]->valor}}" id="id_propietario_nro_documento" name="propietario_nro_documento" maxlength="20" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 form-group">
                            <label for="id_propietario_apellidos_nombre" class="col-form-label col-form-label-sm">Apellidos y nombres</label>
                            <input type="text" value="{{$configuracion[9]->valor}}" name="propietario_apellidos_nombre" id="id_propietario_apellidos_nombre" maxlength="255" class="form-control form-control-sm">
                        </div>
                </fieldset>
                <fieldset class="bg-white col-12 px-3 mb-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Cuentas Bancarias</legend>
                    <div class="col-12">
                        <textarea id="sumernoteNumeroCuenta">
                            {!! $configuracion[15]->valor !!}
                        </textarea>
                    </div>
                </fieldset>
                <fieldset class="bg-white col-12 px-3 border form-row align-items-center">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Otros</legend>
                    <input type="file" name="formatoVisita" class="form-control" accept=".pdf" id="archivoFormatoVisita" hidden>
                    <div class="form-group col-12 d-flex flex-wrap" style="gap: 8px;">
                        <button class="btn btn-sm btn-primary" id="subirFormatoVisita" type="button" title="Cambiar formato único de visitas">
                            <i class="fas fa-sync-alt"></i>
                            <span>Formato único de visitas</span>
                        </button>
                        <span id="documentoVisita" download="{{$configuracion[17]->valor}}">
                            <i class="fas fa-download"></i>
                            <span>{{$configuracion[17]->valor}}</span>
                        </span>
                    </div>
                </fieldset>
            </div>
            <div class="col-12 form-group text-center">
                <button type="submit" class="btn btn-success" id="btnSubmitNegocio">
                    <i class="fas fa-pencil-alt"></i>
                    <span>Actualizar Datos</span>
                </button>
            </div>
        </form>
    </section>
@endsection