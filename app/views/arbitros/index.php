<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
<?php require_once __DIR__ . '/../partials/alerts.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-header header-rioja">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-address-book"></i> Listado de árbitros
                    </h3>
                    <a href="<?= BASE_URL ?>arbitros/crear" class="btn btn-header">
                        <i class="fas fa-plus"></i> Nuevo Árbitro
                    </a>
                </div>
                <div class="card-body">
                    <!-- FILTROS -->
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Buscar por nombre o email</label>
                            <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select name="activo" class="form-control">
                                <option value="">Todos</option>
                                <option value="SI" <?= (($_GET['activo'] ?? '') == 'SI') ? 'selected' : '' ?>>Activos</option>
                                <option value="NO" <?= (($_GET['activo'] ?? '') == 'NO') ? 'selected' : '' ?>>Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Mostrar</label>
                            <select name="per_page" class="form-control" onchange="this.form.submit()">
                                <?php foreach ([10,20,50,100] as $n): ?>
                                    <option value="<?= $n ?>" <?= (($_GET['per_page'] ?? 20) == $n) ? 'selected' : '' ?>><?= $n ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block w-100"><i class="fas fa-search"></i></button>
                        </div>
                        <div class="col-12 text-end">
                            <a href="<?= BASE_URL ?>arbitros" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Limpiar filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABLA -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <?php
                    // Función PHP inline para formatear teléfono en bloques de 2
                    function formatearTelefono($numero) {
                        // Quita espacios y caracteres no numéricos
                        $num = preg_replace('/\D/', '', $numero);
                        // Agrupa en bloques de 2 números
                        return preg_replace('/^(\d{3})(\d{2})(\d{2})(\d{2})$/', '$1 $2 $3 $4', $num);
                    }
                    ?>

                    <table class="table table-hover mb-0">
                        <thead class="bg-gradient-maroon">
                        <tr class="text-center">
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Activo</th>
                            <th style="width: 120px;">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($arbitros)): ?>
                            <tr><td colspan="5" class="text-center p-4">No hay árbitros registrados.</td></tr>
                        <?php else: foreach ($arbitros as $a): ?>
                            <tr class="text-center <?= ($a['activo'] === 'NO') ? 'table-danger' : '' ?>">
                                <td class="text-left"><?= htmlspecialchars($a['nombre']) ?></td>
                                <td class="text-left"><?= htmlspecialchars($a['email'] ?? '—') ?></td>
                                <td>
                                    <?= $a['telefono']
                                            ? htmlspecialchars(formatearTelefono($a['telefono']))
                                            : '—' ?>
                                </td>

                                <td><strong><?= htmlspecialchars($a['activo']) ?></strong></td>
                                <td>
                                    <a href="<?= BASE_URL ?>arbitros/editar/<?= $a['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <a href="<?= BASE_URL ?>arbitros/eliminar/<?= $a['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirmarEliminacion('<?= htmlspecialchars($a['nombre']) ?>');">
                                        <i class="fas fa-trash"></i>
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PAGINACIÓN -->
            <?php if (!empty($totalPaginas) && $totalPaginas > 1): ?>
                <nav aria-label="Paginación" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?= ($i == $paginaActual) ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    function confirmarEliminacion(nombre) {
        return confirm(`¿Seguro que deseas eliminar al árbitro "${nombre}"?\nEsta acción no se puede deshacer.`);
    }
</script>


<?php require_once __DIR__ . '/../partials/footer.php'; ?>
