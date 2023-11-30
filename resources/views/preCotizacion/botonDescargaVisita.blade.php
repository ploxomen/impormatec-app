@empty(!$archivoVisitaUrl && !$archivoVisitaNombre)
    <a href="{{route('descargarArchivo',[$archivoVisitaUrl->valor])}}" download="{{str_replace(".pdf","",$archivoVisitaNombre->valor)}}" class="btn btn-sm btn-primary">
        <i class="fas fa-download"></i>
        <span>{{$archivoVisitaNombre->valor}}</span>
    </a>
@endempty