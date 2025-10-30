<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Editar Precio</h1>
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
                    <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($precio['categoria']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Designación</label>
                    <select name="designacion" class="form-control" required>
                        <?php
                        $designaciones = ['2A_2O', '2A_1O', '1A_2O', '1A_1O', '1A_0O'];
                        foreach ($designaciones as $d) {
                            $sel = ($precio['designacion'] == $d) ? 'selected' : '';
                            echo "<option value='$d' $sel>$d</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Importe (€)</label>
                    <input type="number" name="importe" class="form-control" step="0.01" value="<?= htmlspecialchars($precio['importe']) ?>" required>
                </div>

                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

