<?php
class Dashboard {

    public static function resumen(): array {
        $db = Database::getInstance()->getConnection();

        // Ventas de hoy
        $ventas = $db->query("
            SELECT
                COUNT(*)                   AS total_pedidos,
                COALESCE(SUM(total), 0)    AS total_ventas,
                COALESCE(SUM(servicio), 0) AS total_propinas,
                COALESCE(AVG(total), 0)    AS ticket_promedio
            FROM pedidos
            WHERE estado = 'cerrado'
            AND DATE(cerrado_en) = CURDATE()
        ")->fetch();

        // Mesas
        $mesas = $db->query("
            SELECT
                COUNT(*) AS total,
                SUM(estado = 'libre')         AS libres,
                SUM(estado = 'ocupada')       AS ocupadas,
                SUM(estado = 'reservada')     AS reservadas,
                SUM(estado = 'mantenimiento') AS mantenimiento
            FROM mesas WHERE activo = 1
        ")->fetch();

        // Pedidos activos ahora
        $activos = $db->query("
            SELECT COUNT(*) AS total
            FROM pedidos
            WHERE estado IN ('abierto','en_cocina','listo')
        ")->fetch();

        // Comandas pendientes en cocina
        $cocina = $db->query("
            SELECT COUNT(*) AS total
            FROM detalle_pedidos
            WHERE estado IN ('pendiente','en_preparacion')
        ")->fetch();

        // Ventas de la semana (últimos 7 días)
        $semana = $db->query("
            SELECT
                DATE(cerrado_en)        AS dia,
                COALESCE(SUM(total), 0) AS ventas,
                COUNT(*)                AS pedidos
            FROM pedidos
            WHERE estado = 'cerrado'
            AND cerrado_en >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(cerrado_en)
            ORDER BY dia ASC
        ")->fetchAll();

        // Top 5 platos más vendidos hoy
        $top_platos = $db->query("
            SELECT
                pr.nombre,
                SUM(dp.cantidad)        AS total_vendido,
                SUM(dp.subtotal)        AS total_monto
            FROM detalle_pedidos dp
            JOIN productos pr ON dp.producto_id = pr.id
            JOIN pedidos p    ON dp.pedido_id   = p.id
            WHERE p.estado = 'cerrado'
            AND DATE(p.cerrado_en) = CURDATE()
            GROUP BY pr.id, pr.nombre
            ORDER BY total_vendido DESC
            LIMIT 5
        ")->fetchAll();

        // Top platos del mes si hoy no hay datos
        if (empty($top_platos)) {
            $top_platos = $db->query("
                SELECT
                    pr.nombre,
                    SUM(dp.cantidad)    AS total_vendido,
                    SUM(dp.subtotal)    AS total_monto
                FROM detalle_pedidos dp
                JOIN productos pr ON dp.producto_id = pr.id
                JOIN pedidos p    ON dp.pedido_id   = p.id
                WHERE p.estado = 'cerrado'
                AND MONTH(p.cerrado_en) = MONTH(CURDATE())
                GROUP BY pr.id, pr.nombre
                ORDER BY total_vendido DESC
                LIMIT 5
            ")->fetchAll();
        }

        // Métodos de pago del día
        $metodos = $db->query("
            SELECT
                metodo_pago,
                COUNT(*)             AS cantidad,
                COALESCE(SUM(total), 0) AS monto
            FROM pedidos
            WHERE estado = 'cerrado'
            AND DATE(cerrado_en) = CURDATE()
            GROUP BY metodo_pago
        ")->fetchAll();

        // Personal activo hoy (que tiene pedidos)
        $personal = $db->query("
            SELECT COUNT(DISTINCT usuario_id) AS total
            FROM pedidos
            WHERE DATE(creado_en) = CURDATE()
        ")->fetch();

        // Pedidos activos con detalle para tabla
        $pedidos_activos = $db->query("
            SELECT p.id, m.numero AS mesa_numero, m.zona,
                   u.nombre AS mesero, p.estado,
                   p.total, p.personas,
                   TIMESTAMPDIFF(MINUTE, p.creado_en, NOW()) AS minutos
            FROM pedidos p
            JOIN mesas m    ON p.mesa_id    = m.id
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.estado IN ('abierto','en_cocina','listo')
            ORDER BY p.creado_en ASC
        ")->fetchAll();

        return [
            'ventas'          => $ventas,
            'mesas'           => $mesas,
            'activos'         => (int)($activos['total'] ?? 0),
            'cocina'          => (int)($cocina['total']  ?? 0),
            'semana'          => $semana,
            'top_platos'      => $top_platos,
            'metodos'         => $metodos,
            'personal_activo' => (int)($personal['total'] ?? 0),
            'pedidos_activos' => $pedidos_activos,
        ];
    }
}