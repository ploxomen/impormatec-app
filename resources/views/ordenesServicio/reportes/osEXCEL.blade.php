<table>
    <tr></tr>
    <tr>
        <td></td>
        <td colspan="11">ORDENES DE SERVICIOS {{$fechaInicioReporte}} - {{$fechaFinReporte}}</td>
    </tr>
    <tr></tr>
</table>
@include('ordenesServicio.reportes.osCompartido', ['ordenesServicios' => $ordenesServicios,'incluirMoneda' => false])