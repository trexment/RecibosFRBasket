<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
<?php require_once __DIR__ . '/../partials/alerts.php'; ?>

<?php
// Función para mostrar el número con formato 622 63 47 90
function formatearTelefono($numero) {
    $num = preg_replace('/\D/', '', $numero);
    return preg_replace('/^(\d{3})(\d{2})(\d{2})(\d{2})$/', '$1 $2 $3 $4', $num);
}
?>


<div class="content-wrapper">
    <section class="content-header">
        <div class="card shadow-sm border-0">
            <div class="card-header header-rioja">
                <h3 class="card-title mb-0">
                    <i class="fas fa-user-edit"></i> Editar Árbitro
                </h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre"
                                   value="<?= htmlspecialchars($arbitro['nombre']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email (opcional)</label>
                            <input type="email" class="form-control" name="email" id="email"
                                   value="<?= htmlspecialchars($arbitro['email']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="telefono"
                                   value="<?= htmlspecialchars(formatearTelefono($arbitro['telefono'])) ?>"
                                   maxlength="12" inputmode="numeric" pattern="[0-9\s]+"
                                   placeholder="622 63 47 90">
                            <small class="text-muted">Formato: 622 63 47 90</small>
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="activo" id="activo"
                                        <?= ($arbitro['activo'] === 'SI') ? 'checked' : '' ?>>
                                <label for="activo" class="form-check-label fw-bold">Activo en federación</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                        <a href="<?= BASE_URL ?>arbitros" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const telInput = document.getElementById('telefono');
        if (!telInput) return;

        telInput.addEventListener('input', () => {
            // Elimina todo lo que no sea número
            let digits = telInput.value.replace(/\D/g, '');
            // Aplica formato 3-2-2-2
            if (digits.length > 3) digits = digits.replace(/^(\d{3})(\d{0,2})(\d{0,2})(\d{0,2}).*$/, '$1 $2 $3 $4');
            telInput.value = digits.trim();
        });
    });
</script>


<?php require_once __DIR__ . '/../partials/footer.php'; ?>
