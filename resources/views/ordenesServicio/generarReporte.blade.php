@extends('helper.index')
@section('head')
    <script src="/library/tinyeditor/tinyeditor.js"></script>
    <script src="/library/tinyeditor/es.js"></script>
    <script src="/ordenServicio/compartidoOs.js?v1.1"></script>
    <script src="/ordenServicio/generarInforme.js?v1.1"></script>
    <title>Generar reporte</title>
@endsection
@section('body')
    <style>
        .posicion-visible{
            position: sticky;
            top: -20px;
            background: var(--color-principal);
            z-index: 100;
            color: #ffffff;
        }
        .img-guias {
            display: block;
            width: 200px;
            min-width: 200px;
            object-fit: contain;
            height: 100px;
            margin: auto;
        }
        .contenido-img{
            position: relative;
        }
        .contenido-img button{
            position: absolute;
            top: 0;
            right: 15px;
        }
        .pagination .page-link{
            border-color: var(--activo-li-navegacion) !important;
            color: var(--activo-li-navegacion) !important;
        }
        .pagination .page-link:focus{
            box-shadow: 0 0 0 0.2rem rgba(29, 87, 2, 0.25);
        }
        .pagination .active .page-link{
            background-color: var(--activo-li-navegacion) !important;
            color: #ffffff !important;

        }
    </style>
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 400px;">
                <img src="/img/modulo/notas.png" alt="Imagen de un libro de notas" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Generar nuevo informe</h4>
            </div>
        </div>
        <form class="form-group" method="GET" action="{{route('informe.generar')}}">
            <fieldset class="bg-white px-3 border form-row">
                <legend class="bg-white d-inline-block w-auto px-2 border shadow-sm text-left legend-add">Filtros</legend>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                        <label for="cbPreCotizacion" class="col-form-label col-form-label-sm">Clientes</label>
                        <select name="cliente" id="cbClientes" required class="form-control select2-simple" data-tags="true" data-placeholder="Seleccione un cliente">
                            <option value=""></option>
                            @foreach ($clientes as $cliente)
                                <option {{$cliente->id == $idCliente ? 'selected' : ''}} value="{{$cliente->id}}">{{$cliente->nombreCliente}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                        <label for="cbOrdenServicio" class="col-form-label col-form-label-sm">Ordenes de servicio</label>
                        <select name="ordenServicio" id="cbOrdenServicio" required class="form-control select2-simple" data-tags="true" data-placeholder="Seleccione una orden de servicio">
                            <option value=""></option>
                            @foreach ($listaOs as $os)
                                <option value="{{$os->id}}" {{$os->id == $idOs ? 'selected' : ''}}>{{$os->nroOs}}</option>
                            @endforeach                         
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
            </fieldset>
        </form>
        @if (!is_null($ordenServicio))
            <div class="p-3 bg-white form-row" id="contenidoInformes">
                @foreach ($ordenServicio->servicios as $servicio)
                <div class="col-12 form-group">
                    <h4 class="text-primary mb-0">
                        <i class="fas fa-caret-right"></i>
                        Lista de servicios               
                    </h4>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header posicion-visible">
                            <h5 class="card-title mb-0">{{$servicio->cotizacionServicio->servicios->servicio}}</h5>
                        </div>
                      <div class="card-body">
                        <div class="card-text form-row">
                            <div class="form-group col-12 col-md-6 col-lg-4">
                                <label for="fechaTermino{{$servicio->id}}">Fecha termino</label>
                                <input required id="fechaTermino{{$servicio->id}}" name="fecha_termino[]" type="date" class="form-control form-control-sm">
                            </div>
                            <div class="form-group col-12">
                                <label for="objetivos{{$servicio->id}}">Objetivos</label>
                                <textarea class="informe" data-os="{{$ordenServicio->id}}" data-servicio="{{$servicio->id}}" data-columna="objetivos" required data-height="200px" name="obetivos[]" id="objetivos{{$servicio->id}}">{{$servicio->objetivos}}</textarea>
                            </div>
                            <div class="form-group col-12">
                                <label for="acciones{{$servicio->id}}">Acciones</label>
                                <textarea class="informe" data-os="{{$ordenServicio->id}}" data-servicio="{{$servicio->id}}" data-columna="acciones" required name="acciones[]" id="acciones{{$servicio->id}}">{{$servicio->acciones}}</textarea>
                            </div>
                            <div class="form-group col-12">
                                <label for="descripciones{{$servicio->id}}">Descripciones</label>
                                <textarea class="informe" data-os="{{$ordenServicio->id}}" data-servicio="{{$servicio->id}}" data-columna="descripcion" required name="descripciones[]" id="descripciones{{$servicio->id}}">{{$servicio->descripcion}}</textarea>
                            </div>
                            <div class="form-group col-12">
                                <div class="d-flex justify-content-between flex-wrap" style="gap:5px;">
                                    <h5 class="text-primary mb-0">
                                        <i class="fas fa-caret-right"></i>
                                        Lista de secciones
                                    </h5>
                                    <button data-toggle="tooltip" data-placement="top" title="Agregar una sección" class="btn btn-sm btn-light agregar-seccion" data-servicio="{{$servicio->id}}" data-os="{{$ordenServicio->id}}" data-contenido="#contenidoSeccionServicio{{$servicio->id}}" type="button">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group col-12" id="contenidoSeccionServicio{{$servicio->id}}">
                                @if ($servicio->secciones->count() === 0)
                                    <div class="text-center contenido-vacio">
                                        <span>No se agregaron secciones</span>
                                    </div>
                                @else
                                    @foreach ($servicio->secciones as $ks => $seccion)
                                        <div class="form-group p-2">
                                            <div class="justify-content-between align-items-center d-flex" style="gap: 5px;">
                                                <h6 class="mb-0 nombre-seccion">
                                                    <i class="fas fa-caret-right"></i>
                                                    Sección N° {{$ks + 1}}
                                                </h6>
                                                <div class="align-items-center d-flex" style="gap: 5px;">
                                                    <button class="btn btn-sm btn-light" data-toggle="tooltip" data-placement="top" title="Número de columnas" type="button" id="servicio{{$servicio->id}}Seccion{{$seccion->id}}Columna">
                                                        <i class="fas fa-columns"></i>
                                                        <span>{{$seccion->columnas}}</span>
                                                    </button>
                                                    <button class="btn btn-sm editar-seccion btn-info" data-servicio="{{$servicio->id}}" data-toggle="tooltip" data-placement="top" title="Editar sección" data-os="{{$ordenServicio->id}}" data-seccion="{{$seccion->id}}" type="button">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                    <button class="btn btn-sm eliminar-seccion btn-danger" data-servicio="{{$servicio->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar sección" data-os="{{$ordenServicio->id}}" data-seccion="{{$seccion->id}}" type="button">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>   
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="servicio{{$servicio->id}}Seccion{{$seccion->id}}">Título de la sección</label>
                                                <input required readonly id="servicio{{$servicio->id}}Seccion{{$seccion->id}}" type="text" class="form-control form-control-sm" value="{{$seccion->titulo}}">
                                            </div>
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between flex-wrap" style="gap:5px;">
                                                    <h6 class="text-primary mb-0">
                                                        <i class="fas fa-caret-right"></i>
                                                        Imágenes de la sección
                                                    </h6>
                                                    <button class="btn btn-sm btn-light" data-contenido="#contenidoImagenes{{$servicio->id}}Seccion{{$seccion->id}}" data-toggle="tooltip" data-placement="top" title="Agregar una imagen" type="button">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="contenidoImagenes{{$servicio->id}}Seccion{{$seccion->id}}">
                                                @if($seccion->imagenes->count() === 0)
                                                    <div class="text-center contenido-vacio">
                                                        <span>No se agregaron imágenes para esta sección</span>
                                                    </div>
                                                @else
                                                    <div class="form-group row">
                                                        @foreach ($seccion->imagenes as $imagen)
                                                            <div class="col-12 col-lg-6 form-group contenido-img">
                                                                <div class="form-group">
                                                                    <img src="{{route('urlImagen',['productos','bomba espa_1693769746.jpeg'])}}" alt="Imagen {{$imagen->descripcion}}" class="img-guias">
                                                                </div>
                                                                <textarea class="form-control form-control-sm" rows="2">{{$imagen->descripcion}}</textarea>
                                                                <button class="btn btn-sm btn-danger" data-servicio="{{$servicio->id}}" data-os="{{$ordenServicio->id}}" data-seccion="{{$seccion->id}}" data-img="{{$imagen->id}}" type="button">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        <div>
                      </div>
                    </div>
                  </div>
                @endforeach
            </div>
        @else
            <div class="p-3 bg-white" id="contenidoInformes">
                <div class="m-auto">
                    <div class="form-group">
                        <img src="/img/modulo/sin-contenido.png" alt="Imagen de sin contenido" width="120px" class="img-fluid d-block m-auto">
                    </div>
                    <h5 class="text-center text-primary">No se encontraron resultados para esta busqueda</h5>
                </div>
            </div>
        @endif
    </section>
    @if (!is_null($ordenServicio))
        @include('ordenesServicio.modales.agregarSeccion')
    @endif
@endsection