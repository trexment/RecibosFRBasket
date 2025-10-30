<?php
class Equipo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function obtenerTodos(): array
    {
        $sql = "
            SELECT e.*, 
                   c.nombre AS categoria_nombre, 
                   t.nombre AS temporada_nombre
            FROM equipos e
            LEFT JOIN categorias c ON e.categoria_id = c.id
            LEFT JOIN temporadas t ON e.temporada_id = t.id
            ORDER BY c.nombre ASC, e.nombre ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM equipos WHERE id = ?");
        $stmt->execute([$id]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $equipo ?: null;
    }

    public function guardar(array $data): bool
    {
        $sql = "INSERT INTO equipos (nombre, categoria_id, temporada_id, usuario_id)
                VALUES (:nombre, :categoria_id, :temporada_id, :usuario_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':categoria_id' => $data['categoria_id'] ?? null,
            ':temporada_id' => $data['temporada_id'] ?? null,
            ':usuario_id' => $data['usuario_id'] ?? null
        ]);
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE equipos
                SET nombre = :nombre,
                    categoria_id = :categoria_id,
                    temporada_id = :temporada_id
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':categoria_id' => $data['categoria_id'] ?? null,
            ':temporada_id' => $data['temporada_id'] ?? null,
            ':id' => $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM equipos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
