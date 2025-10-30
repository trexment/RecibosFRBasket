<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper" style="min-height: 600px;">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Editar Temporada</h1>
            <a href="<?= BASE_URL ?>temporadas" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Volver a Temporadas
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form action="<?= BASE_URL ?>temporadas/editar/<?= $temporada['id'] ?>" method="post">
                <div class="form-group">
                    <label for="nombre">Nombre de la Temporada</label>
                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        class="form-control"
                        value="<?= htmlspecialchars($temporada['nombre']) ?>"
                        required
                    />
                </div>

                <div class="form-check mb-3">
                    <input
                        type="checkbox"
                        name="activa"
                        id="activa"
                        class="form-check-input"
                        <?= $temporada['activa'] ? 'checked' : '' ?>
                    />
                    <label for="activa" class="form-check-label">¿Temporada activa?</label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
