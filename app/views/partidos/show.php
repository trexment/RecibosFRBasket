<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<!-- 🌍 CONTENEDOR PRINCIPAL -->
<div class="content-wrapper">

    <!-- 🧭 ENCABEZADO DE PÁGINA -->
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-2 mb-md-0">Partidos</h1>

            <!-- 📆 Selector de temporada + botón crear -->
            <div class="d-flex align-items-center gap-2">
                <form method="GET" action="<?= BASE_URL ?>partidos" class="form-inline">
                    <select name="temporada_id" class="form-control mr-2" onchange="this.form.submit()">
                        <?php foreach ($temporadas as $temp): ?>
                            <option value="<?= htmlspecialchars($temp['id']) ?>"
                                    <?= ($temp['id'] == ($temporadaActiva['id'] ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($temp['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="<?= BASE_URL ?>partidos/crear" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nuevo partido
                </a>
            </div>
        </div>
    </section>

    <!-- 📋 CONTENIDO PRINCIPAL -->
    <section class="content">
        <div class="container-fluid">

            <!-- ✅ Mensajes flash -->
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['flash_success']) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['flash_error']) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <!-- 📦 TARJETA DE LISTADO -->
            <div class="card shadow-sm mt-3">
                <div class="card-body table-responsive">

                    <!-- ⚽ TABLA DE PARTIDOS -->
                    <table id="tablaPartidos" class="table table-bordered table-hover text-nowrap mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Jornada</th>
                            <th>Local</th>
                            <th>Visitante</th>
                            <th>Categoría</th>
                            <th>Importe (€)</th>
                            <th>Desplazamiento (€)</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($partidos)): ?>
                            <?php foreach ($partidos as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($p['fecha']))) ?></td>
                                    <td><?= htmlspecialchars($p['jornada']) ?></td>
                                    <td><?= htmlspecialchars($p['equipo_local'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($p['equipo_visitante'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($p['categoria_nombre'] ?? '-') ?></td>
                                    <td><?= number_format($p['importe'], 2, ',', '.') ?></td>
                                    <td><?= number_format($p['importe_desplazamiento'], 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>partidos/editar/<?= $p['id'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger"
                                                data-toggle="modal"
                                                data-target="#modalEliminar"
                                                data-id="<?= $p['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($p['equipo_local'] . ' vs ' . $p['equipo_visitante']) ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">
                                    No hay partidos registrados en esta temporada.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </section>
</div> <!-- 🚪 FIN WRAPPER -->

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<!-- 📊 DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- 🚫 Evitar error de columnas -->
<script>
    $(document).ready(function() {
        var tabla = $('#tablaPartidos');
        if (tabla.find('tbody tr').length > 0) {
            tabla.DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                pageLength: 10,
                lengthMenu: [5, 10, 20, 50],
                order: [[0, "desc"]],
                columnDefs: [{ orderable: false, targets: 7 }]
            });
        }
    });
</script>

<!-- 🧱 MODAL DE CONFIRMACIÓN RIOJA -->
<style>
    .modal-header.bg-rioja {
        background: linear-gradient(90deg, #6e0b14 0%, #9c1d2b 100%);
        color: #fff;
    }
</style>

<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-rioja">
                <h5 class="modal-title" id="modalEliminarLabel"><i class="fas fa-exclamation-triangle"></i> Confirmar eliminación</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas eliminar el partido <strong id="nombreEliminar"></strong>?</p>
                <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnConfirmarEliminar" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#modalEliminar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nombre = button.data('nombre');
        var modal = $(this);
        modal.find('#nombreEliminar').text(nombre);
        modal.find('#btnConfirmarEliminar').attr('href', '<?= BASE_URL ?>partidos/eliminar/' + id);
    });
</script>
