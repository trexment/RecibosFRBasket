<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Login | Recibos Arbitrales</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Estilo personalizado -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom.css">
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>img/silbato.ico">

</head>

<body class="hold-transition login-page">

<div class="login-box" style="max-width:850px;">
    <div class="login-logo">
        <a href="#" style="color:#fff;text-shadow:1px 1px 3px rgba(0,0,0,0.3);">
            <b>Recibos</b> Arbitrales
        </a>
    </div>

    <div class="card shadow-lg">
        <div class="card-header header-rioja text-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-sign-in-alt"></i> Iniciar sesión
            </h3>
        </div>

        <div class="card-body">
            <p class="text-center text-muted mb-4">Introduce tus credenciales para acceder</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-animado text-center">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>login/autenticar" method="post">
                <div class="form-group mb-3">
                    <label>Email</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required autofocus>
                        <div class="input-group-append">
                            <span class="input-group-text bg-light"><i class="fas fa-envelope text-secondary"></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label>Contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" id="password" placeholder="********" required>
                        <div class="input-group-append">
                            <span class="input-group-text bg-light" id="togglePassword" style="cursor:pointer;">
                                <i class="fas fa-eye text-secondary"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-lock-open"></i> Entrar
                    </button>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>password/forgot" style="color:#7b1113; font-weight:500;">
                        ¿Has olvidado tu contraseña?
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const pass = document.getElementById('password');
        const icon = this.querySelector('i');
        if (pass.type === 'password') {
            pass.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pass.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>

</body>
</html>
