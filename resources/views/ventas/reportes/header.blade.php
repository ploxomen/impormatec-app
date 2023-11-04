<header class="text-center">
    <h3>{{$configuracion[0]->valor}}</h3>
    <div class="mb-1"><b>RUC:</b> {{$configuracion[1]->valor}}</div>
    <div class="mb-1"><b>Teléfono:</b> {{$configuracion[3]->valor}}</div>
    <div class="mb-1"><b>Celular:</b> {{$configuracion[2]->valor}}</div>
    <div class="mb-1"><b>Dirección:</b> {{$configuracion[6]->valor}}</div>
    <div class="mb-1"><b>Fecha Impresión:</b> {{date("d/m/Y h:i a")}}</div>
    <div class="separador"></div>
</header>