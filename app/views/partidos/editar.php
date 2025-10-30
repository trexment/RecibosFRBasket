<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fas fa-edit"></i> Editar Partido</h1>
    </section>

    <section class="content">
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (isset($error_duplicado)): ?>
                    <div class="alert alert-warning"><?= $error_duplicado ?></div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>partidos/editar/<?= $partido['id'] ?>" method="POST">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="fecha">Fecha</label>
                            <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($partido['fecha']) ?>" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label for="jornada">Jornada</label>
                            <input type="text" id="jornada" name="jornada" value="<?= htmlspecialchars($partido['jornada']) ?>" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label for="categoria_id">Categoría</label>
                            <select id="categoria_id" name="categoria_id" class="form-control" required>
                                <option value="">-- Selecciona Categoría --</option>
                                <?php foreach ($categorias ?? [] as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $partido['categoria_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="rol">Rol</label>
                            <select id="rol" name="rol" class="form-control" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= htmlspecialchars($r) ?>" <?= ($partido['rol'] === $r) ? 'selected' : '' ?>>
                                        <?= ucfirst(str_replace('_', ' ', $r)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="usa_tablet">Tablet</label><br>
                            <input type="checkbox" id="usa_tablet" name="usa_tablet" value="1" <?= ($partido['usa_tablet']) ? 'checked' : '' ?>>
                            <span class="text-muted">(+1 € si es oficial)</span>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="equipo_local_id">Equipo Local</label>
                            <select id="equipo_local_id" name="equipo_local_id" class="form-control" required>
                                <option value="">-- Selecciona equipo local --</option>
                                <?php foreach ($equipos as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= ($partido['equipo_local_id'] == $eq['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($eq['nombre']) ?> (<?= htmlspecialchars($eq['categoria_nombre']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="equipo_visitante_id">Equipo Visitante</label>
                            <select id="equipo_visitante_id" name="equipo_visitante_id" class="form-control" required>
                                <option value="">-- Selecciona equipo visitante --</option>
                                <?php foreach ($equipos as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= ($partido['equipo_visitante_id'] == $eq['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($eq['nombre']) ?> (<?= htmlspecialchars($eq['categoria_nombre']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3">
                            <label for="km">Kilómetros</label>
                            <input type="number" id="km" name="km" value="<?= htmlspecialchars($partido['km']) ?>" step="0.1" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="importe_desplazamiento">Importe Desplazamiento (€)</label>
                            <input type="number" id="importe_desplazamiento" name="importe_desplazamiento" value="<?= htmlspecialchars($partido['importe_desplazamiento']) ?>" step="0.01" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="dieta">Dieta (€)</label>
                            <input type="number" id="dieta" name="dieta" value="<?= htmlspecialchars($partido['dieta']) ?>" step="0.01" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="importe">Importe Total (€)</label>
                            <input type="number" id="importe" name="importe" value="<?= htmlspecialchars($partido['importe']) ?>" step="0.01" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
    // === Cálculo automático de tarifa ===
    document.getElementById('rol').addEventListener('change', calcularImporte);
    document.getElementById('categoria_id').addEventListener('change', calcularImporte);
    document.getElementById('usa_tablet').addEventListener('change', calcularImporte);

    function calcularImporte() {
        const categoria_id = document.getElementById('categoria_id').value;
        const rol = document.getElementById('rol').value;
        const temporada_id = <?= json_encode($temporada_activa['id']) ?>;
        const usa_tablet = document.getElementById('usa_tablet').checked ? 1 : 0;

        if (!categoria_id || !rol) return;

        fetch('<?= BASE_URL ?>partidos/obtenerTarifa', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                categoria_id,
                rol,
                temporada_id
            })
        })
            .then(response => response.json())
            .then(data => {
                let importe = data.importe ?? 0;
                if ((rol.includes('oficial')) && usa_tablet) {
                    importe += 1;
                }
                document.getElementById('importe').value = importe.toFixed(2);
            })
            .catch(err => console.error(err));
    }
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
