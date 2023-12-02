@extends('helper.index')
@section('head')
    {{-- @include('helper.headDatatable') --}}
    <script src="/facturacion/factura.js?v1"></script>
    <title>Facturación electrónica - Factura</title>
@endsection
@section('body')
    <section class="p-3">
        <div class="mb-4">
            <div class="m-auto" style="max-width: 600px;">
                <img src="/img/modulo/cuenta.png" alt="Imagen de una calculadora" width="120px" class="img-fluid d-block m-auto">
                <h4 class="text-center text-primary my-2">Factura electrónica</h4>
            </div>
        </div>
        <form method="GET">
            <div class="bg-white p-3 border mb-3">
                <div class="row" >
                    <div class="form-group col-6 col-lg-4 col-xl-3">
                        <label for="txtComprontes">Comprobantes</label>
                        <select name="documento" id="txtComprontes" class="form-control" required>
                            <option value="00" {{$documento == "00" ? 'selected' : ''}}>Todos</option>
                            <option value="01" {{$documento == "01" ? 'selected' : ''}}>Factura</option>
                            <option value="03" {{$documento == "30" ? 'selected' : ''}}>Boleta</option>
                            <option value="09" {{$documento == "09" ? 'selected' : ''}}>Guía Remitente</option>
                        </select>
                    </div>
                    <div class="form-group col-6 col-lg-3 col-xl-2">
                        <label for="txtFechaInicio">Fecha Inicio</label>
                        <input id="txtFechaInicio" name="desde" required type="date" value="{{$desde}}" class="form-control">
                    </div>
                    <div class="form-group col-6 col-lg-3 col-xl-2">
                        <label for="txtFechaFin">Fecha Fin</label>
                        <input id="txtFechaFin" name="hasta" required type="date" value="{{$hasta}}" class="form-control">
                    </div>
                    <div class="form-group col-6 col-lg-2 col-xl-5">
                        <button class="btn btn-sm btn-primary" id="btnBuscar" type="submit" title="Aplicar filtros" data-toggle="tooltip">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="bg-white p-3 border">
                <div class="form-group">
                    <div class="ml-auto" style="max-width: 400px;">
                        <label for="txtBuscar">Buscar</label>
                        <input id="txtBuscar" name="busqueda" placeholder="Número de documento o nombre" type="search" class="form-control" value="{{$busqueda}}">
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="tablaFactura" class="table table-sm table-bordered" style="font-size: 0.8rem;">
                        <thead class="text-center">
                            <tr>
                                <th>N°</th>
                                <th>Serie - Correlativo</th>
                                <th>Fecha Emisión</th>
                                <th>Tipo Documento</th>
                                <th>Tipo Documento</th>
                                <th>Numero Documento</th>
                                <th>Nombre Cliente</th>
                                <th>Monto o Bulto Total</th>
                                <th>Estado</th>
                                <th style="width: 200px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaFactura">
                            @if ($facturas->TotalItems === 0)
                                <tr>
                                    <td colspan="100%" class="text-center">No se encontrados comprobantes</td>
                                </tr>
                            @endif
                            @php
                                $inicio = ($facturas->PaginaActual - 1) * $facturas->ItemsPorPagina;
                                $fin = $inicio;
                            @endphp
                            @foreach ($facturas->ListaItems as $key => $factura)
                                <tr>
                                    @php
                                        $fin++;
                                    @endphp
                                    <td>{{$fin}}</td>
                                    <td>{{$factura->Serie . '-' . $factura->Correlativo}}</td>
                                    <td>{{date('d/m/Y h:i P',strtotime($factura->FechaEmision))}}</td>
                                    <td>{{$factura->TipoDocumentoDetalle}}</td>
                                    <td>{{$factura->ClienteTipoDocIdentidadDetalle}}</td>
                                    <td>{{$factura->ClienteNumeroDocIdentidad}}</td>
                                    <td>{{$factura->ClienteNombreRazonSocial}}</td>
                                    <td>{{$factura->MonedaSimbolo . ' ' . number_format($factura->Total,2)}}</td>
                                    <td>
                                        @if($factura->Baja)
                                            <span class="badge badge-danger">Anulado</span>
                                        @else
                                            <span class="badge badge-success">Aprobado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-success" target="_blank" href="{{$urlPdfFactura . '?key=' . $factura->ComprobanteKey}}">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>Ver</span>
                                        </a>
                                        @if(!$factura->Baja)
                                            <button type="button" class="btn btn-sm btn-danger" data-id="{{$factura->ID}}" data-serie="{{$factura->Serie}}" data-correlativo="{{$factura->Correlativo}}" data-tipo-documento="{{$factura->ClienteTipoDocIdentidadDetalle}}" data-numero-documento="{{$factura->ClienteNumeroDocIdentidad}}" data-fecha="{{date('d/m/Y',strtotime($factura->FechaEmision))}}" data-codigo-documento="{{$factura->TipoDocumentoCodigo}}" data-cliente="{{$factura->ClienteNombreRazonSocial}}">
                                                <i class="fas fa-trash-alt"></i>
                                                <span>Anular</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <nav class="d-flex justify-content-between">
                    <span>Mostrando {{$inicio + 1}} a {{$fin}} de {{$facturas->TotalItems}} registros</span>
                    <ul class="pagination justify-content-end">
                    <li class="page-item {{$facturas->PaginaActual === 1 ? 'disabled' : ''}}">
                        <a class="page-link" href="{{$facturas->PaginaActual > 1 ? 'facturar?documento=' . $documento . '&desde=' . $desde . '&hasta=' . $hasta . '&busqueda=' . $busqueda . '&pagina='. $facturas->PaginaActual - 1  : ''}}">Anterior</a>
                    </li>
                    @for ($i = 1; $i <= $facturas->TotalPaginas; $i++)
                        <li class="page-item {{$i === $facturas->PaginaActual ? 'active' : ''}}">
                            <a class="page-link" 
                            href="facturar?documento={{$documento}}&desde={{$desde}}&hasta={{$hasta}}&busqueda={{$busqueda}}&pagina={{$i}}">{{$i}}
                            </a>
                        </li>
                    @endfor
                    <li class="page-item {{$facturas->PaginaActual === $facturas->TotalPaginas ? 'disabled' : ''}}">
                        <a class="page-link" href="{{$facturas->PaginaActual < $facturas->TotalPaginas  ? 'facturar?documento=' . $documento . '&desde=' . $desde . '&hasta=' . $hasta . '&busqueda=' . $busqueda . '&pagina='. $facturas->PaginaActual + 1  : ''}}">Siguiente</a>
                    </li>
                    </ul>
                </nav>
                
            </div>
        </form>
    </section>
    @include('facturacion.modales.eliminar')
@endsection