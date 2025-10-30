<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-2 mb-md-0 text-dark font-weight-bold">Categorías</h1>

            <a href="<?= BASE_URL ?>categorias/crear" class="btn btn-rioja">
                <i class="fas fa-plus"></i> Nueva categoría
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash messages -->
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

            <!-- Tabla -->
            <div class="card shadow-sm mt-3 border-0">
                <div class="card-header text-white bg-rioja">
                    <h3 class="card-title mb-0"><i class="fas fa-layer-group"></i> Listado de categorías</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="tablaCategorias" class="table table-bordered table-hover text-nowrap mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Abreviatura</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $cat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cat['nombre']) ?></td>
                                    <td><?= htmlspecialchars($cat['abreviatura'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>categorias/editar/<?= $cat['id'] ?>" class="btn btn-sm btn-rioja-light" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-rioja-light"
                                                data-toggle="modal"
                                                data-target="#modalEliminar"
                                                data-id="<?= $cat['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($cat['nombre']) ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    No hay categorías registradas.
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

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function () {
        const tabla = $('#tablaCategorias');

        if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().clear().destroy();
        }

        const thCount = tabla.find('thead th').length;
        const tdCount = tabla.find('tbody tr:first td').length;

        if (thCount === tdCount || tabla.find('tbody tr').length === 1) {
            tabla.DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                pageLength: 15,
                lengthMenu: [10, 15, 20, 50],
                order: [[0, "asc"]],
                columnDefs: [{ orderable: false, targets: -1 }]
            });
        }
    });
</script>

<!-- Modal eliminar -->
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
                <p>¿Seguro que deseas eliminar la categoría <strong id="nombreEliminar"></strong>?</p>
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
        modal.find('#btnConfirmarEliminar').attr('href', '<?= BASE_URL ?>categorias/eliminar/' + id);
    });
</script>
