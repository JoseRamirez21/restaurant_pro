<?php
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Mesa.php';

class Pedido {

    public static function crear(array $datos): int {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO pedidos (mesa_id, usuario_id, personas, observaciones, estado)
                VALUES (:mesa_id, :usuario_id, :personas, :observaciones, 'abierto')";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':mesa_id'       => $datos['mesa_id'],
            ':usuario_id'    => $datos['usuario_id'],
            ':personas'      => $datos['personas']      ?? 1,
            ':observaciones' => $datos['observaciones'] ?? null,
        ]);
        $id = (int) $db->lastInsertId();
        Mesa::cambiarEstado($datos['mesa_id'], 'ocupada');
        return $id;
    }

    public static function buscarPorId(int $id): array|false {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.*, m.numero AS mesa_numero, m.zona,
                        u.nombre AS mesero_nombre
                FROM pedidos p
                JOIN mesas m    ON p.mesa_id    = m.id
                JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function buscarActivoPorMesa(int $mesa_id): array|false {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM pedidos
                WHERE mesa_id = :mesa_id AND estado IN ('abierto','en_cocina','listo')
                ORDER BY creado_en DESC LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':mesa_id' => $mesa_id]);
        return $stmt->fetch();
    }

    public static function detalle(int $pedido_id): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT dp.*, pr.nombre AS producto_nombre, pr.tiempo_prep_min
                FROM detalle_pedidos dp
                JOIN productos pr ON dp.producto_id = pr.id
                WHERE dp.pedido_id = :pedido_id
                ORDER BY dp.creado_en ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':pedido_id' => $pedido_id]);
        return $stmt->fetchAll();
    }

    public static function agregarProducto(int $pedido_id, int $producto_id, int $cantidad = 1, string $obs = ''): bool {
        $db = Database::getInstance()->getConnection();

        $check = $db->prepare("SELECT id, cantidad FROM detalle_pedidos WHERE pedido_id = :pid AND producto_id = :prod AND estado = 'pendiente'");
        $check->execute([':pid' => $pedido_id, ':prod' => $producto_id]);
        $existe = $check->fetch();

        if ($existe) {
            $stmt = $db->prepare("UPDATE detalle_pedidos SET cantidad = cantidad + :cant, subtotal = precio_unitario * (cantidad + :cant2) WHERE id = :id");
            $ok = $stmt->execute([':cant' => $cantidad, ':cant2' => $cantidad, ':id' => $existe['id']]);
        } else {
            $prod = Producto::buscarPorId($producto_id);
            if (!$prod) return false;
            $subtotal = $prod['precio'] * $cantidad;
            $sql = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario, subtotal, observaciones)
                    VALUES (:pedido_id, :producto_id, :cantidad, :precio, :subtotal, :obs)";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute([
                ':pedido_id'   => $pedido_id,
                ':producto_id' => $producto_id,
                ':cantidad'    => $cantidad,
                ':precio'      => $prod['precio'],
                ':subtotal'    => $subtotal,
                ':obs'         => $obs,
            ]);
        }

        self::recalcularTotales($pedido_id);
        self::cambiarEstado($pedido_id, 'en_cocina');
        return $ok;
    }

    public static function quitarProducto(int $detalle_id, int $pedido_id): bool {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM detalle_pedidos WHERE id = :id");
        $ok = $stmt->execute([':id' => $detalle_id]);
        self::recalcularTotales($pedido_id);
        return $ok;
    }

    public static function recalcularTotales(int $pedido_id): void {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT SUM(subtotal) AS subtotal FROM detalle_pedidos WHERE pedido_id = :id AND estado != 'cancelado'");
        $stmt->execute([':id' => $pedido_id]);
        $subtotal = (float) ($stmt->fetch()['subtotal'] ?? 0);
        $igv      = round($subtotal * 0.18, 2);
        $servicio = round($subtotal * 0.10, 2);
        $total    = round($subtotal + $igv + $servicio, 2);

        $upd = $db->prepare("UPDATE pedidos SET subtotal = :sub, igv = :igv, servicio = :srv, total = :total WHERE id = :id");
        $upd->execute([':sub' => $subtotal, ':igv' => $igv, ':srv' => $servicio, ':total' => $total, ':id' => $pedido_id]);
    }

    public static function cambiarEstado(int $id, string $estado): bool {
        $db = Database::getInstance()->getConnection();
        $extra = $estado === 'cerrado' ? ", cerrado_en = NOW()" : "";
        $stmt = $db->prepare("UPDATE pedidos SET estado = :estado $extra WHERE id = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public static function cerrar(int $id): bool {
        $pedido = self::buscarPorId($id);
        if (!$pedido) return false;
        self::cambiarEstado($id, 'cerrado');
        Mesa::cambiarEstado($pedido['mesa_id'], 'libre');
        return true;
    }

    public static function pendientesCocina(): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.id AS pedido_id, m.numero AS mesa_numero, m.zona,
                        dp.id AS detalle_id, dp.cantidad, dp.observaciones, dp.estado AS item_estado,
                        dp.creado_en, pr.nombre AS producto_nombre, pr.tiempo_prep_min,
                        u.nombre AS mesero_nombre
                FROM detalle_pedidos dp
                JOIN pedidos p    ON dp.pedido_id   = p.id
                JOIN mesas m      ON p.mesa_id      = m.id
                JOIN productos pr ON dp.producto_id = pr.id
                JOIN usuarios u   ON p.usuario_id   = u.id
                WHERE dp.estado IN ('pendiente','en_preparacion')
                ORDER BY dp.creado_en ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function ventasHoy(): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT
                    COUNT(*)                        AS total_pedidos,
                    COALESCE(SUM(total), 0)         AS total_ventas,
                    COALESCE(SUM(servicio), 0)      AS total_propinas
                FROM pedidos
                WHERE estado = 'cerrado'
                AND DATE(cerrado_en) = CURDATE()";
        return $db->query($sql)->fetch();
    }

    public static function actualizarEstadoItem(int $detalle_id, string $estado): bool {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE detalle_pedidos SET estado = :estado WHERE id = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $detalle_id]);
    }

    // Mesas con pedido activo pendientes de cobro
    public static function mesasPendientesCobro(): array {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.*, m.numero AS mesa_numero, m.zona, m.capacidad,
                        u.nombre AS mesero_nombre,
                        TIMESTAMPDIFF(MINUTE, p.creado_en, NOW()) AS minutos_abierto
                FROM pedidos p
                JOIN mesas m    ON p.mesa_id    = m.id
                JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.estado IN ('abierto','en_cocina','listo')
                ORDER BY p.creado_en ASC";
        return $db->query($sql)->fetchAll();
    }

    // Cobrar pedido
    public static function cobrar(int $id, string $metodo_pago, float $monto_pagado, float $propina = 0): bool {
        $db = Database::getInstance()->getConnection();

        // Agregar propina al servicio si hay
        if ($propina > 0) {
            $upd = $db->prepare("UPDATE pedidos SET servicio = servicio + :propina, total = total + :propina2 WHERE id = :id");
            $upd->execute([':propina' => $propina, ':propina2' => $propina, ':id' => $id]);
        }

        $stmt = $db->prepare("UPDATE pedidos SET
                                estado       = 'cerrado',
                                cerrado_en   = NOW(),
                                metodo_pago  = :metodo,
                                monto_pagado = :monto
                              WHERE id = :id");
        $ok = $stmt->execute([
            ':metodo' => $metodo_pago,
            ':monto'  => $monto_pagado,
            ':id'     => $id,
        ]);

        // Liberar mesa
        $pedido = self::buscarPorId($id);
        if ($pedido) Mesa::cambiarEstado($pedido['mesa_id'], 'libre');

        return $ok;
    }
}