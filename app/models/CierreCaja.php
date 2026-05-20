<?php
class CierreCaja {

    public static function existeHoy(): array|false {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM cierres_caja WHERE fecha = CURDATE() LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function generarResumen(): array {
        $db  = Database::getInstance()->getConnection();

        // Totales por método de pago
        $metodos = $db->query("
            SELECT
                metodo_pago,
                COUNT(*)            AS cantidad,
                COALESCE(SUM(total),0) AS monto
            FROM pedidos
            WHERE estado = 'cerrado' AND DATE(cerrado_en) = CURDATE()
            GROUP BY metodo_pago
        ")->fetchAll();

        $totales = [
            'efectivo'      => 0,
            'tarjeta'       => 0,
            'yape'          => 0,
            'plin'          => 0,
            'transferencia' => 0,
        ];
        $total_pedidos = 0;
        $total_ventas  = 0;

        foreach ($metodos as $m) {
            $totales[$m['metodo_pago']] = (float)$m['monto'];
            $total_pedidos += (int)$m['cantidad'];
            $total_ventas  += (float)$m['monto'];
        }

        // Propinas
        $propinas = $db->query("
            SELECT COALESCE(SUM(servicio),0) AS total
            FROM pedidos
            WHERE estado='cerrado' AND DATE(cerrado_en)=CURDATE()
        ")->fetch()['total'];

        // Top productos del día
        $top = $db->query("
            SELECT pr.nombre, SUM(dp.cantidad) AS cant, SUM(dp.subtotal) AS monto
            FROM detalle_pedidos dp
            JOIN productos pr ON dp.producto_id = pr.id
            JOIN pedidos p    ON dp.pedido_id   = p.id
            WHERE p.estado='cerrado' AND DATE(p.cerrado_en)=CURDATE()
            GROUP BY pr.id ORDER BY cant DESC LIMIT 5
        ")->fetchAll();

        // Pedidos del día detallados
        $pedidos = $db->query("
            SELECT p.id, m.numero AS mesa, p.total, p.metodo_pago,
                   p.cerrado_en, u.nombre AS mesero
            FROM pedidos p
            JOIN mesas m    ON p.mesa_id    = m.id
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.estado='cerrado' AND DATE(p.cerrado_en)=CURDATE()
            ORDER BY p.cerrado_en ASC
        ")->fetchAll();

        return [
            'total_ventas'   => $total_ventas,
            'total_pedidos'  => $total_pedidos,
            'total_propinas' => (float)$propinas,
            'metodos'        => $totales,
            'top_productos'  => $top,
            'pedidos'        => $pedidos,
            'fecha'          => date('Y-m-d'),
        ];
    }

    public static function cerrar(array $resumen, int $usuario_id, string $observaciones = ''): bool {
        $db  = Database::getInstance()->getConnection();
        $sql = "INSERT INTO cierres_caja
                    (fecha, usuario_id, total_ventas, total_pedidos,
                     total_efectivo, total_tarjeta, total_yape, total_plin,
                     total_otros, total_propinas, observaciones)
                VALUES
                    (CURDATE(), :uid, :ventas, :pedidos,
                     :efectivo, :tarjeta, :yape, :plin,
                     :otros, :propinas, :obs)
                ON DUPLICATE KEY UPDATE
                    total_ventas   = :ventas2,
                    total_pedidos  = :pedidos2,
                    observaciones  = :obs2";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':uid'      => $usuario_id,
            ':ventas'   => $resumen['total_ventas'],
            ':pedidos'  => $resumen['total_pedidos'],
            ':efectivo' => $resumen['metodos']['efectivo'],
            ':tarjeta'  => $resumen['metodos']['tarjeta'],
            ':yape'     => $resumen['metodos']['yape'],
            ':plin'     => $resumen['metodos']['plin'],
            ':otros'    => $resumen['metodos']['transferencia'],
            ':propinas' => $resumen['total_propinas'],
            ':obs'      => $observaciones,
            ':ventas2'  => $resumen['total_ventas'],
            ':pedidos2' => $resumen['total_pedidos'],
            ':obs2'     => $observaciones,
        ]);
    }

    public static function historial(int $limite = 30): array {
        $db = Database::getInstance()->getConnection();
        return $db->query("
            SELECT c.*, u.nombre AS cajero
            FROM cierres_caja c
            LEFT JOIN usuarios u ON c.usuario_id = u.id
            ORDER BY c.fecha DESC
            LIMIT $limite
        ")->fetchAll();
    }
}