<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$tituloPdf}}</title>
</head>
<body>
    @include('helper.headerFooterPdfVertical')
    <h1 style="font-size: 18px;text-align: center; margin-top: 0;">REPORTE DE GASTOS - N° {{str_pad($cajaChica->id,5,'0',STR_PAD_LEFT)}}</h1>
    <table style="width: 100%; font-size: 13px; margin-bottom: 10px;">
        <tr>
            <td><strong>Responsable:</strong> {{$cajaChica->reponsable->nombres . ' ' . $cajaChica->reponsable->apellidos}}</td>
            <td><strong>Monto abonado:</strong> {{$monedaTipo. ' ' .number_format($cajaChica->monto_abonado,2)}}</td>
            <td><strong>Monto gastado:</strong> {{$monedaTipo. ' ' .number_format($cajaChica->monto_gastado,2)}}</td>
            <td><strong>Monto restante:</strong> {{$monedaTipo. ' ' . number_format(($cajaChica->monto_abonado - $cajaChica->monto_gastado),2)}}</td>
        </tr>
    </table>
    <table class="tabla-precio tabla-moderna" style="width: 100%; font-size: 12px;">
        <thead>
            <tr>
                <th>N°</th>
                <th>OS</th>
                <th>FECHA</th>
                <th>TIPO COMPROBANTE</th>
                <th>N° COMPROBANTE</th>
                <th>PROVEEDOR</th>
                <th>RUC PROVEEDOR</th>
                <th>ÁREA COSTO</th>
                <th>DESCRIPCIÓN</th>
                <th>I.G.V</th>
                <th>MONTO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $igv = 0;
            @endphp
            @if($detalleGastos->isEmpty())
                <tr>
                    <td colspan="11" class="text-center">No se asignaron gastos para esta caja</td>
                </tr>
            @endif
            @foreach ($detalleGastos as $key => $gasto)
            @php
                 $total += $gasto->monto_total_cambio;
                 $igv += $gasto->igv;
            @endphp
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$gasto->id_os}}</td>
                    <td>{{date('d/m/Y',strtotime($gasto->fecha_gasto))}}</td>
                    <td>{{$gasto->tipo_comprobante}}</td>
                    <td>{{$gasto->nro_comprobante}}</td>
                    <td>{{$gasto->proveedor}}</td>
                    <td>{{$gasto->proveedor_ruc}}</td>
                    <td>{{$gasto->area_costo}}</td>
                    <td>{{$gasto->descripcion_producto}}</td>
                    <td>{{$monedaTipo. ' ' .number_format($gasto->igv,2)}}</td>
                    <td>{{$monedaTipo. ' ' .number_format($gasto->monto_total_cambio,2)}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" class="text-right" style="border: none !important;">SUBTOTAL</td>
                <td>{{$monedaTipo. ' ' . number_format((($total - $igv)),2)}}</td>
            </tr>
            <tr>
                <td colspan="10" class="text-right" style="border: none !important;">I.G.V</td>
                <td>{{$monedaTipo. ' ' . number_format($igv,2)}}</td>
            </tr>
            <tr>
                <td colspan="10" class="text-right" style="border: none !important;">TOTAL</td>
                <td>{{$monedaTipo. ' ' . number_format($total,2)}}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>