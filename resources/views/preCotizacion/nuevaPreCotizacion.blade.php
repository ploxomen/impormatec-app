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
        <form id="configuracionMiNegocio" class="form-row">
            <div class="form-group col-12 col-md-6">
                <fieldset class="bg-white col-12 px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Datos del cliente</legend>
                        <div class="col-12 col-md-6 form-group">
                            <label for="cbCliente" class="col-form-label col-form-label-sm">Cliente</label>
                            <select name="id_cliente" id="cbCliente" class="form-control select2-simple" required data-placeholder="Seleccione un cliente">
                                <option value=""></option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label for="cbContactoCliente" class="col-form-label col-form-label-sm">Contacto</label>
                            <select name="id_cliente_contacto" id="cbContactoCliente" class="form-control select2-simple" data-placeholder="Seleccione un contacto"></select>
                        </div>
                        <div class="col-12 form-group">
                            <label for="id_direccion" class="col-form-label col-form-label-sm">Dirección</label>
                            <textarea name="" required class="form-control" id="" cols="30" rows="2"></textarea>
                        </div>               
                </fieldset>
            </div>
            <div class="form-group col-12 col-md-6">
                <fieldset class="bg-white col-12 px-3 border form-row mb-3">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Descripción del servicio</legend>
                        <div class="col-12 form-group">
                            <label for="id_propietario_tipo_documento" class="col-form-label col-form-label-sm">Servicio</label>
                            <select name="" class="form-control select2-simple" data-placeholder="Seleccione un servicio" required id="">
                                <option value=""></option>
                                @foreach ($servicios as $servicio)
                                    <option value="{{$servicio->id}}">{{$servicio->servicio}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <label for="id_propietario_nro_documento" class="col-form-label col-form-label-sm">Descripción del servicio</label>
                            <textarea name="" class="form-control" id="" cols="30" rows="2"></textarea>
                        </div>
                </fieldset>
            </div>
            <div class="form-group col-12">
                <fieldset class="bg-white col-12 px-3 border form-row">
                    <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Programación</legend>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-6 form-group">
                            <label for="id_abrir_caja" class="col-form-label col-form-label-sm">Técnico</label>
                            <select name="" class="form-control select2-simple" id="" required data-placeholder="Seleccione un técnico">
                                <option value=""></option>
                                @foreach ($tecnicos as $tecnico)
                                    <option value="{{$tecnico->id}}">{{$tecnico->nombres . ' ' . $tecnico->apellidos}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                            <label for="id_cerrar_caja" class="col-form-label col-form-label-sm">Fecha de visita</label>
                            <input type="date" name="cerrar_caja" required class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                            <label for="id_cerrar_caja" class="col-form-label col-form-label-sm">Hora de visita</label>
                            <input type="time" name="cerrar_caja" required class="form-control form-control-sm">
                        </div>
                        <div class="col-12 form-group">
                            <label for="id_propietario_nro_documento" class="col-form-label col-form-label-sm">Detalle</label>
                            <textarea name="" class="form-control" id="" rows="2"></textarea>
                        </div>
                </fieldset>
            </div>
            <div class="col-12 form-group text-center">
                <button type="submit" class="btn btn-primary" id="btnSubmitNegocio">
                    <i class="fas fa-plus"></i>
                    <span>Agregar</span>
                </button>
            </div>
        </form>
    </section>
@endsection