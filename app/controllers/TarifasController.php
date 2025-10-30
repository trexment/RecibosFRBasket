<?php

class TarifasController
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
        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $categorias = $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

        $temporadaId = $_GET['temporada_id'] ?? null;
        $categoriaId = $_GET['categoria_id'] ?? null;

        if (empty($temporadaId)) {
            $temporadaId = $this->db->query("SELECT id FROM temporadas WHERE activa = 1 LIMIT 1")->fetchColumn();
        }

        $sql = "SELECT t.*, c.nombre AS categoria_nombre, temp.nombre AS temporada_nombre
                FROM tarifas t
                LEFT JOIN categorias c ON t.categoria_id = c.id
                LEFT JOIN temporadas temp ON t.temporada_id = temp.id
                WHERE 1=1";
        $params = [];
        if (!empty($temporadaId)) { $sql .= " AND t.temporada_id = :temporada"; $params[':temporada'] = $temporadaId; }
        if (!empty($categoriaId)) { $sql .= " AND t.categoria_id = :categoria"; $params[':categoria'] = $categoriaId; }
        $sql .= " ORDER BY c.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $tarifas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/tarifas/index.php';
    }

    public function crear()
    {
        if (!$this->isAdmin) {
            $_SESSION['flash_tarifa_error'] = "No tienes permisos para crear tarifas.";
            header('Location: ' . BASE_URL . 'tarifas');
            exit;
        }

        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $categorias = $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria_id = $_POST['categoria_id'] ?? null;
            $temporada_id = $_POST['temporada_id'] ?? null;
            $rol = $_POST['rol'] ?? '';
            $importe = floatval($_POST['importe'] ?? 0);

            $stmt = $this->db->prepare("
                INSERT INTO tarifas (categoria_id, temporada_id, rol, importe)
                VALUES (:categoria, :temporada, :rol, :importe)
            ");
            $stmt->execute([
                ':categoria' => $categoria_id,
                ':temporada' => $temporada_id,
                ':rol' => $rol,
                ':importe' => $importe
            ]);

            $_SESSION['flash_tarifa_success'] = "Tarifa creada correctamente.";
            header('Location: ' . BASE_URL . 'tarifas');
            exit;
        }

        require_once __DIR__ . '/../views/tarifas/crear.php';
    }

    public function editar($id)
    {
        if (!$this->isAdmin) {
            $_SESSION['flash_tarifa_error'] = "No tienes permisos para editar.";
            header('Location: ' . BASE_URL . 'tarifas');
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM tarifas WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $tarifa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tarifa) {
            $_SESSION['flash_tarifa_error'] = "Tarifa no encontrada.";
            header('Location: ' . BASE_URL . 'tarifas');
            exit;
        }

        $temporadas = $this->db->query("SELECT * FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $categorias = $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria_id = $_POST['categoria_id'] ?? null;
            $temporada_id = $_POST['temporada_id'] ?? null;
            $rol = $_POST['rol'] ?? '';
            $importe = floatval($_POST['importe'] ?? 0);

            $stmt = $this->db->prepare("
                UPDATE tarifas SET
                    categoria_id = :categoria,
                    temporada_id = :temporada,
                    rol = :rol,
                    importe = :importe
                WHERE id = :id
            ");
            $stmt->execute([
                ':categoria' => $categoria_id,
                ':temporada' => $temporada_id,
                ':rol' => $rol,
                ':importe' => $importe,
                ':id' => $id
            ]);

            $_SESSION['flash_tarifa_success'] = "Tarifa actualizada correctamente.";
            header('Location: ' . BASE_URL . 'tarifas');
            exit;
        }

        require_once __DIR__ . '/../views/tarifas/editar.php';
    }

    public function eliminar($id)
    {
        if (!$this->isAdmin) {
            $_SESSION['flash_tarifa_error'] = "No tienes permisos para eliminar.";
            header('Location: ' . BASE_URL . 'tarifas');
            exit;
        }

        $stmt = $this->db->prepare("DELETE FROM tarifas WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['flash_tarifa_success'] = "Tarifa eliminada correctamente.";
        header('Location: ' . BASE_URL . 'tarifas');
        exit;
    }
}
?>
