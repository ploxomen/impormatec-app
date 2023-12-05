<table class="tabla-precio" style="width: 100%; font-size: 12px;" border="1">
    <thead>
        <tr>
            <th>N° OS</th>
            <th>F. EMISION</th>
            <th>CLIENTE</th>
            <th>MONEDA</th>
            <th>MONTO A PAGAR</th>
            <th>COMPROBANTES - MONTO PAGADO</th>
            <th>ESTADO</th>
            <th>N° CUOTA</th>
            <th>FECHA VENCIMIENTO</th>
            <th>MONTO A PAGAR</th>
            <th>FECHA PAGADA</th>
            <th>MONTO PAGADO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ordenesServicios as $ordenServicio)
            @php
                $moneda = $incluirMoneda ? ($ordenServicio->tipoMoneda === 'USD' ? '$ ' : 'S/ ') : '';
                $montoAPagar = $ordenServicio->importe - $ordenServicio->descuento + $ordenServicio->igv;
                $comprobantes = $ordenServicio->comprobantes()->whereIn('tipo_comprobante',['00','01','03'])->get();
                $comprobantesTexto = "";
                $numeroCuotas = $ordenServicio->pagoCuotas()->count();
                foreach ($comprobantes as $keyComprobante => $comprobante) {
                    $comprobantesTexto .= '<strong>'.$comprobante['numero_comprobante'].'</strong>' . '<br>' . $moneda.$comprobante['monto_total'];
                    if(($keyComprobante + 1) !== $comprobantes->count()){
                        $comprobantesTexto .= "<br>";
                    }
                }
                $comprobantesTexto = empty($comprobantesTexto) ? 'Pendiente' : $comprobantesTexto;
                $estado = $comprobantesTexto === 'Pendiente' ? 'Por pagar' : 'Cancelado';
                if($comprobantesTexto === 'Pendiente'){
                    $sumaCuotas = $ordenServicio->pagoCuotas()->where('estado',2)->sum('monto_pagado');
                    if($sumaCuotas >= $montoAPagar){
                        $estado = 'Por generar recibo';
                    }
                }
            @endphp
            <tr>
                <td rowspan="{{$numeroCuotas === 0 ? 1 : $numeroCuotas}}">{{$ordenServicio->nroOs}}</td>
                <td rowspan="{{$numeroCuotas === 0 ? 1 : $numeroCuotas}}">{{$ordenServicio->fechaOs}}</td>
                <td rowspan="{{$numeroCuotas === 0 ? 1 : $numeroCuotas}}">{{$ordenServicio->nombreCliente}}</td>
                <td rowspan="{{$numeroCuotas === 0 ? 1 : $numeroCuotas}}">{{$ordenServicio->tipoMoneda === 'PEN' ? 'SOLES' : 'DOLARES'}}</td>
                <td rowspan="{{$numeroCuotas === 0 ? 1 : $numeroCuotas}}">{{$moneda.$montoAPagar}}</td>
                <td rowspan="{{$numeroCuotas === 0 ? 1 : $numeroCuotas}}">{!!$comprobantesTexto!!}</td>
                <td rowspan="{{$numeroCuotas === 0 ? 1 : $numeroCuotas}}">{{$estado}}</td>
                @if ($numeroCuotas > 0)
                    @foreach ($ordenServicio->pagoCuotas as $key => $cuota)
                        @if($key > 0)
                            <tr>
                        @endif
                        <td>{{$cuota->nro_cuota}}</td>
                        <td>{{date('d/m/Y',strtotime($cuota->fecha_vencimiento))}}</td>
                        <td>{{$moneda.$cuota->monto_pagar}}</td>
                        <td>{{empty($cuota->fecha_pagada) ? 'Pendiente' : date('d/m/Y',strtotime($cuota->fecha_pagada))}}</td>
                        <td>{{empty($cuota->monto_pagado) ? 'Pendiente' : $moneda.$cuota->monto_pagado}}</td>
                        @if(($key + 1) !== $numeroCuotas)
                            </tr>
                        @endif
                    @endforeach
                @else
                    <td> - </td>
                    <td> - </td>
                    <td> - </td>
                    <td> - </td>
                    <td> - </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>