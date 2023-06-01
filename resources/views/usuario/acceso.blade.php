<!DOCTYPE html>
<html lang="es">
<head>
    @include('helper.meta')
    <link rel="stylesheet" href="/usuario/login.css">
    <script src="/usuario/login.js"></script>
    <title>Acceso</title>
</head>
<body>
    <div class="container login">
        <div class="row justify-content-center">
            <div class="col-10 col-md-5 col-lg-5 formulario">
                <div class="mb-3 logo">
                    <img src="/img/logo.png" alt="" class="img-fluid">
                </div>

                <div class="mb-4 text-center">
                    <h2 class="titulo">Iniciar Sesión</h2>
                    <p>Ingresa a la Plataforma Virtual</p>
                </div>
                <form id="frmLogin">
                    <div class="mb-3">
                      <label for="txtCorreo" class="form-label">Usuario</label>
                      <input type="email" class="form-control form-control-lg" name="correo" id="txtCorreo">                      
                    </div>

                    <div class="mb-3">
                      <label for="txtContrasena" class="form-label">Contraseña</label>
                      <input type="password" class="form-control form-control-lg" name="password" id="txtContrasena">
                    </div>
                    {{-- <div class="mb-3">
                      <a href="#" class="text-center d-block enlace-olvide">¿Olvidaste tu contraseña?</a>
                    </div> --}}
                    <button type="submit" class="btn w-100 btn-acceder btn-lg">Acceder</button>
                  </form>
            </div>
        </div>
    </div>
</body>
</html>