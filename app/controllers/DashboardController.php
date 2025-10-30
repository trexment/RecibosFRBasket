<?php
class DashboardController
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../helpers/session_guard.php';
        require_once __DIR__ . '/../views/partials/alerts.php';

        SessionGuard::check();
        $this->db = Database::getInstance();
    }

    public function index()
    {
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        // Temporada activa
        $tmp = $this->db->query("SELECT id, nombre FROM temporadas WHERE activa=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $temporadaId = $tmp['id'] ?? null;
        $temporadaNombre = $tmp['nombre'] ?? '—';

        // ============================
        // 📊 PARTIDOS POR CATEGORÍA
        // ============================
        $sql1 = "SELECT c.nombre AS categoria, COUNT(*) AS cantidad
                 FROM partidos p
                 LEFT JOIN categorias c ON c.id = p.categoria_id
                 WHERE p.usuario_id = :u";
        $params = [':u' => $usuarioId];
        if ($temporadaId) {
            $sql1 .= " AND p.temporada_id = :t";
            $params[':t'] = $temporadaId;
        }
        $sql1 .= " GROUP BY c.nombre ORDER BY cantidad DESC";
        $st1 = $this->db->prepare($sql1);
        $st1->execute($params);
        $partidosPorCategoria = $st1->fetchAll(PDO::FETCH_ASSOC);

        // ============================
        // 💶 INGRESOS MENSUALES
        // ============================
        $sql2 = "SELECT DATE_FORMAT(p.fecha, '%Y-%m') AS ym,
                        ROUND(SUM(COALESCE(p.importe,0) + COALESCE(p.importe_desplazamiento,0) + COALESCE(p.dieta,0)), 2) AS total
                 FROM partidos p
                 WHERE p.usuario_id = :u";
        if ($temporadaId) $sql2 .= " AND p.temporada_id = :t";
        $sql2 .= " GROUP BY ym ORDER BY ym ASC";

        $st2 = $this->db->prepare($sql2);
        $st2->execute($params);
        $ingresosMensuales = $st2->fetchAll(PDO::FETCH_ASSOC);

        // Variables esperadas por la vista
        $title = 'Dashboard';
        $temporada_activa = $temporadaNombre;

        // Evita errores si están vacíos
        $partidosPorCategoria = $partidosPorCategoria ?? [];
        $ingresosMensuales = $ingresosMensuales ?? [];

        require_once __DIR__ . '/../views/dashboard.php';
    }
}
