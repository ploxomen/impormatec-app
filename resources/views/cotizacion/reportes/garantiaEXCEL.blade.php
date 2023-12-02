<table>
    <tr></tr>
    <tr>
        <td colspan="8">SEGUIMIENTO DE GARANTIAS DE PRODUCTOS Y/O SERVICIOS {{$fechaInicioReporte}} - {{$fechaFinReporte}}</td>
    </tr>
    <tr></tr>
</table>
@include('cotizacion.reportes.garantiaCompartido', ['garantias' => $garantias])