<?php

$usuario = $_SESSION['usuario'] ?? null;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>





<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Temporadas</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <a href="<?= BASE_URL ?>temporadas/crear" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Nueva Temporada
            </a>

            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($temporadas)): ?>
                    <tr>
                        <td colspan="3" class="text-center">No hay temporadas registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($temporadas as $temporada): ?>
                        <tr>
                            <td><?= htmlspecialchars($temporada['nombre']) ?></td>
                            <td>
                                <?php if (!$temporada['activa']): ?>
                                    <a href="<?= BASE_URL ?>temporadas/activar/<?= $temporada['id'] ?>" class="btn btn-sm btn-outline-primary">Activar</a>
                                <?php else: ?>
                                    <span class="badge badge-success">Activa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>temporadas/editar/<?= $temporada['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="<?= BASE_URL ?>temporadas/eliminar/<?= $temporada['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta temporada?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
