<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../helpers/session_guard.php';

class LoginController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        require __DIR__ . '/../views/login.php';
    }

    public function autenticar()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['password'])) {
                // Guardamos TODO lo que usa la app
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['rol']        = $usuario['rol'] ?? 'usuario';
                $_SESSION['nombre']     = $usuario['nombre'] ?? '';
                $_SESSION['usuario']    = $usuario; // para que SessionGuard::user() lo tenga ya

                header('Location: ' . BASE_URL . 'dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Usuario o contraseña incorrectos';
                header('Location: ' . BASE_URL . 'login');
                exit;
            }
        } catch (PDOException $e) {
            die("Error de autenticación: " . $e->getMessage());
        }
    }

    public function logout()
    {
        SessionGuard::logout();
    }
}
