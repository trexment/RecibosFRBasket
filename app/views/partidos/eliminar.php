<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Eliminar partido</h1>
            <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-rioja text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-exclamation-triangle"></i> Confirmar eliminación</h3>
                </div>
                <div class="card-body">
                    <p class="mb-3">¿Seguro que deseas eliminar este partido?</p>
                    <form method="POST">
                        <input type="hidden" name="confirmar" value="si">
                        <button class="btn btn-rioja"><i class="fas fa-trash-alt"></i> Eliminar</button>
                        <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary ml-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
<style>.bg-rioja{background:linear-gradient(90deg,#6e0b14 0%,#9c1d2b 100%)!important;color:#fff!important}</style>
