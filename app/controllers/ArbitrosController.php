<?php
class ArbitrosController
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../helpers/session_guard.php';
        SessionGuard::check();
        $this->db = Database::getInstance();
    }

    public function index()
    {
        $q          = trim($_GET['q'] ?? '');
        $activo     = $_GET['activo'] ?? ''; // '', 'SI', 'NO'
        $perPage    = (int)($_GET['per_page'] ?? 20);
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $offset     = ($page - 1) * $perPage;

        // Filtros
        $where = [];
        $params = [];

        if ($q !== '') {
            $where[] = "(nombre LIKE :q OR email LIKE :q)";
            $params[':q'] = "%{$q}%";
        }
        if ($activo === 'SI' || $activo === 'NO') {
            $where[] = "activo = :act";
            $params[':act'] = $activo;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Total
        $stCnt = $this->db->prepare("SELECT COUNT(*) FROM arbitros {$whereSql}");
        $stCnt->execute($params);
        $total = (int)$stCnt->fetchColumn();

        // Datos
        $sql = "SELECT * FROM arbitros {$whereSql} ORDER BY nombre ASC LIMIT :lim OFFSET :off";
        $st = $this->db->prepare($sql);
        foreach ($params as $k => $v) $st->bindValue($k, $v);
        $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        $arbitros = $st->fetchAll(PDO::FETCH_ASSOC);

        $totalPaginas = (int)ceil($total / max(1, $perPage));
        $paginaActual = $page;

        require_once __DIR__ . '/../views/arbitros/index.php';
    }


    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $activo   = isset($_POST['activo']) ? 'SI' : 'NO';

            if ($nombre === '') {
                $_SESSION['flash_error'] = "El nombre es obligatorio.";
            } else {
                // ✅ Comprobación para evitar duplicados
                $check = $this->db->prepare("
                SELECT COUNT(*) FROM arbitros
                WHERE nombre = :nombre OR email = :email
            ");
                $check->execute([':nombre' => $nombre, ':email' => $email]);
                if ($check->fetchColumn() > 0) {
                    $_SESSION['flash_warning'] = "Ya existe un árbitro con ese nombre o correo.";
                } else {
                    // ✅ Inserción limpia (sin usuario_id)
                    $st = $this->db->prepare("
                    INSERT INTO arbitros (nombre, email, telefono, activo)
                    VALUES (:n, :e, :t, :a)
                ");
                    $st->execute([
                        ':n' => $nombre,
                        ':e' => $email ?: null,
                        ':t' => $telefono ?: null,
                        ':a' => $activo
                    ]);

                    $_SESSION['flash_success'] = "Árbitro creado correctamente.";
                    header("Location: " . BASE_URL . "arbitros");
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../views/arbitros/crear.php';
    }


    public function editar($id)
    {
        // Solo admin puede editar (opcional: elimínalo si quieres que todos puedan)
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            $_SESSION['flash_error'] = "No tienes permisos para editar árbitros.";
            header('Location: ' . BASE_URL . 'arbitros');
            exit;
        }

        // Buscar árbitro por ID
        $st = $this->db->prepare("SELECT * FROM arbitros WHERE id = :id LIMIT 1");
        $st->execute([':id' => $id]);
        $arbitro = $st->fetch(PDO::FETCH_ASSOC);

        if (!$arbitro) {
            $_SESSION['flash_error'] = "El árbitro no existe.";
            header("Location: " . BASE_URL . "arbitros");
            exit;
        }

        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $activo   = isset($_POST['activo']) ? 'SI' : 'NO';

            if ($nombre === '') {
                $_SESSION['flash_error'] = "El nombre es obligatorio.";
            } else {
                // ✅ Comprobación de duplicados (excluyendo el actual)
                $check = $this->db->prepare("
                SELECT COUNT(*) FROM arbitros
                WHERE (nombre = :nombre OR email = :email)
                  AND id != :id
            ");
                $check->execute([':nombre' => $nombre, ':email' => $email, ':id' => $id]);

                if ($check->fetchColumn() > 0) {
                    $_SESSION['flash_warning'] = "Ya existe otro árbitro con ese nombre o correo.";
                } else {
                    // ✅ Actualización limpia
                    $upd = $this->db->prepare("
                    UPDATE arbitros
                    SET nombre = :n, email = :e, telefono = :t, activo = :a
                    WHERE id = :id
                ");
                    $upd->execute([
                        ':n' => $nombre,
                        ':e' => $email ?: null,
                        ':t' => $telefono ?: null,
                        ':a' => $activo,
                        ':id' => $id
                    ]);

                    $_SESSION['flash_success'] = "Árbitro actualizado correctamente.";
                    header("Location: " . BASE_URL . "arbitros");
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../views/arbitros/editar.php';
    }


    public function eliminar($id)
    {
        // Solo admin puede eliminar (opcional)
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            $_SESSION['flash_error'] = "No tienes permisos para eliminar árbitros.";
            header('Location: ' . BASE_URL . 'arbitros');
            exit;
        }

        // Buscar árbitro por ID
        $st = $this->db->prepare("SELECT nombre FROM arbitros WHERE id = :id LIMIT 1");
        $st->execute([':id' => $id]);
        $nombre = $st->fetchColumn();

        if (!$nombre) {
            $_SESSION['flash_error'] = "El árbitro no existe.";
            header("Location: " . BASE_URL . "arbitros");
            exit;
        }

        // Eliminar directamente
        $del = $this->db->prepare("DELETE FROM arbitros WHERE id = :id");
        $del->execute([':id' => $id]);

        $_SESSION['flash_success'] = "Árbitro <strong>" . htmlspecialchars($nombre) . "</strong> eliminado correctamente.";
        header("Location: " . BASE_URL . "arbitros");
        exit;
    }

}
