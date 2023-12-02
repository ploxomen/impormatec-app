<table>
    <tr></tr>
    <tr>
        <td></td>
        <td colspan="8">SEGUIMIENTO DE COTIZACIONES POR APROBAR {{$fechaInicioReporte}} - {{$fechaFinReporte}}</td>
    </tr>
    <tr></tr>
</table>
@include('cotizacion.reportes.seguimientoCompartido', ['cotizaciones' => $cotizaciones,'incluirMoneda' => false])