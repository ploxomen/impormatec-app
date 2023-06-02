<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="stylesheet" href="/library/alertify/alertify.min.css">
    <script src="/library/alertify/alertify.min.js"></script>
    <script src="/general.js"></script>
    <link rel="stylesheet" href="/iconos/fontawesome/css/all.css">
    <link rel="stylesheet" href="/general.css">
    <link rel="stylesheet" href="/library/bootstrap/bootstrap.min.css">
    <script src="/library/jquery/external/jquery/jquery.js"></script>
    <script src="/library/bootstrap/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/usuario/cambioContrasena.css">

    <script src="/usuario/restaurar.js"></script>
    <title>Restaurar Contraseña</title>
</head>
<body>
    <div class="pagina-centro p-4">
        <form id="frmLogin" class="row border p-2 rounded form-restore">
            <div class="form-group col-12">
                <img src="/img/modulo/password.png" alt="Candado abierto" class="d-block img-fluid m-auto" width="150px">
            </div>
            <div class="form-group col-12">
                <small class="text-muted">Su cuenta a sido restaurada por un administrador o es la primera vez que ingresa al sistema, por favor establesca una contraseña para acceder al sistema</small>
            </div>
            <div class="form-group col-12 form-required ">
                <label for="">Contraseña</label>
                <input type="password" name="password" required class="form-control" minlength="8" required>
            </div>
            <div class="form-group col-12 form-required">
                <label for="">Repetir contraseña</label>
                <input type="password" name="password2" required class="form-control" minlength="8" required>
            </div>
            <div class="form-group col-12 text-center">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-paper-plane"></i>
                    <span>Ingresar</span>
                </button>
                <a href="{{route('salirRestaurar')}}" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Salir</span>
                </a>
            </div>
        </form>
    </div>
</body>
</html>