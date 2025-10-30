<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Editar Equipo</h1>
            <?php if (!empty($equipo['temporada_id'])): ?>
                <span class="badge badge-info p-2">
                    Temporada <?= htmlspecialchars($equipo['temporada_id']) ?>
                </span>
            <?php else: ?>
                <span class="badge badge-secondary p-2">Sin temporada activa</span>
            <?php endif; ?>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['flash_error']) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <div class="card card-primary shadow-sm">
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>equipos/editar/<?= htmlspecialchars($equipo['id']) ?>">
                        <div class="form-group">
                            <label for="nombre">Nombre del equipo</label>
                            <input
                                    type="text"
                                    name="nombre"
                                    id="nombre"
                                    class="form-control"
                                    value="<?= htmlspecialchars($equipo['nombre'] ?? '') ?>"
                                    required
                            >
                        </div>

                        <div class="form-group">
                            <label for="categoria_id">Categoría</label>
                            <select name="categoria_id" id="categoria_id" class="form-control" required>
                                <option value="">-- Selecciona una categoría --</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['id']) ?>"
                                            <?= ($cat['id'] == $equipo['categoria_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar cambios
                            </button>
                            <a href="<?= BASE_URL ?>equipos" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
