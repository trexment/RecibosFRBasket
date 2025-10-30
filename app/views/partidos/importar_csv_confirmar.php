<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fas fa-file-csv"></i> Previsualización de Partidos CSV</h1>
    </section>

    <section class="content">
        <div class="card shadow-sm">
            <div class="card-body">

                <?php if (!empty($preview)): ?>
                    <form action="<?= BASE_URL ?>partidos/confirmar_importacion_csv" method="POST">
                        <p class="text-muted">
                            Revisa los datos antes de importar. Puedes corregir nombres o cambiar el rol de cada partido.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-success">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Jornada</th>
                                    <th>Categoría</th>
                                    <th>Equipo Local</th>
                                    <th>Equipo Visitante</th>
                                    <th>Rol</th>
                                    <th>Tablet</th>
                                    <th>Importar</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $db = Database::getInstance();
                                $roles = $db->query("SELECT DISTINCT rol FROM tarifas ORDER BY rol ASC")->fetchAll(PDO::FETCH_COLUMN);
                                foreach ($preview as $i => $p): ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><input type="text" name="partidos[<?= $i ?>][fecha]" value="<?= htmlspecialchars($p['fecha'] ?? '') ?>" class="form-control text-center"></td>
                                        <td><input type="text" name="partidos[<?= $i ?>][jornada]" value="<?= htmlspecialchars($p['jornada'] ?? '') ?>" class="form-control text-center"></td>
                                        <td><input type="text" name="partidos[<?= $i ?>][categoria]" value="<?= htmlspecialchars($p['categoria'] ?? '') ?>" class="form-control text-center"></td>
                                        <td><input type="text" name="partidos[<?= $i ?>][equipo_local]" value="<?= htmlspecialchars($p['equipo_local'] ?? '') ?>" class="form-control"></td>
                                        <td><input type="text" name="partidos[<?= $i ?>][equipo_visitante]" value="<?= htmlspecialchars($p['equipo_visitante'] ?? '') ?>" class="form-control"></td>
                                        <td class="text-center">
                                            <select name="partidos[<?= $i ?>][rol]" class="form-control">
                                                <?php foreach ($roles as $rol): ?>
                                                    <option value="<?= htmlspecialchars($rol) ?>" <?= ($p['rol'] ?? '') === $rol ? 'selected' : '' ?>>
                                                        <?= ucfirst(str_replace('_', ' ', $rol)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="partidos[<?= $i ?>][tablet]" value="1" <?= !empty($p['tablet']) ? 'checked' : '' ?>>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="importar[]" value="<?= $i ?>" checked>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-cloud-upload-alt"></i> Confirmar Importación
                            </button>
                            <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning text-center mt-4">
                        ⚠️ No se detectaron partidos válidos en el archivo CSV.<br>
                        Revisa que esté delimitado por <b>;</b> y contenga las columnas necesarias.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
