<?php
// Mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rutas base / config
require_once __DIR__ . '/../app/config/config.php';

// Cargar rutas amigables
$url = $_GET['url'] ?? 'login';
$url = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));

$controllerName = ucfirst($url[0]) . 'Controller';
$method         = $url[1] ?? 'index';
$params         = array_slice($url, 2);

$controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

// ------------------------------
// Bypasses específicos (opcionales)
// ------------------------------
// Estos atajos invocan directamente ciertos métodos (útil para endpoints AJAX/POST que devuelven JSON/PDF)
if ($controllerName === 'PartidosController') {

    // === AJAX tarifas / utilidades ya existentes ===
    if (in_array($method, ['calcularImporte','obtenerTarifa','getCategoriaNombre','getEquiposPorCategoria'], true)) {
        require_once __DIR__ . '/../app/controllers/PartidosController.php';
        $controller = new PartidosController();
        call_user_func_array([$controller, $method], $params);
        exit;
    }

    // === NUEVOS endpoints CSV/PDF/AJAX sugerencias (opcionalmente por bypass) ===
    $bypassMethods = [
        // CSV
        'importar_csv',          // GET (form) / POST (analizar)
        'confirmar_csv',         // POST
        // PDF
        'importar_pdf',          // GET (form)
        'procesar_importacion_pdf', // POST (analizar)
        'confirmar_importacion_pdf_corregida', // POST (guardar tras corrección)
        // Sugerencias AJAX
        'sugerir_equipos'        // GET -> JSON
    ];

    if (in_array($method, $bypassMethods, true)) {
        require_once __DIR__ . '/../app/controllers/PartidosController.php';
        $controller = new PartidosController();
        call_user_func_array([$controller, $method], $params);
        exit;
    }
}

// ------------------------------
// Despacho genérico MVC
// ------------------------------
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        } else {
            echo "Método '$method' no encontrado en $controllerName.";
        }
    } else {
        echo "Clase $controllerName no encontrada.";
    }
} else {
    echo "Controlador '$controllerName' no encontrado.";
}
