<?php
class Reserva {

    public static function todas(): array {
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT r.*,
                    m.nombre AS mesa_nombre, m.numero AS mesa_numero,
                    u.nombre AS usuario_nombre
                FROM reservas r
                LEFT JOIN mesas m    ON r.mesa_id    = m.id
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                ORDER BY r.fecha ASC, r.hora ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function porFecha(string $fecha): array {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT r.*,
                    m.nombre AS mesa_nombre, m.numero AS mesa_numero
                FROM reservas r
                LEFT JOIN mesas m ON r.mesa_id = m.id
                WHERE r.fecha = :fecha
                AND r.estado NOT IN ('cancelada','no_show')
                ORDER BY r.hora ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':fecha' => $fecha]);
        return $stmt->fetchAll();
    }

    public static function proximas(int $dias = 7): array {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT r.*,
                    m.nombre AS mesa_nombre, m.numero AS mesa_numero
                FROM reservas r
                LEFT JOIN mesas m ON r.mesa_id = m.id
                WHERE r.fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                AND r.estado NOT IN ('cancelada','no_show')
                ORDER BY r.fecha ASC, r.hora ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':dias' => $dias]);
        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): array|false {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT r.*, m.nombre AS mesa_nombre, m.numero AS mesa_numero
                              FROM reservas r
                              LEFT JOIN mesas m ON r.mesa_id = m.id
                              WHERE r.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function crear(array $datos): int {
        $db  = Database::getInstance()->getConnection();
        $sql = "INSERT INTO reservas
                    (mesa_id, nombre_cliente, telefono, email_cliente, fecha, hora, personas, estado, notas, usuario_id)
                VALUES
                    (:mesa_id, :nombre, :telefono, :email, :fecha, :hora, :personas, 'pendiente', :notas, :usuario_id)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':mesa_id'    => $datos['mesa_id']    ?: null,
            ':nombre'     => $datos['nombre_cliente'],
            ':telefono'   => $datos['telefono']   ?? null,
            ':email'      => $datos['email']       ?? null,
            ':fecha'      => $datos['fecha'],
            ':hora'       => $datos['hora'],
            ':personas'   => $datos['personas']    ?? 2,
            ':notas'      => $datos['notas']       ?? null,
            ':usuario_id' => $datos['usuario_id']  ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): bool {
        $db  = Database::getInstance()->getConnection();
        $sql = "UPDATE reservas SET
                    mesa_id        = :mesa_id,
                    nombre_cliente = :nombre,
                    telefono       = :telefono,
                    email_cliente  = :email,
                    fecha          = :fecha,
                    hora           = :hora,
                    personas       = :personas,
                    notas          = :notas
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':mesa_id'  => $datos['mesa_id'] ?: null,
            ':nombre'   => $datos['nombre_cliente'],
            ':telefono' => $datos['telefono']  ?? null,
            ':email'    => $datos['email']      ?? null,
            ':fecha'    => $datos['fecha'],
            ':hora'     => $datos['hora'],
            ':personas' => $datos['personas']   ?? 2,
            ':notas'    => $datos['notas']      ?? null,
            ':id'       => $id,
        ]);
    }

    public static function cambiarEstado(int $id, string $estado): bool {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE reservas SET estado = :estado WHERE id = :id");
        $ok   = $stmt->execute([':estado' => $estado, ':id' => $id]);

        // Si se sienta al cliente, marcar mesa como reservada
        if ($ok && $estado === 'sentada') {
            $res = self::buscarPorId($id);
            if ($res && $res['mesa_id']) {
                $upd = $db->prepare("UPDATE mesas SET estado = 'reservada' WHERE id = :id");
                $upd->execute([':id' => $res['mesa_id']]);
            }
        }
        return $ok;
    }

    public static function eliminar(int $id): bool {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM reservas WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function resumenHoy(): array {
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(estado = 'pendiente')   AS pendientes,
                    SUM(estado = 'confirmada')  AS confirmadas,
                    SUM(estado = 'sentada')     AS sentadas,
                    SUM(estado = 'cancelada')   AS canceladas,
                    SUM(estado = 'no_show')     AS no_shows
                FROM reservas
                WHERE fecha = CURDATE()";
        return $db->query($sql)->fetch();
    }
}