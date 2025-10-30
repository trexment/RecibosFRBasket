<?php
class PasswordController
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        $this->db = Database::getInstance();
    }

    /** ✅ Vista del formulario "¿Olvidaste tu contraseña?" */
    public function forgot()
    {
        require_once __DIR__ . '/../views/password/forgot.php';
    }

    /** ✉️ Envío del enlace al correo */
    public function send()
    {
        $email = trim($_POST['email'] ?? '');

        if (!$email) {
            $_SESSION['flash_error'] = "Por favor, introduce tu correo.";
            header("Location: " . BASE_URL . "password/forgot");
            exit;
        }

        // ¿Existe el usuario?
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['flash_error'] = "No existe ninguna cuenta con ese correo.";
            header("Location: " . BASE_URL . "password/forgot");
            exit;
        }

        // Generar token único
        $token = bin2hex(random_bytes(32));

        // Guardar token en BD
        $stmt = $this->db->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
        $stmt->execute([':email' => $email, ':token' => $token]);

        // Enviar correo con PHPMailer
        require_once __DIR__ . '/../libraries/PHPMailer/PHPMailer.php';
        require_once __DIR__ . '/../libraries/PHPMailer/SMTP.php';
        require_once __DIR__ . '/../libraries/PHPMailer/Exception.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = MAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_USER;
            $mail->Password = MAIL_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port = MAIL_PORT;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de contraseña - Recibos Arbitrales';
            $mail->Body = "
                <p>Hola,</p>
                <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace:</p>
                <p><a href='" . BASE_URL . "password/reset?token=$token'>Restablecer contraseña</a></p>
                <p>Si no lo solicitaste, ignora este mensaje.</p>
            ";
            $mail->send();

            $_SESSION['flash_success'] = "Se ha enviado un correo con instrucciones.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "No se pudo enviar el correo: " . $mail->ErrorInfo;
        }

        header("Location: " . BASE_URL . "password/forgot");
        exit;
    }

    /** 📝 Mostrar formulario para introducir nueva contraseña */
    public function reset()
    {
        $token = $_GET['token'] ?? '';
        require_once __DIR__ . '/../views/password/reset.php';
    }

    /** 💾 Guardar nueva contraseña */
    public function update()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (!$token || !$password || $password !== $password2) {
            $_SESSION['flash_error'] = "Las contraseñas no coinciden o el token no es válido.";
            header("Location: " . BASE_URL . "password/reset?token=$token");
            exit;
        }

        // Buscar el email del token
        $stmt = $this->db->prepare("SELECT email FROM password_resets WHERE token = :token LIMIT 1");
        $stmt->execute([':token' => $token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            $_SESSION['flash_error'] = "El enlace no es válido o ha caducado.";
            header("Location: " . BASE_URL . "login");
            exit;
        }

        $email = $reset['email'];
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Actualizar usuario
        $stmt = $this->db->prepare("UPDATE usuarios SET password = :p WHERE email = :e");
        $stmt->execute([':p' => $hash, ':e' => $email]);

        // Eliminar token usado
        $this->db->prepare("DELETE FROM password_resets WHERE email = :e")->execute([':e' => $email]);

        $_SESSION['flash_success'] = "Tu contraseña se ha actualizado correctamente.";
        header("Location: " . BASE_URL . "login");
        exit;
    }
}
