<?php
class PreciosController
{
    private $db;
    private $isAdmin;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../helpers/session_guard.php';
        require_once __DIR__ . '/../views/partials/alerts.php';
        SessionGuard::check();
        $this->db = Database::getInstance();
        $u = $_SESSION['usuario'] ?? [];
        $this->isAdmin = isset($u['id']) && ((int)$u['id'] === 1);
    }

    public function index()
    {
        $temporadas = $this->db->query("SELECT id, nombre FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $temporadaId = $_GET['temporada_id'] ?? ($temporadas[0]['id'] ?? null);

        $st = $this->db->prepare("
            SELECT t.id, c.nombre AS categoria, t.rol, t.usa_tablet, t.importe
            FROM tarifas t
            JOIN categorias c ON c.id = t.categoria_id
            WHERE t.temporada_id = :t
            ORDER BY c.nombre, t.rol, t.usa_tablet
        ");
        $st->execute([':t'=>(int)$temporadaId]);
        $tarifas = $st->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/precios/index.php';
    }

    public function crear()
    {
        if (!$this->isAdmin) { header('Location: '.BASE_URL.'precios'); exit; }

        $categorias = $this->db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
        $temporadas = $this->db->query("SELECT id, nombre FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $data = [
                ':c'=>(int)($_POST['categoria_id'] ?? 0),
                ':t'=>(int)($_POST['temporada_id'] ?? 0),
                ':r'=>($_POST['rol'] ?? 'arbitro')==='oficial'?'oficial':'arbitro',
                ':tab'=>!empty($_POST['usa_tablet'])?1:0,
                ':imp'=>(float)($_POST['importe'] ?? 0.0)
            ];
            $ins=$this->db->prepare("INSERT INTO tarifas (categoria_id, temporada_id, rol, usa_tablet, importe) VALUES (:c,:t,:r,:tab,:imp)");
            $ins->execute($data);
            $_SESSION['flash_success']="Tarifa creada.";
            header('Location: '.BASE_URL.'precios');
            exit;
        }

        require_once __DIR__ . '/../views/precios/crear.php';
    }

    public function editar($id)
    {
        if (!$this->isAdmin) { header('Location: '.BASE_URL.'precios'); exit; }

        $categorias = $this->db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
        $temporadas = $this->db->query("SELECT id, nombre FROM temporadas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $data = [
                ':id'=>(int)$id,
                ':c'=>(int)($_POST['categoria_id'] ?? 0),
                ':t'=>(int)($_POST['temporada_id'] ?? 0),
                ':r'=>($_POST['rol'] ?? 'arbitro')==='oficial'?'oficial':'arbitro',
                ':tab'=>!empty($_POST['usa_tablet'])?1:0,
                ':imp'=>(float)($_POST['importe'] ?? 0.0)
            ];
            $up=$this->db->prepare("UPDATE tarifas SET categoria_id=:c, temporada_id=:t, rol=:r, usa_tablet=:tab, importe=:imp WHERE id=:id");
            $up->execute($data);
            $_SESSION['flash_success']="Tarifa actualizada.";
            header('Location: '.BASE_URL.'precios');
            exit;
        }

        $st=$this->db->prepare("SELECT * FROM tarifas WHERE id=:id LIMIT 1");
        $st->execute([':id'=>(int)$id]);
        $tarifa=$st->fetch(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/precios/editar.php';
    }

    public function eliminar($id)
    {
        if (!$this->isAdmin) { header('Location: '.BASE_URL.'precios'); exit; }
        $del=$this->db->prepare("DELETE FROM tarifas WHERE id=:id");
        $del->execute([':id'=>(int)$id]);
        $_SESSION['flash_success']="Tarifa eliminada.";
        header('Location: '.BASE_URL.'precios');
        exit;
    }
}
