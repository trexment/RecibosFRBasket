<?php
// $partidosDetectados = [...]; // Llévalos desde tu previsualización PDF por POST o SESSION
$partidosDetectados = $_SESSION['pdf_partidos_corregir'] ?? [];
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Corrección de datos detectados (PDF)</h1>
            <a href="<?= BASE_URL ?>partidos/importar_pdf" class="btn btn-secondary">Volver</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (empty($partidosDetectados)): ?>
                <div class="alert alert-info">No hay partidos para corregir.</div>
            <?php else: ?>
                <form method="post" action="<?= BASE_URL ?>partidos/confirmar_importacion_pdf_corregida">
                    <div class="card">
                        <div class="card-header"><strong>Corrige y confirma</strong></div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Jornada</th>
                                    <th>Categoría</th>
                                    <th>Equipo Local</th>
                                    <th>Equipo Visitante</th>
                                    <th>Rol</th>
                                    <th>Tablet</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($partidosDetectados as $i => $p): ?>
                                    <tr>
                                        <td><input type="checkbox" name="rows[]" value="<?= $i ?>" checked></td>
                                        <td><input type="date" name="fecha[<?= $i ?>]" class="form-control" value="<?= htmlspecialchars($p['fecha_sql'] ?? '') ?>"></td>
                                        <td><input type="number" name="jornada[<?= $i ?>]" class="form-control" value="<?= htmlspecialchars($p['jornada'] ?? '') ?>"></td>
                                        <td><input type="text" name="categoria[<?= $i ?>]" class="form-control" value="<?= htmlspecialchars($p['categoria'] ?? '') ?>"></td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="equipo_local[<?= $i ?>]" class="form-control" value="<?= htmlspecialchars($p['equipo_local'] ?? '') ?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary btn-sugerir" data-input="el<?= $i ?>" type="button"><i class="fas fa-search"></i></button>
                                                </div>
                                            </div>
                                            <div class="suggestions" id="sugg_el<?= $i ?>"></div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="equipo_visitante[<?= $i ?>]" class="form-control" value="<?= htmlspecialchars($p['equipo_visitante'] ?? '') ?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary btn-sugerir" data-input="ev<?= $i ?>" type="button"><i class="fas fa-search"></i></button>
                                                </div>
                                            </div>
                                            <div class="suggestions" id="sugg_ev<?= $i ?>"></div>
                                        </td>
                                        <td>
                                            <select name="rol[<?= $i ?>]" class="form-control">
                                                <option value="arbitro"       <?= ($p['rol']??'')==='arbitro'?'selected':'' ?>>Árbitro</option>
                                                <option value="oficial"       <?= ($p['rol']??'')==='oficial'?'selected':'' ?>>Oficial</option>
                                                <option value="arbitro_solo"  <?= ($p['rol']??'')==='arbitro_solo'?'selected':'' ?>>Árbitro (solo)</option>
                                                <option value="oficial_solo"  <?= ($p['rol']??'')==='oficial_solo'?'selected':'' ?>>Oficial (solo)</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="checkbox" name="tablet[<?= $i ?>]" value="1" <?= !empty($p['tablet'])?'checked':'' ?>>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-success">Confirmar importación</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
    .suggestions { max-height: 180px; overflow:auto; background:#1114; border:1px solid #6663; border-radius:8px; padding:6px; margin-top:6px; }
</style>

<?php require __DIR__ . '/../partials/footer.php'; ?>
