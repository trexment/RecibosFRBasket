<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h1>Confirmar importación desde PDF</h1>
                <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <form method="POST" action="<?= BASE_URL ?>partidos/confirmarPDF">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-rioja text-white">
                            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Revisión de partidos detectados</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Rol general:</label>
                                    <select name="rol_global" class="form-control">
                                        <option value="Árbitro">Árbitro</option>
                                        <option value="Oficial">Oficial</option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check">
                                        <input type="checkbox" name="usa_tablet" id="usa_tablet" value="1" class="form-check-input">
                                        <label class="form-check-label" for="usa_tablet">Usé tablet en estos partidos</label>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered table-hover text-nowrap">
                                <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Local</th>
                                    <th>Visitante</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($_SESSION['pdf_partidos'] as $i => $p): ?>
                                    <tr>
                                        <td><?= $i+1 ?></td>
                                        <td><?= htmlspecialchars($p['fecha']) ?></td>
                                        <td><?= htmlspecialchars($p['hora']) ?></td>
                                        <td><?= htmlspecialchars($p['local']) ?></td>
                                        <td><?= htmlspecialchars($p['visitante']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-rioja"><i class="fas fa-check"></i> Confirmar e importar</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>