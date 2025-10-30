<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Importar Partidos desde PDF</h1>
            <p class="text-muted">Sube el archivo oficial de designación para detectar y añadir automáticamente los partidos.</p>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body text-center">
                    <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>partidos/procesar_importacion_pdf">
                        <div class="form-group">
                            <input type="file" name="pdf" accept="application/pdf" required class="form-control-file mb-3">
                        </div>
                        <button type="submit" class="btn btn-success btn-rioja">
                            <i class="fas fa-file-import"></i> Analizar PDF
                        </button>
                        <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary ml-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
