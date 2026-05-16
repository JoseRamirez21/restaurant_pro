<?php
class Categoria {

    public static function todas(): array {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM categorias WHERE activo = 1 ORDER BY orden ASC");
        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): array|false {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM categorias WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function crear(array $datos): int {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO categorias (nombre, descripcion, icono, color, orden, activo)
                VALUES (:nombre, :descripcion, :icono, :color, :orden, 1)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nombre'      => $datos['nombre'],
            ':descripcion' => $datos['descripcion'] ?? null,
            ':icono'       => $datos['icono']       ?? 'bi-grid',
            ':color'       => $datos['color']       ?? '#6c757d',
            ':orden'       => $datos['orden']        ?? 0,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): bool {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE categorias SET
                    nombre      = :nombre,
                    descripcion = :descripcion,
                    icono       = :icono,
                    color       = :color,
                    orden       = :orden
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':nombre'      => $datos['nombre'],
            ':descripcion' => $datos['descripcion'] ?? null,
            ':icono'       => $datos['icono']       ?? 'bi-grid',
            ':color'       => $datos['color']       ?? '#6c757d',
            ':orden'       => $datos['orden']        ?? 0,
            ':id'          => $id,
        ]);
    }

    public static function eliminar(int $id): bool {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE categorias SET activo = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}