<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>


<div class="content-wrapper">
    <!-- Encabezado -->
    <section class="content-header">
        <div class="container-fluid">
            <?php
            $nombreUsuario = $_SESSION['nombre']
                    ?? ($_SESSION['usuario']['nombre'] ?? 'Invitado');
            ?>
            <h2 class="mb-0">Bienvenido, <?= htmlspecialchars($nombreUsuario) ?></h2>


            <small class="text-muted">
                Resumen de tu actividad arbitral —
                Temporada activa:
                <strong><?= htmlspecialchars($temporada_activa ?? 'Sin definir') ?></strong>
            </small>
        </div>
    </section>

    <!-- Contenido principal -->
    <section class="content">
        <div class="container-fluid mt-3">

            <div class="row">
                <!-- 🟢 Partidos por categoría -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-white" style="background: linear-gradient(90deg, #4CAF50, #C62828);">
                            <i class="fas fa-chart-pie"></i> Partidos por categoría
                        </div>
                        <div class="card-body">
                            <?php if (!empty($partidosPorCategoria)): ?>
                                <canvas id="graficoPartidosCategoria" height="200"></canvas>
                            <?php else: ?>
                                <p class="text-muted text-center mt-4 mb-4">No hay partidos registrados para esta temporada.</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($partidosPorCategoria)): ?>
                            <div class="card-footer p-0">
                                <table class="table table-striped table-hover mb-0 text-center">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Categoría</th>
                                        <th>Partidos</th>
                                        <th>%</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $totalPartidos = array_sum(array_column($partidosPorCategoria, 'cantidad'));
                                    foreach ($partidosPorCategoria as $fila):
                                        $porcentaje = $totalPartidos > 0
                                                ? round(($fila['cantidad'] / $totalPartidos) * 100, 1)
                                                : 0;
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($fila['categoria'] ?? '—') ?></td>
                                            <td><?= htmlspecialchars($fila['cantidad']) ?></td>
                                            <td><?= $porcentaje ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-secondary fw-bold">
                                        <td>Total</td>
                                        <td><?= $totalPartidos ?></td>
                                        <td>100%</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 💶 Ingresos mensuales -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-white" style="background: linear-gradient(90deg, #1976D2, #42A5F5);">
                            <i class="fas fa-euro-sign"></i> Ingresos mensuales
                        </div>
                        <div class="card-body">
                            <?php if (!empty($ingresosMensuales)): ?>
                                <canvas id="graficoIngresos" height="200"></canvas>
                            <?php else: ?>
                                <p class="text-muted text-center mt-4 mb-4">Aún no hay ingresos registrados.</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($ingresosMensuales)): ?>
                            <div class="card-footer p-0">
                                <table class="table table-striped table-hover mb-0 text-center">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Mes</th>
                                        <th>Importe (€)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $totalIngresos = array_sum(array_column($ingresosMensuales, 'total'));
                                    foreach ($ingresosMensuales as $fila):
                                        $mes = date('m/Y', strtotime($fila['ym'] . '-01'));
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($mes) ?></td>
                                            <td><?= number_format($fila['total'], 2, ',', '.') ?> €</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-secondary fw-bold">
                                        <td>Total</td>
                                        <td><?= number_format($totalIngresos, 2, ',', '.') ?> €</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

<!-- ============================ -->
<!-- 📊 BLOQUE DE GRÁFICOS -->
<!-- ============================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // === 🟢 Partidos por categoría ===
        const categorias = <?= json_encode(array_column($partidosPorCategoria ?? [], 'categoria'), JSON_UNESCAPED_UNICODE); ?>;
        const cantidades = <?= json_encode(array_column($partidosPorCategoria ?? [], 'cantidad'), JSON_UNESCAPED_UNICODE); ?>;

        if (categorias.length > 0 && cantidades.length > 0) {
            const ctx1 = document.getElementById('graficoPartidosCategoria');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'doughnut',
                    data: {
                        labels: categorias,
                        datasets: [{
                            data: cantidades,
                            backgroundColor: [
                                '#F94144', '#F3722C', '#F8961E', '#F9844A', '#F9C74F',
                                '#90BE6D', '#43AA8B', '#577590', '#277DA1', '#7209B7'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        return `${label}: ${value} partido${value !== 1 ? 's' : ''}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // === 💙 Ingresos mensuales ===
        const meses = <?= json_encode(array_column($ingresosMensuales ?? [], 'ym'), JSON_UNESCAPED_UNICODE); ?>;
        const totales = <?= json_encode(array_column($ingresosMensuales ?? [], 'total'), JSON_UNESCAPED_UNICODE); ?>;

        if (meses.length > 0 && totales.length > 0) {
            const ctx2 = document.getElementById('graficoIngresos');
            if (ctx2) {
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: meses.map(m => m.replace('-', '/')),
                        datasets: [{
                            label: '€ Ingresos',
                            data: totales,
                            backgroundColor: '#3B82F6',
                            borderColor: '#1E40AF',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value + ' €';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' €';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    });
</script>
