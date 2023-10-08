<style>
    table{
        border-collapse: collapse;
    }
    .text-right{
        text-align: right;
    }
    .text-center{
        text-align: center;
    }
    .text-justify{
        text-align: justify;
    }
    .saltopagina{
        page-break-after:always;
    }
    @page{
        font-family: 'Courier New', Courier, monospace;
        margin-top: 130px;
    }
    .nota{
        font-size: 16px;
    }
    header{
        position: fixed;
        top: -130px;
        left: 0;
        font-size: 13px;
        border-bottom: 2px solid #1F2B53;
        padding-bottom: 5px;
        font-family: Arial, Helvetica, sans-serif;
        color: #1F2B53;
    }
    footer{
        position: fixed;
        bottom: -30px;
        left: 0;
        font-size: 13px;
        border-top: 2px solid #1F2B53;
        padding-top: 15px;
        font-family: Arial, Helvetica, sans-serif;
        color: #1F2B53;
    }
    .tabla-precio{
        font-size: 13px;
        margin-bottom: 20px;
    }
    .tabla-precio td,
    .tabla-precio th{
        padding: 8px 5px;
    }
    .tabla-precio th{
        background: rgb(223, 223, 223);
    }
    footer a{
        color: #ffff;
        padding: 3px;
    }
</style>
<header>
    <table>
        <tr>
            <td>
                <p class="text-center">
                    <small>{{$configuracion->where('descripcion','direccion')->first()->valor}}</small>
                    <br>
                    <small>{{$configuracion->where('descripcion','telefono')->first()->valor}}</small>
                    <br>
                    <small>{{Auth::user()->celular}}</small>
                    <br>
                    <small>{{Auth::user()->correo}}</small>
                </p>
            </td>
            <td style="width: 150px;">

            </td>
            <td>
                <img src="{{public_path("img/logo.png")}}" alt="logo de impormatec" width="300px">
            </td>
        </tr>
    </table>
</header>
<footer style="width: 700px">
    <table style="width: 100%;">
        <tr>
            <td>
                <img src="{{public_path("img/logo.png")}}" alt="logo de impormatec" width="100px">
            </td>
            <td style="width: 340px;">
            </td>
            <td class="text-right">
                @php
                    $facebook = $configuracion->where('descripcion','red_social_facebook')->first();
                    $instagram = $configuracion->where('descripcion','red_social_instagram')->first();
                    $tiktok = $configuracion->where('descripcion','red_social_tiktok')->first();
                    $twiter = $configuracion->where('descripcion','red_social_twitter')->first();
                @endphp
                @empty(!$facebook->valor)
                    <a href="{{$facebook->valor}}">
                        <img src="{{public_path('img/logos/facebook.png')}}" width="20px" alt="logo de Facebook">
                    </a>
                @endempty
                @empty(!$instagram->valor)
                    <a href="{{$instagram->valor}}">
                        <img src="{{public_path('img/logos/instagram.png')}}" width="20px" alt="logo de Instagram">
                    </a>
                @endempty
                @empty(!$tiktok->valor)
                    <a href="{{$tiktok->valor}}">
                        <img src="{{public_path('img/logos/tik-tok.png')}}" width="20px" alt="logo de TikTok">
                    </a>
                @endempty
                @empty(!$twiter->valor)
                    <a href="{{$twiter->valor}}">
                        <img src="{{public_path('img/logos/twiter.png')}}" width="20px" alt="logo de Twiter">
                    </a>
                @endempty
            </td>
        </tr>
    </table>
</footer>