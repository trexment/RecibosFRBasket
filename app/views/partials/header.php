<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>img/silbato.ico">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Recibos Arbitrales' ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" />
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $usuario = $_SESSION['usuario'] ?? null;
    $nombreUsuario = $_SESSION['nombre'] ?? ($usuario['nombre'] ?? 'Invitado');
    $rolUsuario = $_SESSION['rol'] ?? 'usuario';
    ?>

    <!-- ÚNICO NAVBAR SUPERIOR -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom shadow-sm">
        <!-- Botón de menú lateral -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= BASE_URL ?>dashboard" class="nav-link">Inicio</a>
            </li>
        </ul>

        <!-- Menú de usuario -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="fas fa-user-circle fa-lg text-primary"></i>
                    <span class="ml-2 font-weight-bold"><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></span>
                    <i class="fas fa-caret-down ml-1"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow-lg">
                <span class="dropdown-item-text text-muted">
                    <i class="fas fa-user-shield mr-1"></i>
                    <?= ucfirst(htmlspecialchars($rolUsuario, ENT_QUOTES, 'UTF-8')) ?>
                </span>
                    <div class="dropdown-divider"></div>

                    <?php if (!empty($_SESSION['usuario_id'])): ?>
                        <a href="<?= BASE_URL ?>perfil" class="dropdown-item">
                            <i class="fas fa-id-card-alt mr-2 text-primary"></i> Mi perfil
                        </a>
                        <a href="<?= BASE_URL ?>perfil/cambiarPassword" class="dropdown-item">
                            <i class="fas fa-key mr-2 text-warning"></i> Cambiar contraseña
                        </a>
                        <div class="dropdown-divider"></div>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>login/logout" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /NAVBAR -->
