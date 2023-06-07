@extends('helper.index')
@section('head')
    <script src="/preCotizacion/nuevaPreCotizacion.js"></script>
    <title>Nueva pre cotización</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/solicitud-de-cotizacion.png" alt="Imagen de configuración" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Nueva Pre - Cotización</h4>
            </div>
        </div>
        <form id="frmPreCotizacion" class="form-row">
            <div class="form-group col-12">
                <fieldset class="bg-white col-12 px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos del cliente</legend>
                        <div class="col-12 form-group">
                            <label for="cbCliente" class="col-form-label col-form-label-sm">Cliente</label>
                            <select name="id_cliente" id="cbCliente" class="form-control select2-simple" data-tags="true" required data-placeholder="Seleccione un cliente">
                                <option value=""></option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                                @endforeach
                            </select>
                            <small hidden class="form-text text-muted">Si es un nuevo cliente no olvides que la contraseña temporal es: sistema{{date('Y')}}@</small>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4 form-required">
                            <label for="idModaltipoDocumento">Tipo Documento</label>
                            <select name="tipoDocumento" id="idModaltipoDocumento" class="select2-simple limpiar-frm">
                                <option value=""></option>
                                @foreach ($tiposDocumentos as $tipoDocumento)
                                    <option value="{{$tipoDocumento->id}}">{{$tipoDocumento->documento}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4 form-required">
                            <label for="idModalnroDocumento">N° Documento</label>
                            <input type="text" name="nroDocumento" class="form-control limpiar-frm" id="idModalnroDocumento">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <label for="txtCorreo">Correo</label>
                            <input type="email" name="correo" class="form-control limpiar-frm" id="idModalcorreo" required>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3">
                            <label for="idModalcelular">Celular</label>
                            <input type="tel" name="celular" class="form-control limpiar-frm" id="idModalcelular">
                        </div>
                        
                        <div class="form-group col-12 col-md-6 col-lg-3">
                            <label for="idModaltelefono">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control limpiar-frm" id="idModaltelefono">
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label for="cbContactoCliente" class="col-form-label col-form-label-sm">Contacto</label>
                            <select name="id_cliente_contacto[]" id="cbContactoCliente" multiple class="form-control select2-simple limpiar-frm" data-placeholder="Seleccione los contactos"></select>
                            <small hidden class="form-text text-muted">Para diferenciar el nombre del número dividelo con un "-" (guion)</small>
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtdireccion" class="col-form-label col-form-label-sm">Dirección</label>
                            <input type="text" name="direccion" id="idModaldireccion" required class="form-control limpiar-frm">
                        </div>               
                </fieldset>
            </div>
            <div class="form-group col-12">
                <fieldset class="bg-white col-12 px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Programación</legend>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                            <label for="cbTecnicoResponsable" class="col-form-label col-form-label-sm">Técnico responsable</label>
                            <select name="cbTecnicoResponsable" class="form-control select2-simple" id="cbTecnicoResponsable" required data-placeholder="Seleccione un técnico">
                                <option value=""></option>
                                @foreach ($tecnicos as $tecnico)
                                    <option value="{{$tecnico->id}}">{{$tecnico->nombres . ' ' . $tecnico->apellidos}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-6 form-group">
                            <label for="cbOtrosTecnicos" class="col-form-label col-form-label-sm">Otros técnicos</label>
                            <select name="cbOtrosTecnicos[]" class="form-control select2-simple" id="cbOtrosTecnicos" multiple data-placeholder="Seleccione un técnico">
                                <option value=""></option>
                                @foreach ($tecnicos as $tecnico)
                                    <option value="{{$tecnico->id}}">{{$tecnico->nombres . ' ' . $tecnico->apellidos}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                            <label for="txtfechaHrVisita" class="col-form-label col-form-label-sm">Fecha y Hr. de visita</label>
                            <input type="datetime-local" id="txtfechaHrVisita" name="fecha_hr_visita" required class="form-control form-control-sm">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtDescripcion" class="col-form-label col-form-label-sm">Detalles</label>
                            <textarea name="detalle" class="form-control" id="txtDescripcion" rows="2"></textarea>
                        </div>
                </fieldset>
            </div>
            <div class="col-12 form-group text-center">
                <button type="submit" class="btn btn-primary" id="btnAgregarPreCoti">
                    <i class="fas fa-plus"></i>
                    <span>Agregar</span>
                </button>
            </div>
        </form>
    </section>
@endsection