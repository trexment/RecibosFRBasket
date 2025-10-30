<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';

?>

<?php
$usuario = $usuario_data ?? [];
?>


<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Mi Perfil</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <div class="card card-primary shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre"
                                   value="<?= htmlspecialchars($usuario['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="apellidos">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" id="apellidos"
                                   value="<?= htmlspecialchars($usuario['apellidos'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" class="form-control" name="email" id="email"
                                   value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="domicilio">Domicilio</label>
                            <input type="text" class="form-control" name="domicilio" id="domicilio"
                                   value="<?= htmlspecialchars($usuario['domicilio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="form-group">
                            <label for="codigo_postal">Código Postal</label>
                            <input type="text" class="form-control" name="codigo_postal" id="codigo_postal"
                                   value="<?= htmlspecialchars($usuario['codigo_postal'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="form-group">
                            <label for="cuenta_bancaria">Número de Cuenta</label>
                            <input type="text" class="form-control" name="cuenta_bancaria" id="cuenta_bancaria"
                                   value="<?= htmlspecialchars($usuario['cuenta_bancaria'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="form-group">
                            <label for="dni">DNI</label>
                            <input type="text" class="form-control" name="dni" id="dni"
                                   value="<?= htmlspecialchars($usuario['dni'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                        <a href="<?= BASE_URL ?>dashboard" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

