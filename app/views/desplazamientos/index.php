<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h1>Desplazamientos (€/km)</h1>
                <a href="<?= BASE_URL ?>desplazamientos/crear" class="btn btn-success"><i class="fas fa-plus"></i> Nuevo</a>
            </div>
        </section>

        <section class="content">
            <div class="card card-outline card-maroon">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-maroon text-white">
                        <tr>
                            <th>Temporada</th>
                            <th>€ / km</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($desplazamientos as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['temporada_nombre'] ?? '-') ?></td>
                                <td><?= number_format($d['precio_km'], 3, ',', '.') ?></td>
                                <td><?= $d['activo'] ? 'Sí' : 'No' ?></td>
                                <td>
                                    <a class="btn btn-warning btn-sm" href="<?= BASE_URL ?>desplazamientos/editar/<?= $d['id'] ?>"><i class="fas fa-edit"></i></a>
                                    <a class="btn btn-danger btn-sm" href="<?= BASE_URL ?>desplazamientos/eliminar/<?= $d['id'] ?>" onclick="return confirm('¿Eliminar?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

