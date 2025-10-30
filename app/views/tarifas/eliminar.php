<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Eliminar Tarifa</h1>
            <a href="<?= BASE_URL ?>tarifas" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-danger shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar eliminación
                    </h3>
                </div>

                <div class="card-body">
                    <?php if (!isset($tarifa)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> Tarifa no encontrada o ya eliminada.
                        </div>
                    <?php else: ?>
                        <p class="lead text-center">
                            ¿Estás seguro de que deseas eliminar la siguiente tarifa?
                        </p>

                        <table class="table table-bordered text-center mt-4">
                            <tr>
                                <th>Código</th>
                                <td><?= htmlspecialchars($tarifa['codigo']) ?></td>
                            </tr>
                            <tr>
                                <th>Descripción</th>
                                <td><?= htmlspecialchars($tarifa['descripcion']) ?></td>
                            </tr>
                            <tr>
                                <th>Categoría</th>
                                <td><?= htmlspecialchars($tarifa['categoria']) ?></td>
                            </tr>
                            <tr>
                                <th>Rol</th>
                                <td><?= ucfirst($tarifa['rol']) ?></td>
                            </tr>
                            <tr>
                                <th>Importe (€)</th>
                                <td><?= number_format($tarifa['importe'], 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>Temporada</th>
                                <td><?= htmlspecialchars($tarifa['temporada']) ?></td>
                            </tr>
                        </table>

                        <div class="text-center mt-4">
                            <form method="POST" action="<?= BASE_URL ?>tarifas/eliminar/<?= $tarifa['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-lg mr-2">
                                    <i class="fas fa-trash-alt"></i> Eliminar definitivamente
                                </button>
                                <a href="<?= BASE_URL ?>tarifas" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
