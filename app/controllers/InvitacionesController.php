<?php
require_once 'app/models/Invitacion.php';

class InvitacionesController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index(): void
    {
        $stmt = $this->db->query("
            SELECT i.*, u.email AS usado_por
            FROM invitaciones i
            LEFT JOIN usuarios u ON i.used_by = u.id
            ORDER BY i.fecha_creacion DESC
        ");
        $invitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/invitaciones/index.php';
    }

    public function crear(): void
    {
        require 'app/views/invitaciones/crear.php';
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = strtoupper(bin2hex(random_bytes(4)));
            $email = $_POST['email'] ?? null;

            $stmt = $this->db->prepare("
                INSERT INTO invitaciones (codigo, email_enviado, usado)
                VALUES (:codigo, :email_enviado, 0)
            ");
            $stmt->execute([
                ':codigo' => $codigo,
                ':email_enviado' => $email
            ]);
        }

        header('Location: /invitaciones');
        exit;
    }

    public function eliminar($id): void
    {
        $stmt = $this->db->prepare("DELETE FROM invitaciones WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: /invitaciones');
        exit;
    }
}
