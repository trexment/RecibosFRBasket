<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Datos del usuario
$usuario = $_SESSION['usuario'] ?? null;

// Determinar si es administrador (por id o rol)
$isAdmin = false;
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    $isAdmin = true;
} elseif (isset($usuario['id']) && intval($usuario['id']) === 1) {
    $isAdmin = true;
}
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= BASE_URL ?>dashboard" class="brand-link text-center">
        <span class="brand-text font-weight-light">Recibos Arbitrales</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>dashboard"
                       class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Temporadas -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>temporadas"
                       class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/temporadas') !== false) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Temporadas</p>
                    </a>
                </li>

                <!-- Categorías -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>categorias"
                       class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/categorias') !== false) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Categorías</p>
                    </a>
                </li>

                <!-- Precios -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>tarifas" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/tarifas') !== false) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Tarifas</p>
                    </a>
                </li>

                <!-- Precio del km -->
                <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>desplazamientos" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/desplazamientos') !== false) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-road"></i>
                            <p>Desplazamientos</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Equipos -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>equipos"
                       class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/equipos') !== false) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Equipos</p>
                    </a>
                </li>

                <!-- Partidos -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>partidos"
                       class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/partidos') !== false) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-futbol"></i>
                        <p>Partidos</p>
                    </a>
                </li>

                <!-- Árbitros -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>arbitros"
                       class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/arbitros') !== false) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-stopwatch"></i>
                        <p>Árbitros</p>
                    </a>
                </li>

                <!-- Recibos -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>recibos"
                       class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/recibos') !== false) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Recibos</p>
                    </a>
                </li>

                <!-- Solo administrador -->
                <?php if ($isAdmin): ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>invites"
                           class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/invites') !== false) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-plus"></i>
                            <p>Invitaciones</p>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </nav>

        <!-- Logo inferior mejorado -->
        <div class="sidebar-footer">
            <a href="https://www.frbaloncesto.com/" target="_blank" title="Federación Riojana de Baloncesto">
                <img src="<?= BASE_URL ?>img/Federacion_riojana_baloncesto.png" alt="Logo Federación Riojana de Baloncesto" class="img-fluid">
            </a>
            <p>Federación Riojana<br>de Baloncesto</p>
        </div>

    </div>
</aside>
