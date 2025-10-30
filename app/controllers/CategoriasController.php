<?php
class CategoriasController
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
     * 📋 Listado de categorías
     */
    public function index()
    {
        // Cargar todas las categorías con su temporada asociada
        $stmt = $this->db->query("
            SELECT c.*, t.nombre AS temporada_nombre
            FROM categorias c
            LEFT JOIN temporadas t ON c.temporada_id = t.id
            ORDER BY c.nombre ASC
        ");
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/categorias/index.php';
    }

    /**
     * ➕ Crear categoría
     */
    public function crear()
    {
        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $abreviatura = trim($_POST['abreviatura']);
            $temporada_id = intval($_POST['temporada_id'] ?? 0);

            if (!empty($nombre)) {
                $stmt = $this->db->prepare("
                    INSERT INTO categorias (nombre, abreviatura, temporada_id)
                    VALUES (:nombre, :abreviatura, :temporada_id)
                ");
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':abreviatura' => $abreviatura,
                    ':temporada_id' => $temporada_id ?: null
                ]);

                $_SESSION['flash_success'] = "Categoría creada correctamente.";
                header("Location: " . BASE_URL . "categorias");
                exit;
            } else {
                $_SESSION['flash_error'] = "El nombre de la categoría es obligatorio.";
            }
        }

        require_once __DIR__ . '/../views/categorias/crear.php';
    }

    /**
     * ✏️ Editar categoría
     */
    public function editar($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$categoria) {
            $_SESSION['flash_error'] = "La categoría no existe.";
            header("Location: " . BASE_URL . "categorias");
            exit;
        }

        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $abreviatura = trim($_POST['abreviatura']);
            $temporada_id = intval($_POST['temporada_id'] ?? 0);

            if (!empty($nombre)) {
                $stmt = $this->db->prepare("
                    UPDATE categorias
                    SET nombre = :nombre, abreviatura = :abreviatura, temporada_id = :temporada_id
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':abreviatura' => $abreviatura,
                    ':temporada_id' => $temporada_id ?: null,
                    ':id' => $id
                ]);

                $_SESSION['flash_success'] = "Categoría actualizada correctamente.";
                header("Location: " . BASE_URL . "categorias");
                exit;
            } else {
                $_SESSION['flash_error'] = "El nombre no puede estar vacío.";
            }
        }

        require_once __DIR__ . '/../views/categorias/editar.php';
    }

    /**
     * 🗑️ Eliminar categoría
     */
    public function eliminar($id)
    {
        SessionGuard::check();

        try {
            // Comprobamos que existe la categoría
            $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$categoria) {
                $_SESSION['flash_error'] = "⚠️ La categoría no existe o ya fue eliminada.";
                header("Location: " . BASE_URL . "categorias");
                exit;
            }

            // ⚠️ Verificar si tiene equipos asociados
            $checkEquipos = $this->db->prepare("SELECT COUNT(*) FROM equipos WHERE categoria_id = :id");
            $checkEquipos->execute([':id' => $id]);
            if ($checkEquipos->fetchColumn() > 0) {
                $_SESSION['flash_error'] = "❌ No puedes eliminar una categoría que tiene equipos asociados.";
                header("Location: " . BASE_URL . "categorias");
                exit;
            }

            // Eliminar la categoría
            $delete = $this->db->prepare("DELETE FROM categorias WHERE id = :id");
            $delete->execute([':id' => $id]);

            $_SESSION['flash_success'] = "✅ Categoría eliminada correctamente.";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "❌ Error al eliminar: " . $e->getMessage();
        }

        header("Location: " . BASE_URL . "categorias");
        exit;
    }

}
?>
