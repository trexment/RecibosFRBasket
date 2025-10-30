<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña | Recibos Arbitrales</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom.css">
</head>

<body class="login-page">
<div class="login-box">
    <div class="card shadow-lg">
        <div class="card-header text-center header-rioja">
            <i class="fas fa-key mr-2"></i> Recuperar contraseña
        </div>

        <div class="card-body">
            <p class="text-muted text-center mb-4">
                Introduce tu correo electrónico y recibirás un enlace para restablecer tu contraseña.
            </p>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-animado text-center">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['flash_error']); ?>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-animado text-center">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash_success']); ?>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>password/send">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-group">
                        <input type="email" name="email" id="email" class="form-control" placeholder="ejemplo@correo.com" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-3">
                    <i class="fas fa-paper-plane"></i> Enviar enlace de recuperación
                </button>

                <a href="<?= BASE_URL ?>login" class="btn btn-secondary btn-block mt-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
                </a>
            </form>
        </div>
    </div>
</div>

<footer class="text-center text-muted mt-4" style="color: rgba(255,255,255,0.8)">
    Recibos Arbitrales © <?= date('Y') ?>
</footer>

</body>
</html>
