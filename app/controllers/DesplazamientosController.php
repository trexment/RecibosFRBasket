<?php

class DesplazamientosController
{
    private $db;
    private $isAdmin;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../helpers/session_guard.php';
        require_once __DIR__ . '/../views/partials/alerts.php';

        $this->db = Database::getInstance();
        $this->isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
    }

    public function index()
    {
        if (!$this->isAdmin) {
            $_SESSION['flash_error'] = "Acceso no autorizado.";
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT d.*, t.nombre AS temporada_nombre
                FROM desplazamientos d
                LEFT JOIN temporadas t ON t.id = d.temporada_id
                ORDER BY d.temporada_id DESC, d.activo DESC";
        $desplazamientos = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/desplazamientos/index.php';
    }

    public function crear()
    {
        if (!$this->isAdmin) {
            $_SESSION['flash_error'] = "Acceso no autorizado.";
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $temporada_id = $_POST['temporada_id'] ?? null;
            $precio_km = floatval($_POST['precio_km'] ?? 0);
            $activo = isset($_POST['activo']) ? 1 : 0;

            if ($activo) {
                $this->db->prepare("UPDATE desplazamientos SET activo = 0 WHERE temporada_id = :t")
                    ->execute([':t' => $temporada_id]);
            }

            $stmt = $this->db->prepare("INSERT INTO desplazamientos (temporada_id, precio_km, activo) VALUES (:t, :p, :a)");
            $stmt->execute([':t' => $temporada_id, ':p' => $precio_km, ':a' => $activo]);

            $_SESSION['flash_success'] = "Registro creado.";
            header('Location: ' . BASE_URL . 'desplazamientos');
            exit;
        }

        require_once __DIR__ . '/../views/desplazamientos/crear.php';
    }

    public function editar($id)
    {
        if (!$this->isAdmin) {
            $_SESSION['flash_error'] = "Acceso no autorizado.";
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT * FROM desplazamientos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $desp = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$desp) {
            $_SESSION['flash_error'] = "Registro no encontrado.";
            header('Location: ' . BASE_URL . 'desplazamientos');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $temporada_id = $_POST['temporada_id'] ?? null;
            $precio_km = floatval($_POST['precio_km'] ?? 0);
            $activo = isset($_POST['activo']) ? 1 : 0;

            if ($activo) {
                $this->db->prepare("UPDATE desplazamientos SET activo = 0 WHERE temporada_id = :t AND id <> :id")
                    ->execute([':t' => $temporada_id, ':id' => $id]);
            }

            $stmt = $this->db->prepare("UPDATE desplazamientos SET temporada_id = :t, precio_km = :p, activo = :a WHERE id = :id");
            $stmt->execute([':t' => $temporada_id, ':p' => $precio_km, ':a' => $activo, ':id' => $id]);

            $_SESSION['flash_success'] = "Registro actualizado.";
            header('Location: ' . BASE_URL . 'desplazamientos');
            exit;
        }

        require_once __DIR__ . '/../views/desplazamientos/editar.php';
    }

    public function eliminar($id)
    {
        if (!$this->isAdmin) {
            $_SESSION['flash_error'] = "Acceso no autorizado.";
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $this->db->prepare("DELETE FROM desplazamientos WHERE id = :id")->execute([':id' => $id]);
        $_SESSION['flash_success'] = "Registro eliminado.";
        header('Location: ' . BASE_URL . 'desplazamientos');
        exit;
    }
}
