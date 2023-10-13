@if (!is_null($ordenServicio))
    <div class="col-12 form-group">
        <h4 class="text-primary mb-0">
            <i class="fas fa-caret-right"></i>
            Lista de servicios               
        </h4>
    </div>
    <div class="p-3 bg-white form-row" id="contenidoInformes">
        @foreach ($ordenServicio->servicios as $servicio)
        <div class="form-group col-12">
            <div class="card form-group">
                <div class="card-header posicion-visible d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{$servicio->cotizacionServicio->servicios->servicio}}</h5>
                    <a href="{{route('reporte.previo.informe',[$ordenServicio->id,$servicio->id])}}" target="_blank" class="btn btn-sm btn-light" data-toggle="tooltip" data-placement="top" title="Vista previa del informe">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="card-text form-row">
                        <div class="form-group col-12 col-md-6 col-lg-4">
                            <label for="fechaTermino{{$servicio->id}}">Fecha termino</label>
                            <input required id="fechaTermino{{$servicio->id}}" data-servicio="{{$servicio->id}}" data-os="{{$ordenServicio->id}}" value="{{$servicio->fecha_termino}}" type="date" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-12">
                            <label for="objetivos{{$servicio->id}}">Objetivos</label>
                            <textarea class="informe" data-os="{{$ordenServicio->id}}" data-servicio="{{$servicio->id}}" data-columna="objetivos" required data-height="200px" id="objetivos{{$servicio->id}}">{{$servicio->objetivos}}</textarea>
                        </div>
                        <div class="form-group col-12">
                            <label for="acciones{{$servicio->id}}">Acciones</label>
                            <textarea class="informe" data-os="{{$ordenServicio->id}}" data-servicio="{{$servicio->id}}" data-columna="acciones" required id="acciones{{$servicio->id}}">{{$servicio->acciones}}</textarea>
                        </div>
                        <div class="form-group col-12">
                            <label for="descripciones{{$servicio->id}}">Descripciones</label>
                            <textarea class="informe" data-os="{{$ordenServicio->id}}" data-servicio="{{$servicio->id}}" data-columna="descripcion" required id="descripciones{{$servicio->id}}">{{$servicio->descripcion}}</textarea>
                        </div>
                        <div class="form-group col-12">
                            <label for="conclusionesReomendaciones{{$servicio->id}}">Conclusiones y recomendaciones</label>
                            <textarea class="informe" data-os="{{$ordenServicio->id}}" data-servicio="{{$servicio->id}}" data-columna="conclusiones_recomendaciones" required id="conclusionesReomendaciones{{$servicio->id}}">{{$servicio->conclusiones_recomendaciones}}</textarea>
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
                                                <input hidden type="file" multiple accept="image/*" id="imagenServicio{{$servicio->id}}Seccion{{$seccion->id}}" data-servicio="{{$servicio->id}}" data-os="{{$ordenServicio->id}}" data-seccion="{{$seccion->id}}" data-contenido="#contenidoImagenes{{$servicio->id}}Seccion{{$seccion->id}}">
                                                <button class="btn btn-sm agregar-imagen btn-light" data-file="#imagenServicio{{$servicio->id}}Seccion{{$seccion->id}}" data-toggle="tooltip" data-placement="top" title="Agregar una imagen" type="button">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="contenidoImagenes{{$servicio->id}}Seccion{{$seccion->id}}" class="form-group row">
                                            @if($seccion->imagenes->count() === 0)
                                                <div class="text-center contenido-vacio-img col-12">
                                                    <span>No se agregaron imágenes para esta sección</span>
                                                </div>
                                            @else
                                                @foreach ($seccion->imagenes as $imagen)
                                                    <div class="col-12 col-xl-6 form-group contenido-img">
                                                        <div class="form-group">
                                                            <img loading="lazy" src="{{route('urlImagen',["informeImgSeccion",$imagen->url_imagen])}}" alt="Imagen {{$imagen->descripcion}}" class="img-guias">
                                                        </div>
                                                        <textarea class="form-control contenido-descripcion form-control-sm" rows="2" data-servicio="{{$servicio->id}}" data-os="{{$ordenServicio->id}}" data-seccion="{{$seccion->id}}" data-imagen="{{$imagen->id}}">{{$imagen->descripcion}}</textarea>
                                                        <button class="btn btn-sm eliminar-img btn-danger" data-servicio="{{$servicio->id}}" data-os="{{$ordenServicio->id}}" data-seccion="{{$seccion->id}}" data-img="{{$imagen->id}}" type="button">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @if ($botonGenerar)
    <div class="form-group text-center">
        <button class="btn btn-sm btn-primary" id="generarInforme" data-os="{{$ordenServicio->id}}" type="button">
            <i class="fas fa-paper-plane"></i>
            <span>Generar informe</span>
        </button>
    </div>
    @endif
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