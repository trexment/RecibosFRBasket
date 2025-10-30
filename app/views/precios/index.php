<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Precios - Temporada <?= htmlspecialchars($temporada_nombre) ?></h1>
            <a href="<?= BASE_URL ?>precios/crear" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo Precio
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <form method="GET" class="card card-body mb-4">
                <div class="form-row">
                    <div class="col-md-6">
                        <label>Buscar por categoría</label>
                        <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Mostrar</label>
                        <select name="per_page" class="form-control" onchange="this.form.submit()">
                            <?php foreach ([10, 20, 50, 100] as $n): ?>
                                <option value="<?= $n ?>" <?= ($_GET['per_page'] ?? 20) == $n ? 'selected' : '' ?>><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                <div class="text-right mt-2">
                    <a href="<?= BASE_URL ?>precios" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Limpiar filtros
                    </a>
                </div>
            </form>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="thead-dark">
                        <tr>
                            <th>Categoría</th>
                            <th>Importe (€)</th>
                            <th style="width: 120px;">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($precios)): ?>
                            <tr><td colspan="3" class="text-center">No hay precios definidos.</td></tr>
                        <?php else: ?>
                            <?php foreach ($precios as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['categoria']) ?></td>
                                    <td><?= number_format($p['importe'], 2) ?> €</td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>precios/editar/<?= $p['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <a href="<?= BASE_URL ?>precios/eliminar/<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar precio?');"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
