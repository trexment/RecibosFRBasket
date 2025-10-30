<?php
/**
 * 📌 Pie de card Rioja reutilizable
 *
 * Parámetros esperados:
 *  - $btnGuardar (bool) → Mostrar botón de guardar (true por defecto)
 *  - $btnTexto (string) → Texto del botón principal (por defecto: "Guardar cambios")
 *  - $btnCancelar (bool) → Mostrar botón de cancelar (true por defecto)
 *  - $cancelarUrl (string) → URL a donde redirige "Cancelar"
 *  - $btnEliminar (bool) → Mostrar botón rojo de eliminar (opcional)
 *  - $eliminarTexto (string) → Texto del botón rojo (por defecto: "Eliminar")
 */

$btnGuardar = $btnGuardar ?? true;
$btnTexto = $btnTexto ?? 'Guardar cambios';
$btnCancelar = $btnCancelar ?? true;
$cancelarUrl = $cancelarUrl ?? BASE_URL;
$btnEliminar = $btnEliminar ?? false;
$eliminarTexto = $eliminarTexto ?? 'Eliminar';
?>

<div class="card-footer d-flex justify-content-between align-items-center bg-light border-top">
    <div>
        <?php if ($btnGuardar): ?>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?= htmlspecialchars($btnTexto) ?>
            </button>
        <?php endif; ?>

        <?php if ($btnEliminar): ?>
            <button type="submit" name="confirmar" value="si" class="btn btn-danger ms-2">
                <i class="fas fa-trash"></i> <?= htmlspecialchars($eliminarTexto) ?>
            </button>
        <?php endif; ?>
    </div>

    <?php if ($btnCancelar): ?>
        <a href="<?= htmlspecialchars($cancelarUrl) ?>" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cancelar
        </a>
    <?php endif; ?>
</div>
