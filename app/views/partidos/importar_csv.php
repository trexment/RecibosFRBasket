<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fas fa-file-csv"></i> Importar Partidos desde CSV</h1>
    </section>

    <section class="content">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= BASE_URL ?>partidos/importar_csv" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="csv">Selecciona el archivo CSV:</label>
                        <input type="file" name="csv" id="csv" class="form-control" accept=".csv" required>
                        <small class="text-muted">
                            El archivo debe estar delimitado por <b>;</b> y contener las columnas:
                            <code>Fecha</code>, <code>Categoría</code>, <code>Local</code>, <code>Visitante</code>.
                        </small>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-search"></i> Analizar CSV
                        </button>
                        <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
