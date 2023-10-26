@extends('helper.index')
@section('head')
    @if (!empty($cajaChica) && strtotime($cajaChica->fecha_inicio) <= strtotime(now()) && strtotime($cajaChica->fecha_fin) >= strtotime(now()))
        <script src="/almacen/cajaChicaGastos.js"></script>
    @endif
    <title>Gastos caja chica</title>
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
            @if(empty($cajaChica))
                <img src="/img/modulo/caja_cerrada.png" alt="Imagen de abrir caja" width="160px" class="img-fluid d-block m-auto">
            @else
                <img src="/img/modulo/caja_abierta.png" alt="Imagen de cerrar caja" width="160px" class="img-fluid d-block m-auto">
            @endif
        </div>
        <div class="form-group">
            <h4 class="text-center">{{$fechaLarga}}</h4>
        </div>
        <div class="form-group text-center">
            @if(empty($cajaChica))
                <h5 class="text-center text-danger">Actualmente no se encuentra habilitada ninguna caja chica para usted</h5>
            @else
                @if (strtotime($cajaChica->fecha_inicio) <= strtotime(now()) && strtotime($cajaChica->fecha_fin) >= strtotime(now()))
                <div class="form-group">
                    <button class="btn btn-primary" id="btnCerrarCaja" data-toggle="modal" data-target="#agragarGastos">
                        <i class="fas fa-door-closed"></i>
                        <span>Registrar gastos</span>
                    </button>
                </div>
                <div class="form-group contenido-informacion">
                    <div class="informacion">
                        <h6>Monto abonado</h6>
                        <strong>
                            <i class="fas fa-money-bill text-success"></i>
                            <span>{{$monedaTipo .' '. number_format($cajaChica->monto_abonado,2)}}</span>
                        </strong>
                    </div>
                    <div class="informacion">
                        <h6>Monto gastado</h6>
                        <strong>
                            <i class="fas fa-cash-register text-danger"></i>
                            <span>{{$monedaTipo .' '. number_format($cajaChica->monto_gastado,2)}}</span>
                        </strong>
                    </div>
                    <div class="informacion">
                        <h6>Monto restante</h6>
                        <strong>
                            <i class="fas fa-coins text-info"></i>
                            <span>{{$monedaTipo .' '. number_format($cajaChica->monto_abonado - $cajaChica->monto_gastado,2)}}</span>
                        </strong>
                    </div>
                </div>
                @else
                    <h5 class="text-center text-danger">No se puede registrar los gastos de la caja chica, fechas limite desde <strong>{{date('d/m/Y',strtotime($cajaChica->fecha_inicio))}}</strong> hasta <strong>{{date('d/m/Y',strtotime($cajaChica->fecha_fin))}}</strong></h5>
                @endif
            @endif
        </div>
    </div>
</section>
    @if(!empty($cajaChica))
        @include('almacen.modales.agregarGastosCaja')
    @endif
@endsection