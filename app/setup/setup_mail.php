<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configFile = __DIR__ . '/../config/config.php';
    $mail_host = trim($_POST['mail_host']);
    $mail_user = trim($_POST['mail_user']);
    $mail_pass = trim($_POST['mail_pass']);
    $mail_port = trim($_POST['mail_port']);
    $mail_from = trim($_POST['mail_from']);
    $mail_name = trim($_POST['mail_name']);

    $content = file_get_contents($configFile);

    $replace = [
            "define('MAIL_HOST'," => "define('MAIL_HOST', '$mail_host');",
            "define('MAIL_USER'," => "define('MAIL_USER', '$mail_user');",
            "define('MAIL_PASS'," => "define('MAIL_PASS', '$mail_pass');",
            "define('MAIL_PORT'," => "define('MAIL_PORT', $mail_port);",
            "define('MAIL_FROM'," => "define('MAIL_FROM', '$mail_from');",
            "define('MAIL_FROM_NAME'," => "define('MAIL_FROM_NAME', '$mail_name');"
    ];

    foreach ($replace as $key => $newLine) {
        $content = preg_replace("/$key\s*'.*?'\s*\);|$key\s*[0-9]+\s*\);/", $newLine, $content);
    }

    file_put_contents($configFile, $content);

    echo "<div style='color:green;font-family:Arial;margin:2em;text-align:center'>
            ✅ Configuración guardada correctamente.<br><br>
            <a href='" . BASE_URL . "login' style='color:#007bff;text-decoration:none;'>Ir al Login</a>
          </div>";
    exit;
}
?>

<form method="POST" style="font-family:Arial;max-width:600px;margin:2em auto;">
    <h2>Configuración de correo (PHPMailer)</h2>
    <p>Introduce los datos del servidor SMTP de la Federación.</p>

    <label>Servidor SMTP:</label>
    <input type="text" name="mail_host" class="form-control" required><br>

    <label>Usuario (email):</label>
    <input type="email" name="mail_user" class="form-control" required><br>

    <label>Contraseña:</label>
    <input type="password" name="mail_pass" class="form-control" required><br>

    <label>Puerto SMTP:</label>
    <input type="number" name="mail_port" value="465" class="form-control" required><br>

    <label>Email remitente:</label>
    <input type="email" name="mail_from" class="form-control" required><br>

    <label>Nombre remitente:</label>
    <input type="text" name="mail_name" class="form-control" required><br>

    <button type="submit" style="margin-top:1em;padding:10px 20px;background:#4CAF50;color:white;border:none;border-radius:5px;">
        Guardar configuración
    </button>
</form>
