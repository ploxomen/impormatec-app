@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/ordenServicio/generarCertificado.js?v1.5"></script>
    <title>Certificado</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/certificado.png" alt="Imagen de un certificado con una insignia" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Administrar certificado</h4>
            </div>
        </div>
        <div class="container">
            <div class="form-group row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                    <strong>N° Certificado: </strong>
                    <span>{{str_pad($cetificadoOperativo->id,5,"0",STR_PAD_LEFT)}}</span>
                </div>
                <div class="col-12 col-md-6 col-lg-8 col-xl-4 form-group">
                    <strong>Servicio: </strong>
                    <span>{{$cetificadoOperativo->ordenServicioCotizacion->cotizacionServicio->servicios->servicio}}</span>
                </div>
                <div class="col-12 col-lg-5 form-group">
                    <strong>Profesional firmante: </strong>
                    <span>{{$cetificadoOperativo->ordenServicioCotizacion->usuario->nombres . ' ' .$cetificadoOperativo->ordenServicioCotizacion->usuario->apellidos}}</span>
                </div>
            </div>
        </div>
        <div class="bg-white p-3">
            <div class="form-group">
                <h4 class="text-primary mb-0">
                    <i class="fas fa-caret-right"></i>
                    Datos del certificado
                </h4>
            </div>
            <div class="card">
                <div class="card-header" style="background: var(--color-principal);">
                    <h5 class="text-white mb-0">
                        {{$cetificadoOperativo->ordenServicioCotizacion->cotizacionServicio->servicios->servicio}}
                    </h5>
                </div>
                <div class="card-body">
                    <form class="form-group form-row" id="formCertificado">
                        <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                            <label for="txtLugar">Lugar</label>
                            <input type="text" required class="form-control" id="txtLugar" name="lugar" value="{{empty($cetificadoOperativo->lugar) ? 'Lima' : $cetificadoOperativo->lugar}}">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 col-xl-2">
                            <label for="txtFecha">Fecha</label>
                            <input type="date" required class="form-control" id="txtFecha" name="fecha" value="{{empty($cetificadoOperativo->fecha) ? date('Y-m-d') : $cetificadoOperativo->fecha}}">
                        </div>
                        <div class="form-group col-12 col-lg-6 col-xl-8">
                            <label for="txtLugar">Asunto</label>
                            <input type="text" required class="form-control" id="txtLugar" name="asunto" value="{{empty($cetificadoOperativo->asunto) ? 'Certificado de Operatividad del ' . $cetificadoOperativo->ordenServicioCotizacion->cotizacionServicio->servicios->servicio  : $cetificadoOperativo->asunto}}">
                        </div>
                        <div class="form-group col-12">
                            <label for="txtDescripcionCertificado">Descripción</label>
                            <textarea id="txtDescripcionCertificado">{{$cetificadoOperativo->descripcion}}</textarea>
                        </div>
                        <div class="form-group col-12 text-center">
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="far fa-save"></i>
                                <span>Guardar cambios</span>
                            </button>
                            <button type="button" id="visualizarCertificado" class="btn btn-sm btn-danger" target="_blank">
                                <i class="fas fa-eye"></i>
                                <span>Ver certificado</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </section>
@endsection