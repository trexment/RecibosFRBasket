<?php
class TemporadasController
{
    private $db;
    private $usuario;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../helpers/session_guard.php';

        $this->usuario = $_SESSION['usuario'] ?? null;
        if (!$this->usuario) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        $this->db = Database::getInstance();
    }

    public function index()
    {
        $stmt = $this->db->prepare("SELECT * FROM temporadas WHERE usuario_id = :uid ORDER BY id DESC");
        $stmt->execute([':uid' => $this->usuario['id']]);
        $temporadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/temporadas/index.php';
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $activa = isset($_POST['activa']) ? 1 : 0;

            if ($activa) {
                $this->db->prepare("UPDATE temporadas SET activa = 0 WHERE usuario_id = :uid")
                    ->execute([':uid' => $this->usuario['id']]);
            }

            $stmt = $this->db->prepare("
                INSERT INTO temporadas (nombre, activa, usuario_id)
                VALUES (:nombre, :activa, :usuario_id)
            ");
            $stmt->execute([
                ':nombre' => $nombre,
                ':activa' => $activa,
                ':usuario_id' => $this->usuario['id']
            ]);

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
                $this->db->prepare("UPDATE temporadas SET activa = 0 WHERE usuario_id = :uid")
                    ->execute([':uid' => $this->usuario['id']]);
            }

            $stmt = $this->db->prepare("
                UPDATE temporadas SET nombre = :nombre, activa = :activa
                WHERE id = :id AND usuario_id = :uid
            ");
            $stmt->execute([
                ':nombre' => $nombre,
                ':activa' => $activa,
                ':id' => $id,
                ':uid' => $this->usuario['id']
            ]);

            header("Location: " . BASE_URL . "temporadas");
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM temporadas WHERE id = :id AND usuario_id = :uid LIMIT 1");
        $stmt->execute([':id' => $id, ':uid' => $this->usuario['id']]);
        $temporada = $stmt->fetch(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/temporadas/editar.php';
    }

    public function activar($id)
    {
        $this->db->prepare("UPDATE temporadas SET activa = 0 WHERE usuario_id = :uid")
            ->execute([':uid' => $this->usuario['id']]);

        $this->db->prepare("UPDATE temporadas SET activa = 1 WHERE id = :id AND usuario_id = :uid")
            ->execute([':id' => $id, ':uid' => $this->usuario['id']]);

        header("Location: " . BASE_URL . "temporadas");
        exit;
    }

    public function eliminar($id)
    {
        $this->db->prepare("DELETE FROM temporadas WHERE id = :id AND usuario_id = :uid")
            ->execute([':id' => $id, ':uid' => $this->usuario['id']]);
        header("Location: " . BASE_URL . "temporadas");
        exit;
    }
}
?>
