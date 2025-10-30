<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="text-dark">📄 Previsualización de Partidos Detectados</h1>
            <p class="text-muted">Revisa los partidos detectados en el PDF y selecciona cuáles deseas importar.</p>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (empty($partidos)): ?>
                <div class="alert alert-warning">
                    ⚠️ No se detectaron partidos válidos en el PDF subido.
                    <br><br>
                    <a href="<?= BASE_URL ?>partidos/importar_pdf" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            <?php else: ?>
                <form method="POST" action="<?= BASE_URL ?>partidos/confirmar_importacion">
                    <div class="card shadow-lg">
                        <div class="card-header bg-gradient-danger text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-basketball-ball"></i> Partidos encontrados (<?= count($partidos) ?>)
                            </h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="bg-gradient-danger text-white text-center">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Jornada</th>
                                    <th>Fecha</th>
                                    <th>Categoría</th>
                                    <th>Equipo Local</th>
                                    <th>Equipo Visitante</th>
                                    <th>Rol</th>
                                    <th>Tablet</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($partidos as $i => $p): ?>
                                    <tr>
                                        <td class="text-center align-middle">
                                            <input type="checkbox" name="importar[]" value="<?= $i ?>" checked>
                                        </td>
                                        <td><?= htmlspecialchars($p['jornada']) ?></td>
                                        <td><?= htmlspecialchars($p['fecha']) ?></td>
                                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                                        <td><?= htmlspecialchars($p['equipo_local']) ?></td>
                                        <td><?= htmlspecialchars($p['equipo_visitante']) ?></td>
                                        <td>
                                            <?php if ($p['rol'] === 'arbitro'): ?>
                                                <span class="badge badge-primary"><i class="fas fa-whistle"></i> Árbitro</span>
                                            <?php else: ?>
                                                <span class="badge badge-success"><i class="fas fa-tablet-alt"></i> Oficial</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($p['tablet'])): ?>
                                                <i class="fas fa-tablet-alt text-success" title="Usa tablet"></i>
                                            <?php else: ?>
                                                <i class="fas fa-ban text-muted" title="Sin tablet"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Botones -->
                        <div class="card-footer text-center">
                            <input type="hidden" name="partidos" value='<?= json_encode($partidos, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-check-circle"></i> Confirmar importación
                            </button>
                            <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary px-4">
                                <i class="fas fa-times-circle"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<!-- Estilos personalizados -->
<style>
    .bg-gradient-danger {
        background: linear-gradient(90deg, #8B0000 0%, #B22222 50%, #006400 100%) !important;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .badge-success {
        background-color: #28a745;
    }
    .badge-primary {
        background-color: #007bff;
    }
    .card {
        border-radius: 0.75rem;
    }
</style>
