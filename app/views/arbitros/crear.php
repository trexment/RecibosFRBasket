<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
<?php require_once __DIR__ . '/../partials/alerts.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="card shadow-sm border-0">
            <div class="card-header header-rioja">
                <h3 class="card-title mb-0">
                    <i class="fas fa-user-plus"></i> Crear Árbitro
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required
                                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email (opcional)</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                   value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="activo" id="activo" class="form-check-input"
                                        <?= (isset($_POST['activo']) ? 'checked' : 'checked') ?>>
                                <label class="form-check-label fw-bold" for="activo">Activo en federación</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <a href="<?= BASE_URL ?>arbitros" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
