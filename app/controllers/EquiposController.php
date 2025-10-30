<?php
class EquiposController
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../helpers/session_guard.php';
        require_once __DIR__ . '/../views/partials/alerts.php';

        $this->db = Database::getInstance();
    }

    /**
     * 📋 Mostrar listado de equipos (filtrado correctamente por temporada)
     */
    public function index()
    {
        // Temporada activa
        $stmt = $this->db->query("SELECT * FROM temporadas WHERE activa = 1 LIMIT 1");
        $temporadaActiva = $stmt->fetch(PDO::FETCH_ASSOC);

        // ID de temporada seleccionada (GET o activa)
        $temporadaSeleccionada = isset($_GET['temporada_id']) ? intval($_GET['temporada_id']) : ($temporadaActiva['id'] ?? null);

        // Cargar todas las temporadas (para el selector)
        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Si hay temporada seleccionada, filtrar por ella
        if ($temporadaSeleccionada) {
            $stmt = $this->db->prepare("
                SELECT e.*, 
                       c.nombre AS categoria_nombre,
                       t.nombre AS temporada_nombre
                FROM equipos e
                LEFT JOIN categorias c ON e.categoria_id = c.id
                LEFT JOIN temporadas t ON e.temporada_id = t.id
                WHERE e.temporada_id = :temporada_id
                ORDER BY e.nombre ASC
            ");
            $stmt->execute([':temporada_id' => $temporadaSeleccionada]);
        } else {
            // Sin temporada activa → mostrar todos
            $stmt = $this->db->query("
                SELECT e.*, 
                       c.nombre AS categoria_nombre,
                       t.nombre AS temporada_nombre
                FROM equipos e
                LEFT JOIN categorias c ON e.categoria_id = c.id
                LEFT JOIN temporadas t ON e.temporada_id = t.id
                ORDER BY e.nombre ASC
            ");
        }

        $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/equipos/index.php';
    }

    /**
     * ➕ Crear nuevo equipo (asignado a temporada activa)
     */
    public function crear()
    {
        $temporada = $this->db->query("SELECT * FROM temporadas WHERE activa = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $categorias = $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $categoria_id = intval($_POST['categoria_id'] ?? 0);

            if (!$temporada) {
                $_SESSION['flash_error'] = "No hay una temporada activa para asignar al equipo.";
                header("Location: " . BASE_URL . "equipos");
                exit;
            }

            if (!empty($nombre) && $categoria_id > 0) {
                $stmt = $this->db->prepare("
                    INSERT INTO equipos (nombre, categoria_id, temporada_id)
                    VALUES (:nombre, :categoria_id, :temporada_id)
                ");
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':categoria_id' => $categoria_id,
                    ':temporada_id' => $temporada['id']
                ]);

                $_SESSION['flash_success'] = "Equipo creado correctamente.";
                header("Location: " . BASE_URL . "equipos?temporada_id=" . $temporada['id']);
                exit;
            } else {
                $_SESSION['flash_error'] = "Debes indicar el nombre y la categoría.";
            }
        }

        require_once __DIR__ . '/../views/equipos/crear.php';
    }

    /**
     * ✏️ Editar equipo
     */
    public function editar($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM equipos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$equipo) {
            $_SESSION['flash_error'] = "El equipo no existe.";
            header("Location: " . BASE_URL . "equipos");
            exit;
        }

        $categorias = $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $categoria_id = intval($_POST['categoria_id']);

            if (!empty($nombre) && $categoria_id > 0) {
                $stmt = $this->db->prepare("
                    UPDATE equipos
                    SET nombre = :nombre, categoria_id = :categoria_id
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':categoria_id' => $categoria_id,
                    ':id' => $id
                ]);

                $_SESSION['flash_success'] = "Equipo actualizado correctamente.";
                header("Location: " . BASE_URL . "equipos?temporada_id=" . ($equipo['temporada_id'] ?? ''));
                exit;
            } else {
                $_SESSION['flash_error'] = "Debes indicar el nombre y la categoría.";
            }
        }

        require_once __DIR__ . '/../views/equipos/editar.php';
    }

    /**
     * 🗑️ Eliminar equipo
     */
    public function eliminar($id)
    {
        $stmt = $this->db->prepare("
            SELECT e.*, c.nombre AS categoria_nombre, t.nombre AS temporada_nombre
            FROM equipos e
            LEFT JOIN categorias c ON e.categoria_id = c.id
            LEFT JOIN temporadas t ON e.temporada_id = t.id
            WHERE e.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$equipo) {
            $_SESSION['flash_error'] = "El equipo no existe.";
            header("Location: " . BASE_URL . "equipos");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'si') {
                $delete = $this->db->prepare("DELETE FROM equipos WHERE id = :id");
                $delete->execute([':id' => $id]);
                $_SESSION['flash_success'] = "Equipo eliminado correctamente.";
            }
            header("Location: " . BASE_URL . "equipos?temporada_id=" . ($equipo['temporada_id'] ?? ''));
            exit;
        }

        require_once __DIR__ . '/../views/equipos/eliminar.php';
    }
}
?>
