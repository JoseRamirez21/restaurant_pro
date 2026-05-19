<?php
class Reporte {

    public static function obtener(string $desde, string $hasta): array {
        $db = Database::getInstance()->getConnection();

        // Resumen general del período
        $resumen = $db->prepare("
            SELECT
                COUNT(*)                        AS total_pedidos,
                COALESCE(SUM(total), 0)         AS total_ventas,
                COALESCE(AVG(total), 0)         AS ticket_promedio,
                COALESCE(SUM(igv), 0)           AS total_igv,
                COALESCE(SUM(servicio), 0)      AS total_servicio,
                COALESCE(SUM(subtotal), 0)      AS total_subtotal
            FROM pedidos
            WHERE estado = 'cerrado'
            AND DATE(cerrado_en) BETWEEN :desde AND :hasta
        ");
        $resumen->execute([':desde' => $desde, ':hasta' => $hasta]);
        $resumen = $resumen->fetch();

        // Ventas por día
        $por_dia = $db->prepare("
            SELECT
                DATE(cerrado_en)            AS dia,
                COUNT(*)                    AS pedidos,
                COALESCE(SUM(total), 0)     AS ventas,
                COALESCE(AVG(total), 0)     AS promedio
            FROM pedidos
            WHERE estado = 'cerrado'
            AND DATE(cerrado_en) BETWEEN :desde AND :hasta
            GROUP BY DATE(cerrado_en)
            ORDER BY dia ASC
        ");
        $por_dia->execute([':desde' => $desde, ':hasta' => $hasta]);
        $por_dia = $por_dia->fetchAll();

        // Top 10 productos más vendidos
        $top_productos = $db->prepare("
            SELECT
                pr.nombre,
                c.nombre                    AS categoria,
                c.color                     AS categoria_color,
                SUM(dp.cantidad)            AS cantidad,
                COALESCE(SUM(dp.subtotal), 0) AS monto
            FROM detalle_pedidos dp
            JOIN productos pr ON dp.producto_id = pr.id
            JOIN categorias c ON pr.categoria_id = c.id
            JOIN pedidos p    ON dp.pedido_id    = p.id
            WHERE p.estado = 'cerrado'
            AND DATE(p.cerrado_en) BETWEEN :desde AND :hasta
            GROUP BY pr.id, pr.nombre, c.nombre, c.color
            ORDER BY cantidad DESC
            LIMIT 10
        ");
        $top_productos->execute([':desde' => $desde, ':hasta' => $hasta]);
        $top_productos = $top_productos->fetchAll();

        // Ventas por categoría
        $por_categoria = $db->prepare("
            SELECT
                c.nombre                        AS categoria,
                c.color,
                SUM(dp.cantidad)                AS cantidad,
                COALESCE(SUM(dp.subtotal), 0)   AS monto
            FROM detalle_pedidos dp
            JOIN productos pr ON dp.producto_id = pr.id
            JOIN categorias c ON pr.categoria_id = c.id
            JOIN pedidos p    ON dp.pedido_id    = p.id
            WHERE p.estado = 'cerrado'
            AND DATE(p.cerrado_en) BETWEEN :desde AND :hasta
            GROUP BY c.id, c.nombre, c.color
            ORDER BY monto DESC
        ");
        $por_categoria->execute([':desde' => $desde, ':hasta' => $hasta]);
        $por_categoria = $por_categoria->fetchAll();

        // Métodos de pago
        $metodos = $db->prepare("
            SELECT
                metodo_pago,
                COUNT(*)                    AS cantidad,
                COALESCE(SUM(total), 0)     AS monto
            FROM pedidos
            WHERE estado = 'cerrado'
            AND DATE(cerrado_en) BETWEEN :desde AND :hasta
            GROUP BY metodo_pago
            ORDER BY monto DESC
        ");
        $metodos->execute([':desde' => $desde, ':hasta' => $hasta]);
        $metodos = $metodos->fetchAll();

        // Performance por mesero
        $por_mesero = $db->prepare("
            SELECT
                u.nombre                        AS mesero,
                COUNT(p.id)                     AS pedidos,
                COALESCE(SUM(p.total), 0)       AS ventas,
                COALESCE(AVG(p.total), 0)       AS promedio
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.estado = 'cerrado'
            AND DATE(p.cerrado_en) BETWEEN :desde AND :hasta
            GROUP BY u.id, u.nombre
            ORDER BY ventas DESC
        ");
        $por_mesero->execute([':desde' => $desde, ':hasta' => $hasta]);
        $por_mesero = $por_mesero->fetchAll();

        // Horas pico
        $horas_pico = $db->prepare("
            SELECT
                HOUR(cerrado_en)            AS hora,
                COUNT(*)                    AS pedidos,
                COALESCE(SUM(total), 0)     AS ventas
            FROM pedidos
            WHERE estado = 'cerrado'
            AND DATE(cerrado_en) BETWEEN :desde AND :hasta
            GROUP BY HOUR(cerrado_en)
            ORDER BY hora ASC
        ");
        $horas_pico->execute([':desde' => $desde, ':hasta' => $hasta]);
        $horas_pico = $horas_pico->fetchAll();

        return [
            'resumen'        => $resumen,
            'por_dia'        => $por_dia,
            'top_productos'  => $top_productos,
            'por_categoria'  => $por_categoria,
            'metodos'        => $metodos,
            'por_mesero'     => $por_mesero,
            'horas_pico'     => $horas_pico,
        ];
    }
}