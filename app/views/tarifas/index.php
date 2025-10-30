<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fas fa-euro-sign"></i> Tarifas</h1>
    </section>

    <section class="content">
        <?php if (isset($_SESSION['flash_tarifa_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['flash_tarifa_success']); unset($_SESSION['flash_tarifa_success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_tarifa_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['flash_tarifa_error']); unset($_SESSION['flash_tarifa_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-gradient-maroon d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 text-white">
                    <i class="fas fa-list-alt"></i> Listado de Tarifas
                </h3>

                <?php if ($this->isAdmin): ?>
                    <a href="<?= BASE_URL ?>tarifas/crear" class="btn btn-rioja btn-sm shadow-sm">
                        <i class="fas fa-plus"></i> Nueva Tarifa
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <!-- Filtro por temporada y categoría -->
                <form method="GET" action="<?= BASE_URL ?>tarifas" class="row mb-4">
                    <div class="col-md-4 mb-2">
                        <label for="temporada_id" class="form-label">Temporada</label>
                        <select name="temporada_id" id="temporada_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            <?php foreach ($temporadas as $temp): ?>
                                <option value="<?= $temp['id']; ?>"
                                        <?= (isset($_GET['temporada_id']) && $_GET['temporada_id'] == $temp['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($temp['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="categoria_id" class="form-label">Categoría</label>
                        <select name="categoria_id" id="categoria_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id']; ?>"
                                        <?= (isset($_GET['categoria_id']) && $_GET['categoria_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($cat['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table id="tablaTarifas" class="table table-bordered table-hover align-middle">
                        <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Categoría</th>
                            <th>Temporada</th>
                            <th>Rol</th>
                            <th>Importe (€)</th>
                            <?php if ($this->isAdmin): ?>
                                <th style="width: 130px;">Acciones</th>
                            <?php endif; ?>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if (!empty($tarifas)): ?>
                            <?php foreach ($tarifas as $tarifa): ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($tarifa['id']); ?></td>
                                    <td><?= htmlspecialchars($tarifa['categoria_nombre']); ?></td>
                                    <td><?= htmlspecialchars($tarifa['temporada_nombre']); ?></td>
                                    <td><?= ucfirst(htmlspecialchars($tarifa['rol'])); ?></td>
                                    <td class="text-end"><?= number_format((float)$tarifa['importe'], 2, ',', '.'); ?> €</td>

                                    <?php if ($this->isAdmin): ?>
                                        <td class="text-center">
                                            <a href="<?= BASE_URL ?>tarifas/editar/<?= $tarifa['id']; ?>"
                                               class="btn btn-sm btn-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>tarifas/eliminar/<?= $tarifa['id']; ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('¿Seguro que deseas eliminar esta tarifa?');"
                                               title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="<?= $this->isAdmin ? 6 : 5; ?>" class="text-center">No hay tarifas registradas</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
