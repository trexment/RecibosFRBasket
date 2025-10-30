<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fas fa-euro-sign"></i> Editar Tarifa</h1>
    </section>

    <section class="content">
        <div class="card card-primary">
            <div class="card-header bg-maroon">
                <h3 class="card-title">Modificar Tarifa</h3>
            </div>

            <form method="POST" action="">
                <div class="card-body">

                    <!-- Temporada -->
                    <div class="form-group">
                        <label for="temporada_id">Temporada</label>
                        <select name="temporada_id" id="temporada_id" class="form-control" required>
                            <?php foreach ($temporadas as $t): ?>
                                <option value="<?= $t['id']; ?>" <?= $tarifa['temporada_id'] == $t['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($t['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Categoría -->
                    <div class="form-group">
                        <label for="categoria_id">Categoría</label>
                        <select name="categoria_id" id="categoria_id" class="form-control" required>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id']; ?>" <?= $tarifa['categoria_id'] == $c['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($c['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Rol -->
                    <div class="form-group">
                        <label for="rol">Rol</label>
                        <select name="rol" id="rol" class="form-control" required>
                            <option value="arbitro" <?= $tarifa['rol'] === 'arbitro' ? 'selected' : ''; ?>>Árbitro</option>
                            <option value="arbitro_solo" <?= $tarifa['rol'] === 'arbitro_solo' ? 'selected' : ''; ?>>Árbitro (solo)</option>
                            <option value="oficial" <?= $tarifa['rol'] === 'oficial' ? 'selected' : ''; ?>>Oficial</option>
                            <option value="oficial_solo" <?= $tarifa['rol'] === 'oficial_solo' ? 'selected' : ''; ?>>Oficial (solo)</option>
                        </select>
                    </div>

                    <!-- Importe -->
                    <div class="form-group">
                        <label for="importe">Importe (€)</label>
                        <input type="number" step="0.01" min="0" name="importe" id="importe"
                               value="<?= htmlspecialchars($tarifa['importe']); ?>" class="form-control" required>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="<?= BASE_URL ?>tarifas" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
