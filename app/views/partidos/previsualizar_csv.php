<<?php
$preview = $_SESSION['csv_preview'] ?? [];
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Previsualización CSV</h1>
            <a href="<?= BASE_URL ?>partidos/importar_csv" class="btn btn-secondary">Volver</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (empty($preview)): ?>
                <div class="alert alert-warning">No hay filas válidas en el CSV.</div>
            <?php else: ?>
                <form method="post" action="<?= BASE_URL ?>partidos/confirmar_csv">
                    <div class="card">
                        <div class="card-header"><strong>Revisa y corrige antes de importar</strong></div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Jornada</th>
                                    <th>Categoría</th>
                                    <th>Equipo Local</th>
                                    <th>Equipo Visitante</th>
                                    <th>Rol</th>
                                    <th>Tablet</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($preview as $i => $p): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="rows[]" value="<?= $i ?>" checked>
                                        </td>
                                        <td style="min-width:120px;">
                                            <input type="date" name="fecha[<?= $i ?>]" class="form-control"
                                                   value="<?= htmlspecialchars($p['fecha']) ?>">
                                            <small class="text-muted"><?= htmlspecialchars($p['fecha_raw']) ?></small>
                                        </td>
                                        <td style="max-width:100px;">
                                            <input type="number" name="jornada[<?= $i ?>]" class="form-control"
                                                   value="<?= htmlspecialchars($p['jornada']) ?>">
                                        </td>
                                        <td style="min-width:180px;">
                                            <input type="text" name="categoria[<?= $i ?>]" class="form-control"
                                                   value="<?= htmlspecialchars($p['categoria']) ?>">
                                        </td>
                                        <td style="min-width:240px;">
                                            <div class="input-group">
                                                <input type="text" name="equipo_local[<?= $i ?>]" class="form-control equipo-autocomplete"
                                                       data-target="local_<?= $i ?>"
                                                       value="<?= htmlspecialchars($p['equipo_local']) ?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary btn-sugerir" data-input="local_<?= $i ?>" type="button">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="suggestions" id="sugg_local_<?= $i ?>"></div>
                                        </td>
                                        <td style="min-width:240px;">
                                            <div class="input-group">
                                                <input type="text" name="equipo_visitante[<?= $i ?>]" class="form-control equipo-autocomplete"
                                                       data-target="visit_<?= $i ?>"
                                                       value="<?= htmlspecialchars($p['equipo_visitante']) ?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary btn-sugerir" data-input="visit_<?= $i ?>" type="button">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="suggestions" id="sugg_visit_<?= $i ?>"></div>
                                        </td>
                                        <td style="min-width:150px;">
                                            <select name="rol[<?= $i ?>]" class="form-control rol-select">
                                                <?php
                                                $roles = ['arbitro'=>'Árbitro','oficial'=>'Oficial','arbitro_solo'=>'Árbitro (solo)','oficial_solo'=>'Oficial (solo)'];
                                                $rolSel = $p['rol'] ?: 'oficial';
                                                foreach ($roles as $val=>$txt):
                                                    ?>
                                                    <option value="<?= $val ?>" <?= $val === $rolSel ? 'selected':'' ?>><?= $txt ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="checkbox" name="tablet[<?= $i ?>]" value="1" <?= ($p['tablet'] ? 'checked' : '') ?> class="chk-tablet">
                                            <small class="text-muted d-block">Solo cuenta si “Oficial”</small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-info btn-normalizar" data-row="<?= $i ?>">Normalizar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-success">
                                <i class="fas fa-check"></i> Importar partidos seleccionados
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    // Evitar tablet si rol != oficial/oficial_solo
    document.querySelectorAll('.rol-select').forEach(sel=>{
        sel.addEventListener('change', e=>{
            const tr = e.target.closest('tr');
            const chk = tr.querySelector('.chk-tablet');
            const v = e.target.value;
            if (v.startsWith('arbitro')) { chk.checked=false; chk.disabled=true; }
            else { chk.disabled=false; }
        });
    });

    // Botón Normalizar: corrige espacios/puntos comunes (CB -> C.B., etc. si quieres añadir reglas)
    document.querySelectorAll('.btn-normalizar').forEach(btn=>{
        btn.addEventListener('click', ()=>{
            const tr = btn.closest('tr');
            const campos = ['input[name^="equipo_local"]', 'input[name^="equipo_visitante"]','input[name^="categoria"]'];
            campos.forEach(sel=>{
                const el = tr.querySelector(sel);
                if (!el) return;
                let v = el.value.trim().replace(/\s+/g,' ');
                // Normalizaciones mínimas
                v = v.replace(/c\.b\.?/ig,'C.B.');
                v = v.replace(/sd\s+|s\.d\./ig,'S.D.');
                el.value = v;
            });
        });
    });

    // Sugerencias AJAX
    document.querySelectorAll('.btn-sugerir').forEach(btn=>{
        btn.addEventListener('click', async ()=>{
            const inp = btn.closest('.input-group').querySelector('input');
            const q = inp.value.trim();
            const suggBox = btn.closest('td').querySelector('.suggestions');
            suggBox.innerHTML = 'Buscando...';
            try {
                const res = await fetch('<?= BASE_URL ?>partidos/sugerir_equipos?q=' + encodeURIComponent(q));
                const data = await res.json();
                if (!data.length) { suggBox.innerHTML = '<small class="text-muted">Sin coincidencias</small>'; return; }
                const ul = document.createElement('ul');
                ul.className = 'list-unstyled mb-0';
                data.forEach(it=>{
                    const li = document.createElement('li');
                    li.innerHTML = '<button type="button" class="btn btn-sm btn-light mb-1">'+ it.nombre +'</button>';
                    li.querySelector('button').addEventListener('click', ()=>{
                        inp.value = it.nombre;
                        suggBox.innerHTML = '';
                    });
                    ul.appendChild(li);
                });
                suggBox.innerHTML = '';
                suggBox.appendChild(ul);
            } catch(e) {
                suggBox.innerHTML = '<small class="text-danger">Error al sugerir</small>';
            }
        });
    });
</script>

<style>
    .suggestions { max-height: 180px; overflow:auto; background:#1114; border:1px solid #6663; border-radius:8px; padding:6px; margin-top:6px; }
    .suggestions button { width:100%; text-align:left; }
</style>

<?php require __DIR__ . '/../partials/footer.php'; ?>
