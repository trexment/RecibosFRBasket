<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Recibos Arbitrales</title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Estilo Rioja -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom.css">
</head>

<body class="hold-transition login-page">

<div class="login-box" style="max-width:850px;">
    <div class="card shadow-lg border-0">
        <div class="card-header header-rioja">
            <h3 class="card-title"><i class="fas fa-user-plus"></i> Registro de usuario</h3>
        </div>

        <div class="card-body bg-light rounded-bottom">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-animado">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php $codigoUrl = $_GET['codigo'] ?? ''; ?>

            <form method="POST" action="<?= BASE_URL ?>register/store">
                <input type="hidden" name="t" value="<?= htmlspecialchars(REGISTRATION_TOKEN) ?>">

                <div class="form-group">
                    <label for="invite_code">Código de invitación</label>
                    <input type="text" name="invite_code" id="invite_code" class="form-control"
                           placeholder="Introduce tu código de invitación"
                           value="<?= htmlspecialchars($codigoUrl) ?>"
                            <?= $codigoUrl ? 'readonly' : 'required' ?>>
                    <?php if ($codigoUrl): ?>
                        <small class="text-muted">El código fue detectado automáticamente desde el enlace recibido por correo.</small>
                    <?php endif; ?>
                </div>

                <hr>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Nombre</label>
                        <input name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Apellidos</label>
                        <input name="apellidos" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input name="email" type="email" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Contraseña</label>
                        <input id="password" name="password" type="password" class="form-control" required minlength="8">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Repetir contraseña</label>
                        <input id="password2" name="password2" type="password" class="form-control" required minlength="8">
                        <small id="passHelp" class="text-muted"></small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Domicilio</label>
                    <input name="domicilio" class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Código postal</label>
                        <input name="codigo_postal" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Cuenta bancaria (IBAN)</label>
                        <input name="cuenta_bancaria" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>DNI</label>
                        <input name="dni" class="form-control">
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button class="btn btn-success btn-rioja">
                        <i class="fas fa-user-check"></i> Registrar
                    </button>
                    <a href="<?= BASE_URL ?>login" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pass1 = document.getElementById('password');
        const pass2 = document.getElementById('password2');
        const help = document.getElementById('passHelp');

        if (!pass1 || !pass2) return;

        pass2.addEventListener('input', () => {
            if (pass2.value.length === 0) {
                help.textContent = '';
                return;
            }
            if (pass2.value !== pass1.value) {
                help.textContent = 'Las contraseñas no coinciden.';
                help.style.color = '#a12020';
            } else {
                help.textContent = 'Las contraseñas coinciden.';
                help.style.color = '#326c34';
            }
        });
    });
</script>

</body>
</html>
