<?php
class TemporadasController
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

    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC");
        $temporadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/temporadas/index.php';
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $activa = isset($_POST['activa']) ? 1 : 0;

            if ($activa) {
                $this->db->exec("UPDATE temporadas SET activa = 0");
            }

            $stmt = $this->db->prepare("INSERT INTO temporadas (nombre, activa) VALUES (:nombre, :activa)");
            $stmt->execute([':nombre' => $nombre, ':activa' => $activa]);

            header("Location: " . BASE_URL . "temporadas");
            exit;
        }

        require_once __DIR__ . '/../views/temporadas/crear.php';
    }

    public function editar($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $activa = isset($_POST['activa']) ? 1 : 0;

            if ($activa) {
                $this->db->exec("UPDATE temporadas SET activa = 0");
            }

            $stmt = $this->db->prepare("UPDATE temporadas SET nombre = :nombre, activa = :activa WHERE id = :id");
            $stmt->execute([':nombre' => $nombre, ':activa' => $activa, ':id' => $id]);

            header("Location: " . BASE_URL . "temporadas");
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM temporadas WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $temporada = $stmt->fetch(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/temporadas/editar.php';
    }

    public function activar($id)
    {
        $this->db->exec("UPDATE temporadas SET activa = 0");
        $stmt = $this->db->prepare("UPDATE temporadas SET activa = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header("Location: " . BASE_URL . "temporadas");
        exit;
    }

    public function eliminar($id)
    {
        $this->db->prepare("DELETE FROM temporadas WHERE id = :id")->execute([':id' => $id]);
        header("Location: " . BASE_URL . "temporadas");
        exit;
    }
}
?>
