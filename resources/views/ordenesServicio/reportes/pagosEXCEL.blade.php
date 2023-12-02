<table>
    <tr></tr>
    <tr>
        <td></td>
        <td colspan="11">REPORTE DE PAGOS {{$fechaInicioReporte}} - {{$fechaFinReporte}}</td>
    </tr>
    <tr></tr>
</table>
@include('ordenesServicio.reportes.pagosCompartido', ['ordenesServicios' => $ordenesServicios,'incluirMoneda' => false])