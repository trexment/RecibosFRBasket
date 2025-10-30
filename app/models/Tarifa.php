<?php
class Tarifa
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function obtenerTodas(): array
    {
        $sql = "
            SELECT t.*, 
                   c.nombre AS categoria_nombre, 
                   te.nombre AS temporada_nombre
            FROM tarifas t
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN temporadas te ON t.temporada_id = te.id
            ORDER BY te.nombre DESC, c.nombre ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM tarifas WHERE id = ?");
        $stmt->execute([$id]);
        $tarifa = $stmt->fetch(PDO::FETCH_ASSOC);
        return $tarifa ?: null;
    }

    public function guardar(array $data): bool
    {
        $sql = "
            INSERT INTO tarifas (categoria_id, temporada_id, rol, num_arbitros, num_oficiales, usa_tablet, importe)
            VALUES (:categoria_id, :temporada_id, :rol, :num_arbitros, :num_oficiales, :usa_tablet, :importe)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':categoria_id' => $data['categoria_id'],
            ':temporada_id' => $data['temporada_id'],
            ':rol' => $data['rol'] ?? 'arbitro',
            ':num_arbitros' => $data['num_arbitros'] ?? 1,
            ':num_oficiales' => $data['num_oficiales'] ?? 1,
            ':usa_tablet' => isset($data['usa_tablet']) ? 1 : 0,
            ':importe' => $data['importe'] ?? 0.00
        ]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tarifas WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

