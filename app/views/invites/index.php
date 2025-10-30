<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
<?php require_once __DIR__ . '/../partials/alerts.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-envelope"></i> Invitaciones</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Formulario de invitación -->
            <div class="card card-maroon">
                <div class="card-header header-rioja">
                    <h3 class="card-title"><i class="fas fa-paper-plane"></i> Enviar nueva invitación</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>invites/generar">
                        <div class="form-group">
                            <label for="email">Correo electrónico (opcional)</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   placeholder="ejemplo@correo.com">
                            <small class="text-muted">
                                Si introduces un correo, se enviará automáticamente la invitación.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Generar invitación
                        </button>
                    </form>
                </div>
            </div>

            <!-- Listado de invitaciones -->
            <div class="card mt-4">
                <div class="card-header bg-gradient-maroon text-white">
                    <i class="fas fa-list"></i> Invitaciones enviadas
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-center">
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Email</th>
                            <th>Enviado</th>
                            <th>Usado</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($invites)): ?>
                            <tr><td colspan="6" class="text-muted">No hay invitaciones.</td></tr>
                        <?php else: ?>
                            <?php foreach ($invites as $i): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($i['codigo']) ?></strong></td>
                                    <td><?= htmlspecialchars($i['email'] ?? '-') ?></td>
                                    <td><?= $i['email_enviado'] ? '✅' : '❌' ?></td>
                                    <td>
                                        <?php
                                        $expira = strtotime($i['expires_at']);
                                        if ($i['usado']) {
                                            echo "<span class='badge badge-success'>Usado</span>";
                                        } elseif ($expira < time()) {
                                            echo "<span class='badge badge-danger'>Caducado</span>";
                                        } else {
                                            echo "<span class='badge badge-warning'>Activo</span>";
                                        }
                                        ?>
                                    </td>

                                    <td><?= htmlspecialchars($i['nombre_usuario'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($i['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
