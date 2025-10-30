<?php
/**
 * 📌 Cabecera Rioja reutilizable para módulos (cards AdminLTE)
 *
 * Parámetros esperados:
 *  - $titulo   → Título principal (ej: "Listado de partidos")
 *  - $icono    → Icono FontAwesome (ej: "fa-list", "fa-plus", "fa-edit")
 *  - $botonUrl → URL del botón (opcional)
 *  - $botonTxt → Texto del botón (opcional)
 *  - $botonIcono → Icono del botón (opcional)
 */

$titulo = $titulo ?? 'Sin título';
$icono = $icono ?? 'fa-folder';
$botonUrl = $botonUrl ?? '';
$botonTxt = $botonTxt ?? '';
$botonIcono = $botonIcono ?? 'fa-plus';
?>

<div class="card-header header-rioja">
    <h3 class="card-title mb-0">
        <i class="fas <?= htmlspecialchars($icono) ?>"></i> <?= htmlspecialchars($titulo) ?>
    </h3>

    <?php if (!empty($botonUrl) && !empty($botonTxt)): ?>
        <a href="<?= htmlspecialchars($botonUrl) ?>" class="btn btn-header">
            <i class="fas <?= htmlspecialchars($botonIcono) ?>"></i> <?= htmlspecialchars($botonTxt) ?>
        </a>
    <?php endif; ?>
</div>
