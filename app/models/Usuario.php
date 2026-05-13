<?php
class Usuario {

    public static function buscarPorEmail(string $email): array|false {
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT id, nombre, apellido, email, password, rol, activo
                FROM usuarios
                WHERE email = :email
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public static function buscarPorId(int $id): array|false {
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT id, nombre, apellido, email, rol, activo, avatar
                FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function todos(): array {
        $db  = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, nombre, apellido, email, rol, activo FROM usuarios ORDER BY nombre");
        return $stmt->fetchAll();
    }
}