<?php
class Producto {

    public static function todos(): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.*, c.nombre AS categoria_nombre, c.color AS categoria_color
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                ORDER BY c.orden ASC, p.nombre ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function disponibles(): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.*, c.nombre AS categoria_nombre, c.color AS categoria_color, c.icono AS categoria_icono
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.disponible = 1 AND c.activo = 1
                ORDER BY c.orden ASC, p.nombre ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function porCategoria(int $categoria_id): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM productos
                WHERE categoria_id = :cid AND disponible = 1
                ORDER BY nombre ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':cid' => $categoria_id]);
        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): array|false {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function crear(array $datos): int {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO productos
                    (categoria_id, nombre, descripcion, precio, costo, imagen, alergenos, disponible, destacado, tiempo_prep_min)
                VALUES
                    (:categoria_id, :nombre, :descripcion, :precio, :costo, :imagen, :alergenos, :disponible, :destacado, :tiempo_prep_min)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':categoria_id'    => $datos['categoria_id'],
            ':nombre'          => $datos['nombre'],
            ':descripcion'     => $datos['descripcion']     ?? null,
            ':precio'          => $datos['precio'],
            ':costo'           => $datos['costo']           ?? 0,
            ':imagen'          => $datos['imagen']          ?? null,
            ':alergenos'       => $datos['alergenos']       ?? null,
            ':disponible'      => $datos['disponible']      ?? 1,
            ':destacado'       => $datos['destacado']       ?? 0,
            ':tiempo_prep_min' => $datos['tiempo_prep_min'] ?? 10,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): bool {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE productos SET
                    categoria_id    = :categoria_id,
                    nombre          = :nombre,
                    descripcion     = :descripcion,
                    precio          = :precio,
                    costo           = :costo,
                    alergenos       = :alergenos,
                    disponible      = :disponible,
                    destacado       = :destacado,
                    tiempo_prep_min = :tiempo_prep_min
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':categoria_id'    => $datos['categoria_id'],
            ':nombre'          => $datos['nombre'],
            ':descripcion'     => $datos['descripcion']     ?? null,
            ':precio'          => $datos['precio'],
            ':costo'           => $datos['costo']           ?? 0,
            ':alergenos'       => $datos['alergenos']       ?? null,
            ':disponible'      => $datos['disponible']      ?? 1,
            ':destacado'       => $datos['destacado']       ?? 0,
            ':tiempo_prep_min' => $datos['tiempo_prep_min'] ?? 10,
            ':id'              => $id,
        ]);
    }

    public static function toggleDisponible(int $id): bool {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE productos SET disponible = NOT disponible WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function eliminar(int $id): bool {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM productos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}