<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Editar Categoría</h1>
            <a href="<?= BASE_URL ?>categorias" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Mensajes flash -->
            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['flash_error']) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header text-white bg-rioja">
                    <h3 class="card-title mb-0"><i class="fas fa-pen"></i> Editar categoría existente</h3>
                </div>

                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre de la categoría</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                   value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="abreviatura">Abreviatura</label>
                            <input type="text" name="abreviatura" id="abreviatura" class="form-control"
                                   value="<?= htmlspecialchars($categoria['abreviatura'] ?? '') ?>" maxlength="10">
                        </div>

                        <div class="form-group">
                            <label for="temporada_id">Temporada</label>
                            <select name="temporada_id" id="temporada_id" class="form-control">
                                <option value="">-- Sin asignar --</option>
                                <?php foreach ($temporadas as $temp): ?>
                                    <option value="<?= htmlspecialchars($temp['id']) ?>"
                                            <?= ($categoria['temporada_id'] ?? null) == $temp['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($temp['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-rioja">
                                <i class="fas fa-save"></i> Guardar cambios
                            </button>
                            <a href="<?= BASE_URL ?>categorias" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<style>
    .bg-rioja {
        background: linear-gradient(90deg, #6e0b14 0%, #9c1d2b 100%) !important;
        color: #fff !important;
    }
</style>
