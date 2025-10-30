<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Importar partidos desde CSV</h1>
            <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-rioja text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-file-upload"></i> Subir archivo CSV</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2">Formato esperado (separado por <strong>punto y coma</strong>):</p>
                    <pre class="bg-light p-3 rounded border">Categoría;Fecha;Equipo Local;Equipo Visitante</pre>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Archivo CSV</label>
                            <input type="file" name="csv" class="form-control-file" accept=".csv" required>
                        </div>
                        <button class="btn btn-rioja"><i class="fas fa-upload"></i> Importar</button>
                        <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary ml-2">Cancelar</a>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
<style>.bg-rioja{background:linear-gradient(90deg,#6e0b14 0%,#9c1d2b 100%)!important;color:#fff!important}</style>
