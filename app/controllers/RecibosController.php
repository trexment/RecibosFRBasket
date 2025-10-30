<?php
class RecibosController
{
    private $db;
    private $usuario;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../helpers/session_guard.php';
        require_once __DIR__ . '/../views/partials/alerts.php';
        require_once __DIR__ . '/../helpers/session_guard.php';
        $usuario = SessionGuard::user(); // devuelve el array completo


        SessionGuard::check(); // exige login

        $this->db = Database::getInstance();
        $this->usuario = $_SESSION['usuario'] ?? null;

        if (!$this->usuario || empty($this->usuario['id'])) {
            $_SESSION['flash_error'] = "Sesión no válida. Por favor, inicia sesión de nuevo.";
            header("Location: " . BASE_URL . "login");
            exit;
        }
    }

    /**
     * 📋 Buscar partidos sin importes (vista previa)
     */
    public function index()
    {
        require_once __DIR__ . '/../helpers/session_guard.php';
        $usuario = SessionGuard::user();

        if (!$usuario) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        require_once __DIR__ . '/../core/Database.php';
        $db = Database::getInstance();

        $desde     = $_GET['desde']     ?? '';
        $hasta     = $_GET['hasta']     ?? '';
        $retencion = $_GET['retencion'] ?? '';

        $partidos = [];

        // Si el usuario ha indicado fechas, buscar partidos
        if (!empty($desde) && !empty($hasta)) {
            $stmt = $db->prepare("
            SELECT 
                p.*, 
                el.nombre AS equipo_local, 
                ev.nombre AS equipo_visitante, 
                c.nombre AS categoria
            FROM partidos p
            JOIN equipos el ON el.id = p.equipo_local_id
            JOIN equipos ev ON ev.id = p.equipo_visitante_id
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.usuario_id = :uid 
              AND p.fecha BETWEEN :desde AND :hasta
            ORDER BY p.fecha ASC
        ");
            $stmt->execute([
                ':uid'   => $usuario['id'],
                ':desde' => $desde,
                ':hasta' => $hasta
            ]);
            $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        require_once __DIR__ . '/../views/recibos/index.php';
    }


    /**
     * 🧾 Exportar recibo PDF de partidos o desplazamientos
     */
    public function generar()
    {
        require_once __DIR__ . '/../helpers/session_guard.php';
        $usuario = SessionGuard::user(); // garantiza que devuelve el usuario logueado

        // Si no hay sesión válida
        if (!$usuario) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        // Recoger datos del formulario
        $tipo       = $_POST['tipo']       ?? 'partido';
        $desde      = $_POST['desde']      ?? null;
        $hasta      = $_POST['hasta']      ?? null;
        $retencion  = $_POST['retencion']  ?? null;

        // Validar fechas
        if (empty($desde) || empty($hasta)) {
            $_SESSION['error'] = "Debe indicar un rango de fechas.";
            header("Location: " . BASE_URL . "recibos");
            exit;
        }

        // Validar retención solo si es tipo partido
        if ($tipo === 'partido' && ($retencion === null || $retencion === '')) {
            $_SESSION['error'] = "Debe indicar una retención para generar el recibo de partidos.";
            header("Location: " . BASE_URL . "recibos");
            exit;
        }

        // Cargar base de datos
        require_once __DIR__ . '/../core/Database.php';
        $db = Database::getInstance();

        // Consultar partidos del usuario en el rango indicado
        $stmt = $db->prepare("
        SELECT 
            p.*, 
            el.nombre AS equipo_local, 
            ev.nombre AS equipo_visitante, 
            c.nombre AS categoria
        FROM partidos p
        JOIN equipos el ON el.id = p.equipo_local_id
        JOIN equipos ev ON ev.id = p.equipo_visitante_id
        LEFT JOIN categorias c ON c.id = p.categoria_id
        WHERE p.usuario_id = :uid 
          AND p.fecha BETWEEN :desde AND :hasta
        ORDER BY p.fecha ASC
    ");
        $stmt->execute([
            ':uid'   => $usuario['id'],
            ':desde' => $desde,
            ':hasta' => $hasta
        ]);
        $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si no hay partidos
        if (empty($partidos)) {
            $_SESSION['error'] = "No se encontraron partidos en el rango indicado.";
            header("Location: " . BASE_URL . "recibos");
            exit;
        }

        // 🔥 Limpiar cualquier salida previa antes de generar el PDF
        if (ob_get_length()) {
            ob_end_clean();
        }

        // === Generar PDF según el tipo ===
        if ($tipo === 'desplazamiento') {
            require_once __DIR__ . '/../pdf/recibo_desplazamiento.php';
            generarReciboDesplazamientoPDF($partidos, $usuario, $desde, $hasta);
        } else {
            require_once __DIR__ . '/../pdf/recibo_pdf.php';
            generarReciboPDF($partidos, $usuario, $retencion, $desde, $hasta);
        }

        exit;
    }

}
