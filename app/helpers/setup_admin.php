<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getInstance();
    $count = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

    if ($count == 0) {
        $hash = password_hash('admin1234', PASSWORD_DEFAULT);
        $stmt = $db->prepare("
            INSERT INTO usuarios (nombre, apellidos, email, password, rol)
            VALUES ('Administrador', 'Sistema', 'admin@recibos.frannunez.es', :pass, 'admin')
        ");
        $stmt->execute([':pass' => $hash]);

        echo "✅ Usuario administrador creado automáticamente.<br>
              Email: admin@recibos.frannunez.es<br>
              Contraseña: admin1234";
    }
} catch (Exception $e) {
    error_log("Error al verificar o crear admin: " . $e->getMessage());
}
