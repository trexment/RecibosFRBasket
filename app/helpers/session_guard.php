<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../core/Database.php';

class SessionGuard
{
    public static function check()
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    public static function checkAdmin()
    {
        if (empty($_SESSION['usuario_id']) || (($_SESSION['rol'] ?? '') !== 'admin')) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    public static function user()
    {
        self::check();

        if (!empty($_SESSION['usuario'])) {
            return $_SESSION['usuario'];
        }

        if (!empty($_SESSION['usuario_id'])) {
            $db = Database::getInstance();
            $st = $db->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
            $st->execute([':id' => $_SESSION['usuario_id']]);
            $u = $st->fetch(PDO::FETCH_ASSOC);
            if ($u) {
                $_SESSION['usuario'] = $u;
                return $u;
            }
        }

        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}
