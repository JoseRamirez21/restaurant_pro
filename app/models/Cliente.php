<?php
class Cliente {

    public static function todos(): array {
        $db = Database::getInstance()->getConnection();
        return $db->query("
            SELECT * FROM clientes
            WHERE activo = 1
            ORDER BY visitas DESC, nombre ASC
        ")->fetchAll();
    }

    public static function buscarPorId(int $id): array|false {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM clientes WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function buscar(string $q): array {
        $db   = Database::getInstance()->getConnection();
        $like = '%' . $q . '%';
        $stmt = $db->prepare("
            SELECT * FROM clientes
            WHERE activo = 1
            AND (nombre LIKE :q OR apellido LIKE :q2 OR telefono LIKE :q3 OR email LIKE :q4)
            ORDER BY visitas DESC LIMIT 20
        ");
        $stmt->execute([':q'=>$like,':q2'=>$like,':q3'=>$like,':q4'=>$like]);
        return $stmt->fetchAll();
    }

    public static function crear(array $datos): int {
        $db  = Database::getInstance()->getConnection();
        $sql = "INSERT INTO clientes (nombre, apellido, telefono, email, fecha_nac, notas)
                VALUES (:nombre, :apellido, :telefono, :email, :fecha_nac, :notas)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nombre'    => $datos['nombre'],
            ':apellido'  => $datos['apellido']  ?? null,
            ':telefono'  => $datos['telefono']  ?? null,
            ':email'     => $datos['email']      ?? null,
            ':fecha_nac' => $datos['fecha_nac']  ?? null,
            ':notas'     => $datos['notas']      ?? null,
        ]);
        return (int)$db->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): bool {
        $db  = Database::getInstance()->getConnection();
        $sql = "UPDATE clientes SET
                    nombre    = :nombre,
                    apellido  = :apellido,
                    telefono  = :telefono,
                    email     = :email,
                    fecha_nac = :fecha_nac,
                    notas     = :notas
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':nombre'    => $datos['nombre'],
            ':apellido'  => $datos['apellido']  ?? null,
            ':telefono'  => $datos['telefono']  ?? null,
            ':email'     => $datos['email']      ?? null,
            ':fecha_nac' => $datos['fecha_nac']  ?? null,
            ':notas'     => $datos['notas']      ?? null,
            ':id'        => $id,
        ]);
    }

    public static function registrarVisita(int $cliente_id, float $monto, int $pedido_id = null): bool {
        $db = Database::getInstance()->getConnection();

        // Registrar visita
        $stmt = $db->prepare("
            INSERT INTO visitas_cliente (cliente_id, pedido_id, fecha, monto)
            VALUES (:cid, :pid, CURDATE(), :monto)
        ");
        $stmt->execute([':cid'=>$cliente_id, ':pid'=>$pedido_id, ':monto'=>$monto]);

        // Actualizar totales y puntos (1 punto por cada S/10)
        $puntos = (int)floor($monto / 10);
        $upd = $db->prepare("
            UPDATE clientes SET
                visitas       = visitas + 1,
                total_gastado = total_gastado + :monto,
                puntos        = puntos + :puntos
            WHERE id = :id
        ");
        return $upd->execute([':monto'=>$monto, ':puntos'=>$puntos, ':id'=>$cliente_id]);
    }

    public static function historialVisitas(int $id): array {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT v.*, p.total AS pedido_total, m.numero AS mesa_numero
            FROM visitas_cliente v
            LEFT JOIN pedidos p ON v.pedido_id = p.id
            LEFT JOIN mesas m   ON p.mesa_id   = m.id
            WHERE v.cliente_id = :id
            ORDER BY v.fecha DESC LIMIT 20
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    public static function eliminar(int $id): bool {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE clientes SET activo = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function estadisticas(): array {
        $db = Database::getInstance()->getConnection();
        return $db->query("
            SELECT
                COUNT(*)                    AS total,
                SUM(visitas >= 5)           AS frecuentes,
                COALESCE(SUM(total_gastado),0) AS total_gastado,
                COALESCE(AVG(total_gastado),0) AS promedio_gasto
            FROM clientes WHERE activo = 1
        ")->fetch();
    }
}