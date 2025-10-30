<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class InvitesController
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../helpers/session_guard.php';
        require_once __DIR__ . '/../views/partials/alerts.php';
        SessionGuard::check();

        $this->db = Database::getInstance();

        // Cargar PHPMailer desde tu carpeta local
        require_once __DIR__ . '/../libraries/PHPMailer/PHPMailer.php';
        require_once __DIR__ . '/../libraries/PHPMailer/SMTP.php';
        require_once __DIR__ . '/../libraries/PHPMailer/Exception.php';
    }

    /**
     * 📋 Listado de invitaciones
     */
    public function index()
    {
        $this->db->query("DELETE FROM invites WHERE usado = 0 AND expires_at < NOW()");

        if (($_SESSION['rol'] ?? '') !== 'admin') {
            $_SESSION['flash_error'] = "No tienes permisos para acceder a esta sección.";
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $stmt = $this->db->query("SELECT * FROM invites ORDER BY created_at DESC");
        $invites = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/invites/index.php';
    }

    /**
     * ✉️ Generar y enviar invitación
     */
    public function generar()
    {
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            $_SESSION['flash_error'] = "Acceso denegado.";
            header('Location: ' . BASE_URL . 'invites');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../views/invites/index.php';
            return;
        }

        $email  = trim($_POST['email'] ?? '');
        $codigo = strtoupper('INV-' . bin2hex(random_bytes(4)));

        try {
            // Insertar en la base de datos
            $stmt = $this->db->prepare("
    INSERT INTO invites (codigo, email, email_enviado, usado, nombre_usuario, expires_at)
    VALUES (:codigo, :email, 0, 0, NULL, DATE_ADD(NOW(), INTERVAL 3 DAY))
");

            $stmt->execute([':codigo' => $codigo, ':email' => $email ?: null]);

            if ($email !== '') {
                $this->enviarCorreoInvitacion($email, $codigo);
                $this->db->prepare("UPDATE invites SET email_enviado = 1 WHERE codigo = :codigo")
                    ->execute([':codigo' => $codigo]);
            }

            $_SESSION['flash_success'] = "Invitación generada correctamente. Código: <strong>$codigo</strong>";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al generar la invitación: " . $e->getMessage();
        }

        header('Location: ' . BASE_URL . 'invites');
        exit;
    }

    /**
     * 📧 Enviar correo con PHPMailer
     */
    private function enviarCorreoInvitacion(string $email, string $codigo)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Invitación a Recibos Arbitrales';
            $mail->Body = "
                <p>Hola,</p>
                <p>Has sido invitado a registrarte en la plataforma <strong>Recibos Arbitrales</strong>.</p>
                <p>Tu código de invitación es:</p>
                <h3 style='color:#a12020;'>$codigo</h3>
                <p>Completa tu registro en el siguiente enlace:</p>
                <a href='" . BASE_URL . "register?codigo=$codigo'
                   style='background:#a12020;color:#fff;padding:8px 14px;text-decoration:none;border-radius:5px;'>
                   Ir al registro
                </a>
                <p>Un saludo,<br>Federación Riojana de Baloncesto</p>
            ";

            $mail->AltBody = "Tu código de invitación es $codigo. Regístrate en " . BASE_URL . "registro?codigo=$codigo";

            $mail->send();
        } catch (Exception $e) {
            $_SESSION['flash_warning'] = "Código creado, pero no se pudo enviar el correo: " . $mail->ErrorInfo;
        }
    }
}
