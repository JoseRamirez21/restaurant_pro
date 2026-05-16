<?php
class Mesa {

    public static function todas(): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT m.*,
                    p.id AS pedido_id,
                    p.personas,
                    p.total,
                    p.creado_en AS pedido_inicio,
                    u.nombre AS mesero_nombre
                FROM mesas m
                LEFT JOIN pedidos p ON p.mesa_id = m.id AND p.estado IN ('abierto','en_cocina','listo')
                LEFT JOIN usuarios u ON p.usuario_id = u.id
                WHERE m.activo = 1
                ORDER BY m.numero ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function buscarPorId(int $id): array|false {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT m.*,
                    p.id AS pedido_id,
                    p.personas,
                    p.total,
                    p.estado AS pedido_estado,
                    p.creado_en AS pedido_inicio
                FROM mesas m
                LEFT JOIN pedidos p ON p.mesa_id = m.id AND p.estado IN ('abierto','en_cocina','listo')
                WHERE m.id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function cambiarEstado(int $id, string $estado): bool {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE mesas SET estado = :estado WHERE id = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public static function crear(array $datos): int {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO mesas (numero, nombre, capacidad, zona, pos_x, pos_y, activo)
                VALUES (:numero, :nombre, :capacidad, :zona, :pos_x, :pos_y, 1)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':numero'    => $datos['numero'],
            ':nombre'    => $datos['nombre']    ?? 'Mesa ' . $datos['numero'],
            ':capacidad' => $datos['capacidad'] ?? 4,
            ':zona'      => $datos['zona']      ?? 'salon_principal',
            ':pos_x'     => $datos['pos_x']     ?? 0,
            ':pos_y'     => $datos['pos_y']     ?? 0,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): bool {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE mesas SET
                    numero    = :numero,
                    nombre    = :nombre,
                    capacidad = :capacidad,
                    zona      = :zona
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':numero'    => $datos['numero'],
            ':nombre'    => $datos['nombre'],
            ':capacidad' => $datos['capacidad'],
            ':zona'      => $datos['zona'],
            ':id'        => $id,
        ]);
    }

    public static function eliminar(int $id): bool {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE mesas SET activo = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function resumenEstados(): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT estado, COUNT(*) AS total FROM mesas WHERE activo = 1 GROUP BY estado";
        $rows = $db->query($sql)->fetchAll();
        $res = ['libre' => 0, 'ocupada' => 0, 'reservada' => 0, 'mantenimiento' => 0];
        foreach ($rows as $r) $res[$r['estado']] = (int)$r['total'];
        return $res;
    }
}