<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">

    <!-- 🧭 ENCABEZADO -->
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-2 mb-md-0 text-dark font-weight-bold">Equipos</h1>

            <div class="d-flex align-items-center gap-2">
                <!-- 📆 Selector de temporada -->
                <form method="GET" action="<?= BASE_URL ?>equipos" class="form-inline">
                    <select name="temporada_id" class="form-control mr-2" onchange="this.form.submit()">
                        <?php foreach ($temporadas as $temp): ?>
                            <option value="<?= htmlspecialchars($temp['id']) ?>"
                                    <?= ($temp['id'] == ($_GET['temporada_id'] ?? ($temporadaActiva['id'] ?? ''))) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($temp['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <!-- ➕ Nuevo equipo -->
                <a href="<?= BASE_URL ?>equipos/crear" class="btn btn-rioja">
                    <i class="fas fa-plus"></i> Nuevo equipo
                </a>
            </div>
        </div>
    </section>

    <!-- 📋 CONTENIDO PRINCIPAL -->
    <section class="content">
        <div class="container-fluid">

            <!-- 🔔 MENSAJES FLASH -->
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

            <!-- 🧾 TABLA DE EQUIPOS -->
            <div class="card shadow-sm mt-3 border-0">
                <div class="card-header text-white bg-rioja">
                    <h3 class="card-title mb-0"><i class="fas fa-users"></i> Listado de equipos</h3>
                </div>
                <div class="card-body table-responsive">

                    <table id="tablaEquipos" class="table table-bordered table-hover text-nowrap mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Temporada</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($equipos)): ?>
                            <?php foreach ($equipos as $eq): ?>
                                <tr>
                                    <td><?= htmlspecialchars($eq['nombre']) ?></td>
                                    <td><?= htmlspecialchars($eq['categoria_nombre'] ?? 'Sin categoría') ?></td>
                                    <td><?= htmlspecialchars($eq['temporada_nombre'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>equipos/editar/<?= $eq['id'] ?>" class="btn btn-sm btn-rioja-light" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-rioja-light"
                                                data-toggle="modal"
                                                data-target="#modalEliminar"
                                                data-id="<?= $eq['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($eq['nombre']) ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    No hay equipos registrados en esta temporada.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<!-- 📊 DATATABLES -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function () {
        const tabla = $('#tablaEquipos');

        // 💡 Evitar re-inicialización
        if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().clear().destroy();
        }

        // ✅ Verificar que las columnas coinciden
        const thCount = tabla.find('thead th').length;
        const tdCount = tabla.find('tbody tr:first td').length;

        if (thCount === tdCount || tabla.find('tbody tr').length === 1) {
            tabla.DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                pageLength: 10,
                lengthMenu: [5, 10, 20, 50],
                order: [[0, "asc"]],
                columnDefs: [{ orderable: false, targets: -1 }]
            });
        } else {
            console.warn(`⚠️ Columnas no coinciden: th=${thCount}, td=${tdCount}`);
        }
    });
</script>

<!-- 🧱 MODAL ELIMINAR -->
<style>
    .bg-rioja {
        background: linear-gradient(90deg, #6e0b14 0%, #9c1d2b 100%) !important;
        color: #fff !important;
    }
</style>

<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-rioja">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar eliminación</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas eliminar el equipo <strong id="nombreEliminar"></strong>?</p>
                <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnConfirmarEliminar" class="btn btn-rioja">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#modalEliminar').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        const nombre = button.data('nombre');
        const modal = $(this);
        modal.find('#nombreEliminar').text(nombre);
        modal.find('#btnConfirmarEliminar').attr('href', '<?= BASE_URL ?>equipos/eliminar/' + id);
    });
</script>
