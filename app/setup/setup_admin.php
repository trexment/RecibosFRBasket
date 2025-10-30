<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();

// Verificar si ya existe usuario ID=1
$check = $db->query("SELECT COUNT(*) FROM usuarios WHERE id = 1")->fetchColumn();
if ($check > 0) {
    echo "⚠️ Ya existe un usuario administrador. No es necesario ejecutar este script.";
    exit;
}

// Crear admin genérico sin email vinculado
$stmt = $db->prepare("
    INSERT INTO usuarios (nombre, email, password, rol)
    VALUES (:nombre, :email, :password, :rol)
");
$stmt->execute([
    ':nombre' => 'Administrador',
    ':email' => 'admin@federacionriojana.es',
    ':password' => password_hash('admin123', PASSWORD_DEFAULT),
    ':rol' => 'admin'
]);

echo "✅ Usuario administrador creado correctamente (email: admin@federacionriojana.es / pass: admin123)";
