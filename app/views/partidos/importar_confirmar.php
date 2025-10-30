<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Previsualización CSV</h1>
            <a href="<?= BASE_URL ?>partidos/importar" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="<?= BASE_URL ?>partidos/confirmar">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-rioja text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-list"></i> Revisa y confirma</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Rol para todos:</label>
                                <select name="rol_global" class="form-control">
                                    <option value="Árbitro">Árbitro</option>
                                    <option value="Oficial">Oficial</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" name="usa_tablet" id="usa_tablet" value="1" class="form-check-input">
                                    <label class="form-check-label" for="usa_tablet">Usé tablet</label>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Categoría</th>
                                    <th>Fecha</th>
                                    <th>Local</th>
                                    <th>Visitante</th>
                                    <th>Estado</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach (($_SESSION['csv_preview'] ?? []) as $i => $p): ?>
                                    <tr class="<?= $p['duplicado'] ? 'table-warning' : '' ?>">
                                        <td><?= $i+1 ?></td>
                                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                                        <td><?= htmlspecialchars($p['fecha']) ?></td>
                                        <td><?= htmlspecialchars($p['local']) ?></td>
                                        <td><?= htmlspecialchars($p['visitante']) ?></td>
                                        <td>
                                            <?php if ($p['duplicado']): ?>
                                                <span class="badge badge-warning">Duplicado</span>
                                            <?php elseif (!$p['categoria_id'] || !$p['equipo_local_id'] || !$p['equipo_visitante_id']): ?>
                                                <span class="badge badge-danger">Incompleto</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">OK</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-rioja" type="submit"><i class="fas fa-check"></i> Confirmar e importar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
