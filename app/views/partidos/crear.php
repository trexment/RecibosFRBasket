<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fas fa-futbol"></i> Crear Partido</h1>
    </section>

    <section class="content">
        <div class="card shadow-sm border-0">
            <div class="card-header header-rioja">
                <h3 class="card-title mb-0">
                    <i class="fas fa-plus-circle"></i> Nuevo Partido
                </h3>
            </div>

            <!-- ⚠️ Aviso si existe duplicado -->
            <?php if (isset($error_duplicado)): ?>
                <div class="alert alert-danger alert-animado m-3 shadow-sm" style="border-left: 5px solid #7b1113;">

                <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error_duplicado) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="p-4">
                <div class="row g-3">

                    <!-- Fecha -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fecha</label>
                        <input type="date" name="fecha" id="fecha" class="form-control"
                               value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>" required>
                    </div>

                    <!-- Jornada -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Jornada</label>
                        <input type="text" name="jornada" id="jornada" class="form-control"
                               placeholder="Ej. 4"
                               value="<?= htmlspecialchars($_POST['jornada'] ?? '') ?>" required>
                    </div>

                    <!-- Equipo Local -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Equipo Local</label>
                        <select name="equipo_local_id" id="equipo_local_id" class="form-control" required>
                            <option value="">-- Selecciona equipo local --</option>
                            <?php foreach ($equipos as $equipo): ?>
                                <option value="<?= $equipo['id']; ?>"
                                        data-categoria="<?= $equipo['categoria_id']; ?>"
                                        <?= (isset($_POST['equipo_local_id']) && $_POST['equipo_local_id'] == $equipo['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($equipo['nombre_mostrado']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Equipo Visitante -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Equipo Visitante</label>
                        <select name="equipo_visitante_id" id="equipo_visitante_id" class="form-control" required>
                            <option value="">-- Selecciona equipo visitante --</option>
                            <?php if (!empty($_POST['equipo_visitante_id'])): ?>
                                <?php foreach ($equipos as $eq): ?>
                                    <?php if ($eq['id'] != ($_POST['equipo_local_id'] ?? 0)): ?>
                                        <option value="<?= $eq['id']; ?>"
                                                <?= ($_POST['equipo_visitante_id'] == $eq['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($eq['nombre']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Categoría -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Categoría</label>
                        <input type="text" id="categoria_nombre" class="form-control bg-light"
                               value="<?= htmlspecialchars($_POST['categoria_nombre'] ?? '') ?>" readonly>
                        <input type="hidden" name="categoria_id" id="categoria_id"
                               value="<?= htmlspecialchars($_POST['categoria_id'] ?? '') ?>">
                    </div>

                    <!-- Importe -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Importe (€)</label>
                        <input type="text" name="importe" id="importe" class="form-control bg-light"
                               value="<?= htmlspecialchars($_POST['importe'] ?? '') ?>" readonly>
                    </div>

                    <!-- Rol -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Rol</label>
                        <select name="rol" id="rol" class="form-control" required>
                            <option value="">-- Selecciona rol --</option>
                            <option value="arbitro" <?= (($_POST['rol'] ?? '') === 'arbitro') ? 'selected' : '' ?>>Árbitro</option>
                            <option value="arbitro_solo" <?= (($_POST['rol'] ?? '') === 'arbitro_solo') ? 'selected' : '' ?>>Árbitro (solo)</option>
                            <option value="oficial" <?= (($_POST['rol'] ?? '') === 'oficial') ? 'selected' : '' ?>>Oficial</option>
                            <option value="oficial_solo" <?= (($_POST['rol'] ?? '') === 'oficial_solo') ? 'selected' : '' ?>>Oficial (solo)</option>
                        </select>
                    </div>

                    <!-- Kilómetros -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Kilómetros</label>
                        <input type="number" step="0.01" name="km" id="km" class="form-control"
                               placeholder="Ej. 12.5"
                               value="<?= htmlspecialchars($_POST['km'] ?? '') ?>">
                    </div>

                    <!-- Dieta -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Dieta (€)</label>
                        <input type="number" step="0.01" name="dieta" id="dieta" class="form-control"
                               placeholder="Ej. 5.00"
                               value="<?= htmlspecialchars($_POST['dieta'] ?? '') ?>">
                    </div>

                    <!-- Desplazamiento -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Desplazamiento (€)</label>
                        <input type="number" step="0.01" name="importe_desplazamiento" id="importe_desplazamiento"
                               class="form-control"
                               value="<?= htmlspecialchars($_POST['importe_desplazamiento'] ?? '') ?>">
                    </div>

                    <!-- Usa tablet -->
                    <div class="col-md-12 mt-2">
                        <div class="form-check">
                            <input type="checkbox" name="usa_tablet" id="usa_tablet" class="form-check-input"
                                    <?= !empty($_POST['usa_tablet']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="usa_tablet">Usa tablet (+1 € para oficiales)</label>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="<?= BASE_URL ?>partidos" class="btn btn-secondary px-4">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- ============================ -->
<!-- 📜 JavaScript funcional -->
<!-- ============================ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {

        // ================================
        // 📌 Al cambiar el equipo local:
        // obtiene la categoría y los equipos visitantes
        // ================================
        function cargarCategoriaYVisitantes() {
            const localId = $('#equipo_local_id').val();
            if (!localId) {
                $('#categoria_id').val('');
                $('#categoria_nombre').val('');
                $('#equipo_visitante_id').html('<option value="">-- Selecciona equipo visitante --</option>');
                return;
            }

            // Tomamos el ID de categoría desde el atributo data-categoria del option
            const categoriaId = $('#equipo_local_id option:selected').data('categoria');
            $('#categoria_id').val(categoriaId || '');

            // Obtener el nombre de la categoría
            if (categoriaId) {
                $.post('<?= BASE_URL ?>partidos/getCategoriaNombre',
                    { categoria_id: categoriaId },
                    function (nombre) {
                        $('#categoria_nombre').val(nombre || '');
                    }
                );

                // Cargar equipos visitantes de la misma categoría
                $.post('<?= BASE_URL ?>partidos/getEquiposPorCategoria',
                    { categoria_id: categoriaId, equipo_local_id: localId },
                    function (html) {
                        $('#equipo_visitante_id').html(html);
                    }
                );
            } else {
                $('#categoria_nombre').val('');
                $('#equipo_visitante_id').html('<option value="">-- Selecciona equipo visitante --</option>');
            }
        }

        // Ejecutar al cambiar equipo local
        $('#equipo_local_id').on('change', cargarCategoriaYVisitantes);


        // ================================
        // 💰 Al cambiar el rol → actualizar importe
        // ================================
        $('#rol').on('change', function () {
            const rol = $(this).val();
            const categoria_id = $('#categoria_id').val();
            const temporada_id = <?= json_encode($temporada_activa['id'] ?? null) ?>;

            if (!rol || !categoria_id || !temporada_id) {
                $('#importe').val('0.00');
                return;
            }

            $.post('<?= BASE_URL ?>partidos/obtenerTarifa',
                { categoria_id, rol, temporada_id },
                function (res) {
                    try {
                        const data = JSON.parse(res);
                        const imp = (data && data.importe) ? parseFloat(data.importe) : 0;
                        $('#importe').val(imp.toFixed(2));
                    } catch (e) {
                        $('#importe').val('0.00');
                    }
                }
            );
        });


        // ================================
        // 🚗 Sincronizar km ↔ desplazamiento
        // ================================
        const precioKm = <?= json_encode($precio_km ?? 0.26) ?>;

        $('#km').on('input', function () {
            const km = parseFloat(this.value) || 0;
            if (km > 0) $('#importe_desplazamiento').val((km * precioKm).toFixed(2));
        });

        $('#importe_desplazamiento').on('input', function () {
            const desp = parseFloat(this.value) || 0;
            if (desp > 0) $('#km').val((desp / precioKm).toFixed(2));
        });

        // ================================
        // ♻️ Si ya hay un equipo seleccionado (postback)
        // ================================
        if ($('#equipo_local_id').val()) {
            cargarCategoriaYVisitantes();
        }

    });
</script>




<?php require_once __DIR__ . '/../partials/footer.php'; ?>
