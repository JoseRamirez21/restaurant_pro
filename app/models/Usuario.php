<?php
class Usuario {

    public static function todos(): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT u.*,
                    COUNT(p.id) AS total_pedidos
                FROM usuarios u
                LEFT JOIN pedidos p ON p.usuario_id = u.id
                GROUP BY u.id
                ORDER BY u.rol ASC, u.nombre ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function buscarPorId(int $id): array|false {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function buscarPorEmail(string $email): array|false {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, nombre, apellido, email, password, rol, activo FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public static function crear(array $datos): int {
        $db  = Database::getInstance()->getConnection();
        $sql = "INSERT INTO usuarios (nombre, apellido, email, password, rol, activo)
                VALUES (:nombre, :apellido, :email, :password, :rol, 1)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nombre'   => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':email'    => $datos['email'],
            ':password' => $datos['password'],
            ':rol'      => $datos['rol'],
        ]);
        return (int) $db->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): bool {
        $db = Database::getInstance()->getConnection();

        if (isset($datos['password'])) {
            $sql = "UPDATE usuarios SET
                        nombre   = :nombre,
                        apellido = :apellido,
                        email    = :email,
                        rol      = :rol,
                        password = :password
                    WHERE id = :id";
            $params = [
                ':nombre'   => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':email'    => $datos['email'],
                ':rol'      => $datos['rol'],
                ':password' => $datos['password'],
                ':id'       => $id,
            ];
        } else {
            $sql = "UPDATE usuarios SET
                        nombre   = :nombre,
                        apellido = :apellido,
                        email    = :email,
                        rol      = :rol
                    WHERE id = :id";
            $params = [
                ':nombre'   => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':email'    => $datos['email'],
                ':rol'      => $datos['rol'],
                ':id'       => $id,
            ];
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function toggleActivo(int $id): bool {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET activo = NOT activo WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function eliminar(int $id): bool {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}