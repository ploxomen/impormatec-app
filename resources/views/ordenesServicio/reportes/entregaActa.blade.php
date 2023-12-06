<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
            integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
            crossorigin="anonymous">
    <title>{{$tituloPdf}}</title>
</head>
<body>
    <table class="table">
        <thead>
            <tr>
                <td scope="col" class="alineado">
                    <img src="img/logo.png" alt width="50%">
                    <p style="font-size: 18px; font-weight: 600; margin-top: 1rem; margin-bottom: 0;">{{$configuracion->where('descripcion','razon_social_largo')->first()->valor}}</p>
                    <p style="font-weight: 400; margin: 0;">{{$configuracion->where('descripcion','direccion')->first()->valor}}</p>
                </td>
                <td scope="col">
                    <div class="numeracion"
                        style="text-align: center; border: 2px solid #000; padding: .5rem 0;">
                        <h3><b>R.U.C. {{$configuracion->where('descripcion','ruc')->first()->valor}}</b></h3>
                        <h4>ACTA DE CONFORMIDAD</h4>
                        <h5 style="color: red;">N° {{str_pad($entregaActa->id,6,'0',STR_PAD_LEFT)}}</h5>
                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td scope="row" colspan="2">
                    <div style="padding: 20px 0;">
                        <p>Yo <b>{{$entregaActa->reponsableFirmante->nombres . ' ' . $entregaActa->reponsableFirmante->apellidos}}</b> con D.N.I <b>{{$entregaActa->reponsableFirmante->nroDocumento}}</b> y en representación de la compañia {{$configuracion->where('descripcion','razon_social_largo')->first()->valor}}, procedo hacer la entrega del servicio con la siguiente descripión:</p>
                        <p>
                            @foreach ($entregaActa->ordenServicio->servicios as $key => $servicio)
                                No. Coti. {{str_pad($servicio->cotizacionServicio->id_cotizacion,5,'0',STR_PAD_LEFT) . ' - ' . $servicio->cotizacionServicio->servicios->servicio}}
                                <br>
                            @endforeach
                        </p>

                        <en> Pertenecientes a la
                            Empresa/Condominio: <b>{{$entregaActa->ordenServicio->cliente->nombreCliente}}</b>, en prensencia del Sr.(Sra.) <b>{{$entregaActa->nombre_representante}}</b> identificado con D.N.I.
                            <b>{{$entregaActa->dni_representante}}</b>, quien da fe de haber recibido el servicio y/o equipos en buen estado de operación.
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td scope="row" colspan="2">
                    <p style="text-align: center;">A continuación, se procede a firmar ambas partes.</p>
                </td>
            </tr>
            
        </tbody>
    </table>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; padding: 20px;">
                <div style="text-align: center;">
                    <img src="{{storage_path('app/firmaEntregaActas/' . $entregaActa->firma_representante_cortado)}}" alt="Firma de la conformidad" width="170px" height="120px">
                    <hr style="border: .5px solid #cbcbcb; margin-bottom: 0;  margin-top:10px; ">
                    <p style="margin: 0;">Firma de Conformidad</p>
                    <p style="margin: 0;"><b>{{$entregaActa->nombre_representante}}</b></p>
                </div>
            </td>
            <td style="width: 50%; padding: 20px;">
                <div style="text-align: center;">
                    <img src="{{$entregaActa->reponsableFirmante->firma}}" alt="Firma del usuario" width="170px" height="120px">
                    <hr style="border: .5px solid #cbcbcb; margin-bottom: 0; margin-top:10px; ">
                    <p style="margin: 0;">{{$configuracion->where('descripcion','razon_social')->first()->valor}}</p>
                    <p style="margin: 0;"><b>{{$entregaActa->reponsableFirmante->nombres . ' ' . $entregaActa->reponsableFirmante->apellidos}}</b></p>
                </div>
            </td>
        </tr>
        <tr></tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <p><b>Fecha:</b> {{$diaFecha}}</p>
                </td>
            </tr>
    </table>
</body>
</html>