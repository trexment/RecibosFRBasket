<?php
class RegisterController
{
    public function index()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';

        $db = Database::getInstance();

        // Obtener el código de invitación desde la URL
        $codigo = $_GET['codigo'] ?? null;

        if (!$codigo) {
            header("HTTP/1.0 404 Not Found");
            echo "Página no encontrada.";
            exit;
        }

        // Buscar invitación válida
        $stmt = $db->prepare("SELECT * FROM invites WHERE codigo = :codigo LIMIT 1");
        $stmt->execute([':codigo' => $codigo]);
        $invite = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invite) {
            $error = "El código de invitación no es válido.";
        } elseif ($invite['usado']) {
            $error = "Este código de invitación ya ha sido utilizado.";
        } elseif (strtotime($invite['expires_at']) < time()) {
            $error = "El código de invitación ha caducado.";
        }

        require_once __DIR__ . '/../views/register.php';
    }

    public function store()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        $db = Database::getInstance();

        // Recoger datos del formulario
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $domicilio = trim($_POST['domicilio'] ?? '');
        $codigo_postal = trim($_POST['codigo_postal'] ?? '');
        $cuenta_bancaria = trim($_POST['cuenta_bancaria'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        $invite_code = trim($_POST['invite_code'] ?? '');

        $errors = [];

        // Validaciones
        if (!$invite_code) $errors[] = "Debes introducir un código de invitación.";
        if (!$nombre) $errors[] = "El nombre es obligatorio.";
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo electrónico no válido.";
        if (strlen($password) < 8) $errors[] = "La contraseña debe tener al menos 8 caracteres.";
        if ($password !== $password2) $errors[] = "Las contraseñas no coinciden.";

        if (!empty($errors)) {
            $error = implode('<br>', $errors);
            require_once __DIR__ . '/../views/register.php';
            return;
        }

        // Validar código de invitación
        $stmt = $db->prepare("SELECT * FROM invites WHERE codigo = :codigo AND usado = 0 LIMIT 1");
        $stmt->execute([':codigo' => $invite_code]);
        $invite = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invite) {
            $error = "El código de invitación no es válido o ya ha sido utilizado.";
            require_once __DIR__ . '/../views/register.php';
            return;
        }

        if (strtotime($invite['expires_at']) < time()) {
            $error = "El código de invitación ha caducado.";
            require_once __DIR__ . '/../views/register.php';
            return;
        }

        // Comprobar email único
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $error = "Ya existe una cuenta con ese correo.";
            require_once __DIR__ . '/../views/register.php';
            return;
        }

        // Crear usuario
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO usuarios 
            (nombre, apellidos, email, password, domicilio, codigo_postal, cuenta_bancaria, dni) 
            VALUES 
            (:nombre, :apellidos, :email, :password, :domicilio, :cp, :cuenta, :dni)
        ");
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':email' => $email,
            ':password' => $hash,
            ':domicilio' => $domicilio,
            ':cp' => $codigo_postal,
            ':cuenta' => $cuenta_bancaria,
            ':dni' => $dni
        ]);

        // Marcar invitación como usada
        $userId = $db->lastInsertId();
        $stmt = $db->prepare("UPDATE invites SET usado = 1, nombre_usuario = :nombre WHERE codigo = :codigo");
        $stmt->execute([':nombre' => $nombre . ' ' . $apellidos, ':codigo' => $invite_code]);

        // Login automático
        session_start();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $_SESSION['usuario'] = $stmt->fetch(PDO::FETCH_ASSOC);

        header("Location: " . BASE_URL . "dashboard");
        exit;
    }
}
