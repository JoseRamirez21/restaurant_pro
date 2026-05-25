<?php
class Analitica {

    public static function resumenCompleto(): array {
        $db = Database::getInstance()->getConnection();

        // Comparativa esta semana vs semana anterior
        $semana_actual = $db->query("
            SELECT COALESCE(SUM(total),0) AS ventas, COUNT(*) AS pedidos
            FROM pedidos
            WHERE estado = 'cerrado'
            AND cerrado_en >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ")->fetch();

        $semana_anterior = $db->query("
            SELECT COALESCE(SUM(total),0) AS ventas, COUNT(*) AS pedidos
            FROM pedidos
            WHERE estado = 'cerrado'
            AND cerrado_en BETWEEN DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                                AND DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ")->fetch();

        // Calcular variación porcentual
        $var_ventas  = self::variacion($semana_anterior['ventas'],  $semana_actual['ventas']);
        $var_pedidos = self::variacion($semana_anterior['pedidos'], $semana_actual['pedidos']);

        // Ventas por mes — últimos 6 meses
        $por_mes = $db->query("
            SELECT
                DATE_FORMAT(cerrado_en, '%Y-%m')    AS mes,
                DATE_FORMAT(cerrado_en, '%b %Y')    AS mes_label,
                COALESCE(SUM(total), 0)             AS ventas,
                COUNT(*)                            AS pedidos,
                COALESCE(AVG(total), 0)             AS ticket_prom
            FROM pedidos
            WHERE estado = 'cerrado'
            AND cerrado_en >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(cerrado_en, '%Y-%m')
            ORDER BY mes ASC
        ")->fetchAll();

        // Días más rentables (0=Dom, 1=Lun ... 6=Sáb)
        $por_dia_semana = $db->query("
            SELECT
                DAYOFWEEK(cerrado_en) - 1           AS dia_num,
                DAYNAME(cerrado_en)                 AS dia_nombre,
                COALESCE(SUM(total), 0)             AS ventas,
                COUNT(*)                            AS pedidos
            FROM pedidos
            WHERE estado = 'cerrado'
            AND cerrado_en >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DAYOFWEEK(cerrado_en), DAYNAME(cerrado_en)
            ORDER BY dia_num ASC
        ")->fetchAll();

        // Horas pico — últimos 30 días
        $horas_pico = $db->query("
            SELECT
                HOUR(cerrado_en)                    AS hora,
                COALESCE(SUM(total), 0)             AS ventas,
                COUNT(*)                            AS pedidos
            FROM pedidos
            WHERE estado = 'cerrado'
            AND cerrado_en >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY HOUR(cerrado_en)
            ORDER BY hora ASC
        ")->fetchAll();

        // Top platos por margen de ganancia
        $top_margen = $db->query("
            SELECT
                pr.nombre,
                pr.precio,
                pr.costo,
                (pr.precio - pr.costo)              AS ganancia,
                CASE WHEN pr.precio > 0
                    THEN ROUND(((pr.precio - pr.costo) / pr.precio) * 100, 1)
                    ELSE 0 END                      AS margen_pct,
                SUM(dp.cantidad)                    AS vendidos,
                SUM(dp.subtotal)                    AS ingresos
            FROM productos pr
            LEFT JOIN detalle_pedidos dp ON dp.producto_id = pr.id
            LEFT JOIN pedidos p          ON dp.pedido_id   = p.id
                AND p.estado = 'cerrado'
                AND p.cerrado_en >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            WHERE pr.disponible = 1
            GROUP BY pr.id, pr.nombre, pr.precio, pr.costo
            ORDER BY margen_pct DESC
            LIMIT 10
        ")->fetchAll();

        // Ranking de meseros — últimos 30 días
        $ranking_meseros = $db->query("
            SELECT
                u.nombre,
                COUNT(p.id)                         AS pedidos,
                COALESCE(SUM(p.total), 0)           AS ventas,
                COALESCE(AVG(p.total), 0)           AS ticket_prom,
                COALESCE(MAX(p.total), 0)           AS mejor_mesa
            FROM usuarios u
            LEFT JOIN pedidos p ON p.usuario_id = u.id
                AND p.estado = 'cerrado'
                AND p.cerrado_en >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            WHERE u.rol = 'mesero' AND u.activo = 1
            GROUP BY u.id, u.nombre
            ORDER BY ventas DESC
        ")->fetchAll();

        // Tendencia de clientes nuevos vs recurrentes
        $clientes_stats = $db->query("
            SELECT
                COUNT(*)                            AS total_clientes,
                SUM(visitas >= 5)                   AS frecuentes,
                SUM(visitas = 1)                    AS nuevos,
                COALESCE(AVG(total_gastado), 0)     AS gasto_promedio
            FROM clientes WHERE activo = 1
        ")->fetch();

        // Productos sin movimiento últimos 30 días
        $sin_movimiento = $db->query("
            SELECT pr.nombre, pr.precio, c.nombre AS categoria
            FROM productos pr
            JOIN categorias c ON pr.categoria_id = c.id
            WHERE pr.disponible = 1
            AND pr.id NOT IN (
                SELECT DISTINCT dp.producto_id
                FROM detalle_pedidos dp
                JOIN pedidos p ON dp.pedido_id = p.id
                WHERE p.estado = 'cerrado'
                AND p.cerrado_en >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            )
            LIMIT 8
        ")->fetchAll();

        // Método de pago más usado — últimos 30 días
        $metodos_30d = $db->query("
            SELECT
                metodo_pago,
                COUNT(*)                            AS cantidad,
                COALESCE(SUM(total), 0)             AS monto
            FROM pedidos
            WHERE estado = 'cerrado'
            AND cerrado_en >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY metodo_pago
            ORDER BY monto DESC
        ")->fetchAll();

        return [
            'semana_actual'    => $semana_actual,
            'semana_anterior'  => $semana_anterior,
            'var_ventas'       => $var_ventas,
            'var_pedidos'      => $var_pedidos,
            'por_mes'          => $por_mes,
            'por_dia_semana'   => $por_dia_semana,
            'horas_pico'       => $horas_pico,
            'top_margen'       => $top_margen,
            'ranking_meseros'  => $ranking_meseros,
            'clientes_stats'   => $clientes_stats,
            'sin_movimiento'   => $sin_movimiento,
            'metodos_30d'      => $metodos_30d,
        ];
    }

    private static function variacion(float $anterior, float $actual): array {
        if ($anterior == 0) return ['pct' => 0, 'sube' => true];
        $pct  = (($actual - $anterior) / $anterior) * 100;
        return ['pct' => round(abs($pct), 1), 'sube' => $pct >= 0];
    }
}
