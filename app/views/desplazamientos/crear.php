<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Nuevo precio de desplazamiento</h1>
            <a href="<?= BASE_URL ?>desplazamientos" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
    </section>

    <section class="content">
        <div class="card card-outline card-maroon">
            <div class="card-body">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Temporada</label>
                            <select name="temporada_id" class="form-control" required>
                                <?php foreach ($temporadas as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= $t['activa'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>€ / km</label>
                            <input type="number" step="0.001" name="precio_km" class="form-control" value="0.260" required>
                        </div>
                        <div class="form-group col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activo" id="activo" checked>
                                <label class="form-check-label" for="activo">Activo</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
