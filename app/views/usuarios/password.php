<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Cambiar Contraseña</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php elseif (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Contraseña actual</label>
                    <input type="password" name="actual" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nueva contraseña</label>
                    <input type="password" name="nueva" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Repetir nueva contraseña</label>
                    <input type="password" name="repite" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="<?= BASE_URL ?>dashboard" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
