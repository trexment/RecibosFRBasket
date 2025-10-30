<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fas fa-calendar-alt"></i> Partidos</h1>
    </section>

    <section class="content">
        <div class="card shadow-sm border-0">
            <div class="card-header header-rioja d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-list"></i> Listado de Partidos
                </h3>
                <div>
                    <a href="<?= BASE_URL ?>partidos/importar_pdf" class="btn btn-danger me-2">
                        <i class="fas fa-file-pdf"></i> Importar PDF
                    </a>
                    <a href="<?= BASE_URL ?>partidos/importar_csv" class="btn btn-danger me-2">
                        <i class="fas fa-file-csv"></i> Importar CSV
                    </a>
                    <a href="<?= BASE_URL ?>partidos/crear" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Nuevo Partido
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- FILTROS -->
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <select name="temporada_id" class="form-control">
                            <option value="">-- Todas las temporadas --</option>
                            <?php foreach ($temporadas as $t): ?>
                                <option value="<?= $t['id']; ?>" <?= ($temporadaSeleccionada == $t['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($t['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="categoria_id" class="form-control">
                            <option value="">-- Todas las categorías --</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id']; ?>" <?= ($categoriaSeleccionada == $c['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($c['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="desde" value="<?= htmlspecialchars($fechaDesde); ?>" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="hasta" value="<?= htmlspecialchars($fechaHasta); ?>" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </form>

                <!-- TABLA -->
                <div class="table-responsive">
                    <table class="table table-striped align-middle text-center">
                        <thead class="table-danger">
                        <tr>
                            <th>Fecha</th>
                            <th>Jornada</th>
                            <th>Local</th>
                            <th>Visitante</th>
                            <th>Categoría</th>
                            <th>Rol</th>
                            <th>Tablet</th>
                            <th>Importe (€)</th>
                            <th>Desplazamiento (€)</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($partidos)): ?>
                            <?php foreach ($partidos as $p): ?>
                                <?php
                                // Crear abreviatura de categoría
                                $cat = strtoupper(trim($p['categoria_nombre'] ?? ''));
                                $abbr = '';
                                foreach (explode(' ', $cat) as $w) {
                                    if (mb_strlen($w) > 2) $abbr .= mb_substr($w, 0, 1);
                                }

                                // Asignar color según rol
                                $badgeClass = 'secondary';
                                switch ($p['rol']) {
                                    case 'arbitro':
                                    case 'arbitro_solo':
                                        $badgeClass = 'primary'; break;
                                    case 'oficial':
                                    case 'oficial_solo':
                                        $badgeClass = 'success'; break;
                                }

                                // Mostrar icono tablet si aplica
                                $usaTablet = !empty($p['usa_tablet']) ? true : false;
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($p['fecha'])); ?></td>
                                    <td><?= htmlspecialchars($p['jornada']); ?></td>
                                    <td><?= htmlspecialchars($p['equipo_local']); ?></td>
                                    <td><?= htmlspecialchars($p['equipo_visitante']); ?></td>
                                    <td>
                                        <?php if ($cat): ?>
                                            <?= htmlspecialchars($cat); ?>
                                            <small class="text-muted">(<?= $abbr; ?>)</small>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-<?= $badgeClass; ?>"><?= ucfirst($p['rol']); ?></span></td>
                                    <td>
                                        <?php if ($usaTablet): ?>
                                            <i class="fas fa-tablet-alt text-success" title="Usa tablet (+1 €)"></i>
                                        <?php else: ?>
                                            <i class="fas fa-ban text-muted" title="Sin tablet"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format($p['importe'] ?? 0, 2, ',', '.'); ?> €</td>
                                    <td><?= number_format($p['importe_desplazamiento'] ?? 0, 2, ',', '.'); ?> €</td>
                                    <td>
                                        <a href="<?= BASE_URL ?>partidos/editar/<?= $p['id']; ?>" class="btn btn-success btn-sm me-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>partidos/eliminar/<?= $p['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Seguro que deseas eliminar este partido?');"
                                           title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="10" class="text-muted py-4">No hay partidos registrados.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                <?php if ($totalPaginas > 1): ?>
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?= ($i == $paginaActual) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?= $i; ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
