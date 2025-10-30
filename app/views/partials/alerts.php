<?php
/**
 * 🔔 Sistema de alertas Rioja unificado
 * Compatible con:
 *  - $_SESSION['flash_success']
 *  - $_SESSION['flash_error']
 *  - $_SESSION['flash_warning']
 *  - $_SESSION['flash_info']
 *  - $_SESSION['alert'] (array tipo antiguo con ['type' => 'danger', 'msg' => '...'])
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener alertas en cualquier formato posible
$alertas = [];

$map = [
        'flash_success' => 'success',
        'flash_error'   => 'danger',
        'flash_warning' => 'warning',
        'flash_info'    => 'info'
];

foreach ($map as $key => $tipo) {
    if (!empty($_SESSION[$key])) {
        $alertas[] = ['tipo' => $tipo, 'mensaje' => $_SESSION[$key]];
        unset($_SESSION[$key]);
    }
}

// Compatibilidad con versión antigua
if (!empty($_SESSION['alert'])) {
    $type = $_SESSION['alert']['type'] ?? 'info';
    $msg  = $_SESSION['alert']['msg'] ?? '';
    $alertas[] = ['tipo' => $type, 'mensaje' => $msg];
    unset($_SESSION['alert']);
}
?>

<?php if (!empty($alertas)): ?>
    <div class="alert-container mt-3">
    <?php foreach ($alertas as $a): ?>
            <div class="alert alert-<?= htmlspecialchars($a['tipo']) ?> alert-animado alert-dismissible fade show shadow-sm mb-3" role="alert">
                <?php if ($a['tipo'] === 'success'): ?>
                    <i class="fas fa-check-circle me-2"></i>
                <?php elseif ($a['tipo'] === 'danger'): ?>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                <?php elseif ($a['tipo'] === 'warning'): ?>
                    <i class="fas fa-exclamation-circle me-2"></i>
                <?php elseif ($a['tipo'] === 'info'): ?>
                    <i class="fas fa-info-circle me-2"></i>
                <?php endif; ?>

                <?= html_entity_decode($a['mensaje'], ENT_QUOTES, 'UTF-8') ?>


                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // 🔄 Cierre automático de las alertas después de unos segundos
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(el => {
                    el.classList.remove('show');
                    el.classList.add('fade');
                    setTimeout(() => el.remove(), 500);
                });
            }, 5000); // 5 segundos
        });
    </script>
<?php endif; ?>
