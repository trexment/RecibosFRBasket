<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Eliminar Equipo</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="alert alert-warning shadow-sm">
                <h4><i class="fas fa-exclamation-triangle"></i> Confirmar eliminación</h4>
                <p>¿Seguro que deseas eliminar el equipo
                    <strong><?= htmlspecialchars($equipo['nombre']) ?></strong>?</p>
                <p class="mb-0 text-muted">
                    Esta acción no se puede deshacer.
                </p>
            </div>

            <form method="POST">
                <input type="hidden" name="confirmar" value="si">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Eliminar definitivamente
                </button>
                <a href="<?= BASE_URL ?>equipos" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
