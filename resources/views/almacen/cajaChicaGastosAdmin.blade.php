@extends('helper.index')
@section('head')
    @include('helper.headDatatable')
    <script src="/almacen/cajaChicaGastosAdmin.js"></script>
    <title>Gastos caja chica - Administrador</title>
@endsection
@section('body')
<style>
    .contenido-informacion{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
    .contenido-informacion .informacion{
        padding: 0.8rem;
    }
    .informacion > span{
        padding: 0.8rem;
    }
</style>
<section class="container">
    <div class="bg-white p-3">
        <h3 class="text-center text-primary my-2">Gastos de caja chica {{!empty($cajaChica) ? 'N° - ' . str_pad($cajaChica->id,5,'0',STR_PAD_LEFT) : '' }}</h3>
        <div class="form-group">
            <img src="/img/modulo/caja_abierta.png" alt="Imagen de cerrar caja" width="160px" class="img-fluid d-block m-auto">
        </div>
        <div class="form-group">
            <h4 class="text-center">{{$fechaLarga}}</h4>
        </div>
        <div class="form-group text-center">
            <div class="form-group d-flex justify-content-center" style="gap: 10px 30px;">
                <span><b>Fecha Inicio:</b> {{date('d/m/Y',strtotime($cajaChica->fecha_inicio))}}</span>
                <span><b>Fecha Fin:</b> {{date('d/m/Y',strtotime($cajaChica->fecha_fin))}}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" id="btnCerrarCaja" data-toggle="modal" data-target="#agragarGastos">
                    <i class="fas fa-door-closed"></i>
                    <span>Registrar gastos</span>
                </button>
                <a class="btn btn-danger" id="btnCerrarCaja" href="{{route('caja.chica.reporte.gastos',[$cajaChica->id])}}" target="_blank">
                    <i class="fas fa-file-pdf"></i>
                    <span>Reporte</span>
                </a>
            </div>
            <div class="form-group contenido-informacion">
                <div class="informacion">
                    <h6>Monto abonado</h6>
                    <strong>
                        <i class="fas fa-money-bill text-success"></i>
                        <span id="idGeneralmontoAbonado">{{$monedaTipo .' '. number_format($cajaChica->monto_abonado,2)}}</span>
                    </strong>
                </div>
                <div class="informacion">
                    <h6>Monto gastado</h6>
                    <strong>
                        <i class="fas fa-cash-register text-danger"></i>
                        <span id="idGeneralmontoGastado">{{$monedaTipo .' '. number_format($cajaChica->monto_gastado,2)}}</span>
                    </strong>
                </div>
                <div class="informacion">
                    <h6>Monto restante</h6>
                    <strong>
                        <i class="fas fa-coins text-info"></i>
                        <span id="idGeneralmontoRestante">{{$monedaTipo .' '. number_format($cajaChica->monto_abonado - $cajaChica->monto_gastado,2)}}</span>
                    </strong>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white p-3">
        <h3 class="text-primary my-2">Gastos acumulados</h3>
        <table id="tablaGastos" class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>N° Gasto</th>
                    <th>Fecha</th>
                    <th>OS</th>
                    <th>Proveedor</th>
                    <th>Área costo</th>
                    <th>Descripción</th>
                    <th>Monto Total</th>
                    <th>I.G.V</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</section>
@include('almacen.modales.agregarGastosCaja')
@endsection