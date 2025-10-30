<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Previsualización de Partidos Detectados</h1>
            <p class="text-muted">Revisa los partidos encontrados en el PDF y selecciona cuáles deseas importar.</p>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="<?= BASE_URL ?>partidos/confirmar_importacion_pdf">
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="bg-gradient-maroon text-white">
                            <tr>
                                <th>#</th>
                                <th>Jornada</th>
                                <th>Categoría</th>
                                <th>Equipo Local</th>
                                <th>Equipo Visitante</th>
                                <th>Rol</th>
                                <th>Tablet</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($partidos as $i => $p): ?>
                                <tr>
                                    <td><input type="checkbox" name="importar[]" value="<?= $i ?>" checked></td>
                                    <td><?= htmlspecialchars($p['jornada']) ?></td>
                                    <td><?= htmlspecialchars($p['categoria']) ?></td>
                                    <td><?= htmlspecialchars($p['equipo_local']) ?></td>
                                    <td><?= htmlspecialchars($p['equipo_visitante']) ?></td>
                                    <td><?= htmlspecialchars($p['rol']) ?></td>
                                    <td><?= $p['tablet'] ? '✅' : '❌' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success btn-rioja">
                            <i class="fas fa-save"></i> Confirmar importación
                        </button>
                        <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
