<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h1>Nuevo Precio</h1>
                <a href="<?= BASE_URL ?>precios" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <form method="POST" class="card card-body">
                    <div class="form-group">
                        <label>Categoría</label>
                        <input type="text" name="categoria" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Designación</label>
                        <select name="designacion" class="form-control" required>
                            <option value="2A_2O">2 Árbitros / 2 Oficiales</option>
                            <option value="2A_1O">2 Árbitros / 1 Oficial</option>
                            <option value="1A_2O">1 Árbitro / 2 Oficiales</option>
                            <option value="1A_1O">1 Árbitro / 1 Oficial</option>
                            <option value="1A_0O">1 Árbitro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Importe (€)</label>
                        <input type="number" name="importe" class="form-control" step="0.01" required>
                    </div>

                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
                </form>
            </div>
        </section>
    </div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

