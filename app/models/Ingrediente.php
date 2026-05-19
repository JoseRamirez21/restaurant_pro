<?php
class Ingrediente {

    public static function todos(): array {
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT *,
                    CASE
                        WHEN stock_actual <= stock_minimo              THEN 'critico'
                        WHEN stock_actual <= (stock_minimo * 1.5)      THEN 'bajo'
                        ELSE 'ok'
                    END AS estado_stock
                FROM ingredientes
                WHERE activo = 1
                ORDER BY nombre ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function alertas(): array {
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT *
                FROM ingredientes
                WHERE activo = 1
                AND stock_actual <= (stock_minimo * 1.5)
                ORDER BY (stock_actual / stock_minimo) ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function buscarPorId(int $id): array|false {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM ingredientes WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function crear(array $datos): int {
        $db  = Database::getInstance()->getConnection();
        $sql = "INSERT INTO ingredientes
                    (nombre, unidad, stock_actual, stock_minimo, stock_maximo, costo_unitario, proveedor)
                VALUES
                    (:nombre, :unidad, :stock_actual, :stock_minimo, :stock_maximo, :costo, :proveedor)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nombre'      => $datos['nombre'],
            ':unidad'      => $datos['unidad'],
            ':stock_actual'=> $datos['stock_actual'] ?? 0,
            ':stock_minimo'=> $datos['stock_minimo'] ?? 0,
            ':stock_maximo'=> $datos['stock_maximo'] ?? 0,
            ':costo'       => $datos['costo_unitario'] ?? 0,
            ':proveedor'   => $datos['proveedor'] ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): bool {
        $db  = Database::getInstance()->getConnection();
        $sql = "UPDATE ingredientes SET
                    nombre         = :nombre,
                    unidad         = :unidad,
                    stock_minimo   = :stock_minimo,
                    stock_maximo   = :stock_maximo,
                    costo_unitario = :costo,
                    proveedor      = :proveedor
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':nombre'      => $datos['nombre'],
            ':unidad'      => $datos['unidad'],
            ':stock_minimo'=> $datos['stock_minimo'] ?? 0,
            ':stock_maximo'=> $datos['stock_maximo'] ?? 0,
            ':costo'       => $datos['costo_unitario'] ?? 0,
            ':proveedor'   => $datos['proveedor'] ?? null,
            ':id'          => $id,
        ]);
    }

    public static function ajustarStock(int $id, float $cantidad, string $tipo, string $motivo, int $usuario_id): bool {
        $db          = Database::getInstance()->getConnection();
        $ingrediente = self::buscarPorId($id);
        if (!$ingrediente) return false;

        $stock_anterior = (float)$ingrediente['stock_actual'];
        $stock_nuevo    = $tipo === 'entrada'
            ? $stock_anterior + $cantidad
            : max(0, $stock_anterior - $cantidad);

        // Actualizar stock
        $upd = $db->prepare("UPDATE ingredientes SET stock_actual = :stock WHERE id = :id");
        $upd->execute([':stock' => $stock_nuevo, ':id' => $id]);

        // Registrar movimiento
        $mov = $db->prepare("INSERT INTO movimientos_stock
                            (ingrediente_id, tipo, cantidad, stock_anterior, stock_nuevo, motivo, usuario_id)
                            VALUES (:ing, :tipo, :cant, :ant, :nvo, :motivo, :uid)");
        return $mov->execute([
            ':ing'    => $id,
            ':tipo'   => $tipo,
            ':cant'   => $cantidad,
            ':ant'    => $stock_anterior,
            ':nvo'    => $stock_nuevo,
            ':motivo' => $motivo,
            ':uid'    => $usuario_id,
        ]);
    }

    public static function movimientos(int $id, int $limite = 20): array {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT m.*, u.nombre AS usuario_nombre
                              FROM movimientos_stock m
                              LEFT JOIN usuarios u ON m.usuario_id = u.id
                              WHERE m.ingrediente_id = :id
                              ORDER BY m.creado_en DESC
                              LIMIT :lim");
        $stmt->bindValue(':id',  $id,     PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function totalAlertas(): int {
        $db = Database::getInstance()->getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM ingredientes WHERE activo=1 AND stock_actual <= stock_minimo * 1.5")->fetchColumn();
    }

    public static function eliminar(int $id): bool {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE ingredientes SET activo = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}