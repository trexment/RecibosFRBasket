<?php

class PerfilController
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

    // Mostrar y editar perfil
    public function index()
    {
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if (!$usuarioId) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $domicilio = trim($_POST['domicilio'] ?? '');
            $codigo_postal = trim($_POST['codigo_postal'] ?? '');
            $cuenta_bancaria = trim($_POST['cuenta_bancaria'] ?? '');
            $dni = trim($_POST['dni'] ?? '');

            // Actualizar en base de datos
            $update = $this->db->prepare("UPDATE usuarios SET 
                nombre = :nombre,
                apellidos = :apellidos,
                email = :email,
                domicilio = :domicilio,
                codigo_postal = :codigo_postal,
                cuenta_bancaria = :cuenta_bancaria,
                dni = :dni
                WHERE id = :id");

            $update->execute([
                ':nombre' => $nombre,
                ':apellidos' => $apellidos,
                ':email' => $email,
                ':domicilio' => $domicilio,
                ':codigo_postal' => $codigo_postal,
                ':cuenta_bancaria' => $cuenta_bancaria,
                ':dni' => $dni,
                ':id' => $usuarioId
            ]);

            $_SESSION['flash_success'] = "Perfil actualizado correctamente.";

            // Actualiza también la sesión
            $_SESSION['usuario']['nombre'] = $nombre;
            $_SESSION['usuario']['apellidos'] = $apellidos;
            $_SESSION['usuario']['email'] = $email;
            $_SESSION['usuario']['domicilio'] = $domicilio;
            $_SESSION['usuario']['codigo_postal'] = $codigo_postal;
            $_SESSION['usuario']['cuenta_bancaria'] = $cuenta_bancaria;
            $_SESSION['usuario']['dni'] = $dni;

            header('Location: ' . BASE_URL . 'perfil');
            exit;
        }

        // Obtener datos actualizados desde la base de datos (si GET)
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $usuarioId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        $GLOBALS['usuario'] = $usuario;

        $usuario_data = $usuario;
        require __DIR__ . '/../views/usuarios/perfil.php';

    }

    // Cambiar contraseña
    public function cambiarPassword()
    {
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if (!$usuarioId) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actual = $_POST['actual'] ?? '';
            $nueva = $_POST['nueva'] ?? '';
            $repite = $_POST['repite'] ?? '';

            // Obtener contraseña actual
            $stmt = $this->db->prepare("SELECT password FROM usuarios WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $usuarioId]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario || !password_verify($actual, $usuario['password'])) {
                $error = "La contraseña actual no es correcta.";
            } elseif (strlen($nueva) < 6) {
                $error = "La nueva contraseña debe tener al menos 6 caracteres.";
            } elseif ($nueva !== $repite) {
                $error = "Las nuevas contraseñas no coinciden.";
            } else {
                // Actualizar contraseña
                $hash = password_hash($nueva, PASSWORD_BCRYPT);
                $update = $this->db->prepare("UPDATE usuarios SET password = :pass WHERE id = :id");
                $update->execute([':pass' => $hash, ':id' => $usuarioId]);
                $success = "Contraseña actualizada correctamente.";
            }
        }

        $GLOBALS['error'] = $error;
        $GLOBALS['success'] = $success;

        require_once __DIR__ . '/../views/usuarios/password.php';
    }
}
