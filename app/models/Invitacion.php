<?php
class Invitacion
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function obtenerTodas(): array
    {
        $sql = "
            SELECT i.*, u.email AS usado_por
            FROM invitaciones i
            LEFT JOIN usuarios u ON i.used_by = u.id
            ORDER BY i.fecha_creacion DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardar(string $codigo, ?string $email): bool
    {
        $sql = "INSERT INTO invitaciones (codigo, email_enviado, usado)
                VALUES (:codigo, :email_enviado, 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':codigo' => $codigo,
            ':email_enviado' => $email
        ]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM invitaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
