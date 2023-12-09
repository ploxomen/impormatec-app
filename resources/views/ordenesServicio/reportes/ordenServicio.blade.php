<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orden de servicio</title>
</head>
<body>
    @include('helper.headerFooterPdfVertical')
    <h1 class="text-center">ORDEN DE SERVICIO</h1>
    <p>
        <b>Fecha:</b>
        <span>{{$nombreDia}}</span>
        <br>
        <b>No. Orden de Servicio:</b>
        <span>{{$codigoOrdenServicio}}</span>
    </p>
    <table border="1" class="tabla-precio" style="width: 100%;">
        <thead>
            <tr>
                <th colspan="2">CLIENTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 25%;">
                    <b>Razón Social: </b>
                </td>
                <td>
                    <span>{{$cliente->nombreCliente}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <b>RUC/DNI: </b>
                </td>
                <td>
                    <span>{{$cliente->usuario->nroDocumento}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Dirección: </b>
                </td>
                <td>
                    <span>{{$cliente->usuario->direccion}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Teléfono: </b>
                </td>
                <td>
                    <span>{{$cliente->usuario->telefono}}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Correo electrónico: </b>
                </td>
                <td>
                    <span>{{$cliente->usuario->correo}}</span>
                </td>
            </tr>
        </tbody>
    </table>
    <h2>DESCRIPCION DE SERVICIOS Y/O PRODUCTOS</h2>
    <table border="1" class="tabla-precio" style="width: 100%;">
        <thead>
            <tr>
                <th>ITEM</th>
                <th>DESCRIPCION</th>
                <th>CANT.</th>
                <th>P. VENTA</th>
                <th>DESC.</th>
                <th>V. TOTAL</th>
                <th>P. COMPRA</th>
                <th>C. TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $calculosGenerales = [
                    'totalImporte' => 0,
                    'totalDescuento' => 0,
                    'totalVenta' => 0,
                    'totalCosto' => 0,
                    'totalIgv' => 0,
                    'utilidad' => 0,
                    'totalGastoAdicional' => 0,
                    'totalGastoCaja' => 0,
                ]
            @endphp
            @foreach ($ordenServicioDetalle as $keyDetalle => $detalle)
                @php
                    $precioCompra = round($detalle->costo_total / $detalle->cantidad,2);
                    $calculosGenerales['totalImporte'] += $detalle->importe;
                    $calculosGenerales['totalDescuento'] += $detalle->descuento;
                    $calculosGenerales['totalIgv'] += $detalle->igv;
                    $calculosGenerales['totalCosto'] += $detalle->costo_total;
                @endphp
                <tr>
                    <td>{{$keyDetalle + 1}}</td>
                    <td>{{$detalle->servicio}}</td>
                    <td>{{$detalle->cantidad}}</td>
                    <td>{{$moneda . ' ' .number_format($detalle->precio,2)}}</td>
                    <td>{{'-'.$moneda . ' ' .number_format($detalle->descuento,2)}}</td>
                    <td>{{$moneda . ' ' .number_format($detalle->total,2)}}</td>
                    <td>{{$moneda . ' ' .number_format($precioCompra,2)}}</td>
                    <td>{{$moneda . ' ' .number_format($detalle->costo_total,2)}}</td>
                </tr>
            @endforeach
        </tbody>
        @php
            $calculosGenerales['totalVenta'] = $calculosGenerales['totalImporte'] - $calculosGenerales['totalDescuento'] + $calculosGenerales['totalIgv'];
        @endphp
        <tfoot>
            <tr>
                <th colspan="7" class="text-right">VENTA SUBTOTAL</th>
                <th>{{$moneda . ' ' . number_format($calculosGenerales['totalImporte'],2) }}</th>
            </tr>
            <tr>
                <th colspan="7" class="text-right">DESC.</th>
                <th>{{'-'.$moneda . ' ' . number_format($calculosGenerales['totalDescuento'],2) }}</th>
            </tr>
            <tr>
                <th colspan="7" class="text-right">I.G.V 18%</th>
                <th>{{$moneda . ' ' . number_format($calculosGenerales['totalIgv'],2) }}</th>
            </tr>
            <tr>
                <th colspan="7" class="text-right">VENTA TOTAL</th>
                <th>{{$moneda . ' ' . number_format($calculosGenerales['totalVenta'],2) }}</th>
            </tr>
            <tr>
                <th colspan="7" class="text-right">COSTO TOTAL</th>
                <th>{{$moneda . ' ' . number_format($calculosGenerales['totalCosto'],2)}}</th>
            </tr>
        </tfoot>
    </table>
    <div style="page-break-inside:avoid;">
        <h2>GASTOS ADICIONALES</h2>
        <table class="tabla-precio" border="1" style="width: 100%;">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>DESCRIPCION</th>
                    <th>CANTIDAD</th>
                    <th>PRECIO</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @if (!$ordenServicio->costosAdicionales->count())
                    <tr>
                        <td colspan="5" class="text-center">No se encontraron gastos adicionales</td>
                    </tr>
                @endif
                @foreach ($ordenServicio->costosAdicionales as $keyAdicional => $costoAdicional)
                @php
                    $calculosGenerales['totalGastoAdicional'] += $costoAdicional->total;
                @endphp
                <tr>
                    <td>{{$keyAdicional + 1}}</td>
                    <td>{{$costoAdicional->descripcion}}</td>
                    <td>{{$costoAdicional->cantidad}}</td>
                    <td>{{$moneda . ' ' .number_format($costoAdicional->precio,2)}}</td>
                    <td>{{$moneda . ' ' .number_format($costoAdicional->total,2)}}</td>
                </tr>
                @endforeach
            </tbody>
            <tbody>
                <tr>
                    <td colspan="4" class="text-right">TOTAL</td>
                    <td>{{$moneda . ' ' .number_format($calculosGenerales['totalGastoAdicional'],2)}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="page-break-inside:avoid;">
        <h2>GASTOS CAJA CHICA</h2>
        <table class="tabla-precio" border="1" style="width: 100%;">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>N° GASTO</th>
                    <th>N° CAJA</th>
                    <th>F. GASTO</th>
                    <th>DESCRIPCION</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @if (!$ordenServicio->cajaChicaCostos->count())
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron gastos de caja chica</td>
                    </tr>
                @endif
                @foreach ($ordenServicio->cajaChicaCostos as $keyCajaChica => $costoCajaChica)
                @php
                    $costoTotal = $costoCajaChica->monto_total;
                    if($ordenServicio->tipoMoneda !== $costoCajaChica->tipo_moneda){
                        $costoTotal = $ordenServicio->tipoMoneda === 'PEN' ? round($costoCajaChica->monto_total * $costoCajaChica->tipo_cambio,2) : round($costoCajaChica->monto_total/$costoCajaChica->tipo_cambio,2);
                    }
                    $calculosGenerales['totalGastoCaja'] += $costoTotal;
                @endphp
                <tr>
                    <td>{{$keyCajaChica + 1}}</td>
                    <td>{{str_pad($costoCajaChica->id,5,'0',STR_PAD_LEFT)}}</td>
                    <td>{{str_pad($costoCajaChica->cajaChica->id,5,'0',STR_PAD_LEFT)}}</td>
                    <td>{{date('d/m/Y',strtotime($costoCajaChica->fecha_gasto))}}</td>
                    <td>{{$costoCajaChica->descripcion_producto}}</td>
                    <td>{{$moneda . ' ' .number_format($costoTotal,2)}}</td>
                </tr>
                @endforeach
            </tbody>
            <tbody>
                <tr>
                    <td colspan="5" class="text-right">TOTAL</td>
                    <td>{{$moneda . ' ' .number_format($calculosGenerales['totalGastoCaja'],2)}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="page-break-inside:avoid;">
        <h2>INFORMACION TOTAL</h2>
        <table class="tabla-precio" border="1" style="width: 100%;">
            <thead>
                <tr>
                    <th>VENTA</th>
                    <th>GASTO ADICIONAL</th>
                    <th>GASTO CAJA CHICA</th>
                    <th>COSTO</th>
                    <th>UTILIDAD</th>
                </tr>
            </thead>
            @php
                $calculosGenerales['utilidad'] = round($calculosGenerales['totalVenta'] - $calculosGenerales['totalGastoAdicional'] - $calculosGenerales['totalGastoCaja'] - $calculosGenerales['totalCosto'],2);
            @endphp
            <tbody>
                <tr>
                    <td class="text-center">{{$moneda . ' ' . number_format($calculosGenerales['totalVenta'],2)}}</td>
                    <td class="text-center">{{$moneda . ' ' . number_format($calculosGenerales['totalGastoAdicional'],2)}}</td>
                    <td class="text-center">{{$moneda . ' ' . number_format($calculosGenerales['totalGastoCaja'],2)}}</td>
                    <td class="text-center">{{$moneda . ' ' . number_format($calculosGenerales['totalCosto'],2)}}</td>
                    <td class="text-center">{{$moneda . ' ' . number_format($calculosGenerales['utilidad'],2)}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <strong>OBSERVACIONES:</strong>
    <div>
        {!! str_replace(['../../imagenesEditorOs/'],'imagenesEditorOs/',$ordenServicio->observaciones)  !!}
    </div>
    @php
        $firma = !empty($ordenServicio->firmante) ? $ordenServicio->firmante->firma : null;
    @endphp
    @if (!empty($firma))
        <img src="{{$firma}}" style="position: absolute; right: 5px; bottom: 20px;" alt="Firma del usuario" width="150px" height="120px">
    @endif
</body>
</html>