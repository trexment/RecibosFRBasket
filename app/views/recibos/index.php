<?php
$usuario = $_SESSION['usuario'] ?? null;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Recibos</h1>
            <p class="text-muted">Selecciona un rango de fechas para ver tus partidos y generar los recibos correspondientes.</p>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- 🔸 Formulario de búsqueda -->
            <form method="GET" action="<?= BASE_URL ?>recibos" class="card card-body shadow-sm mb-4">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="desde">Desde</label>
                        <input type="date" class="form-control" name="desde" id="desde"
                               value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="hasta">Hasta</label>
                        <input type="date" class="form-control" name="hasta" id="hasta"
                               value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="retencion">Retención (%)</label>
                        <input type="number" class="form-control" name="retencion" id="retencion" min="0" max="100" step="0.1"
                               value="<?= htmlspecialchars($_GET['retencion'] ?? '') ?>">
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="fas fa-search"></i> Buscar partidos
                    </button>
                </div>
            </form>

            <!-- 🔸 Resultados -->
            <?php if (!empty($partidos)): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-header header-rioja">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-basketball-ball"></i> Partidos encontrados (<?= count($partidos) ?>)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped text-center mb-0">
                                <thead class="bg-gradient-maroon text-white">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Jornada</th>
                                    <th>Equipo Local</th>
                                    <th>Equipo Visitante</th>
                                    <th>Categoría</th>
                                    <th>Desplazamiento</th>
                                    <th>Tablet</th> <!-- ✅ Nueva columna -->
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($partidos as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['fecha']) ?></td>
                                        <td><?= htmlspecialchars($p['jornada']) ?></td>
                                        <td><?= htmlspecialchars($p['equipo_local'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($p['equipo_visitante'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($p['categoria'] ?? '-') ?></td>
                                        <td>
                                            <?php if (!empty($p['importe_desplazamiento']) && $p['importe_desplazamiento'] > 0): ?>
                                                <i class="fas fa-car text-success" title="Con desplazamiento"></i>
                                            <?php else: ?>
                                                <i class="fas fa-minus text-muted" title="Sin desplazamiento"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($p['usa_tablet'])): ?>
                                                <i class="fas fa-tablet-alt text-success" title="Usa tablet (+1 €)"></i>
                                            <?php else: ?>
                                                <i class="fas fa-ban text-muted" title="Sin tablet"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 🔸 Botones de exportación PDF -->
                    <form method="POST" action="<?= BASE_URL ?>recibos/generar" class="p-3 text-center">
                        <input type="hidden" name="desde" value="<?= htmlspecialchars($_GET['desde']) ?>">
                        <input type="hidden" name="hasta" value="<?= htmlspecialchars($_GET['hasta']) ?>">
                        <input type="hidden" name="retencion" value="<?= htmlspecialchars($_GET['retencion']) ?>">

                        <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
                            <button type="submit" name="tipo" value="partido" class="btn btn-success px-4">
                                <i class="fas fa-file-pdf"></i> Exportar PDF (Partidos)
                            </button>
                            <button type="submit" name="tipo" value="desplazamiento" class="btn btn-secondary px-4">
                                <i class="fas fa-route"></i> Exportar PDF (Desplazamientos)
                            </button>
                        </div>
                    </form>
                </div>

            <?php elseif (!empty($_GET['desde']) && !empty($_GET['hasta'])): ?>
                <div class="alert alert-warning text-center mt-4">
                    <i class="fas fa-info-circle"></i> No se encontraron partidos en el rango indicado.
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
