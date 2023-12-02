<table>
    <tr></tr>
    <tr>
        <td></td>
        <td colspan="8">COTIZACIONES {{$fechaInicioReporte}} - {{$fechaFinReporte}}</td>
    </tr>
    <tr></tr>
</table>
@include('cotizacion.reportes.cotizacionCompartido', ['cotizaciones' => $cotizaciones,'incluirMoneda' => false])