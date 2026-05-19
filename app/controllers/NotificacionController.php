<?php
class NotificacionController {

    // Polling — el mozo consulta si hay items listos en su pedido
    public function estado($param = null) {
        requireAuth();
        header('Content-Type: application/json');
        $db = Database::getInstance()->getConnection();

        $usuario_id = (int)$_SESSION['usuario_id'];
        $rol        = $_SESSION['rol'];

        if ($rol === 'mesero' || $rol === 'administrador' || $rol === 'supervisor') {
            // Notificaciones para el mozo: items listos en sus pedidos
            $stmt = $db->prepare("
                SELECT
                    dp.id          AS detalle_id,
                    dp.estado      AS item_estado,
                    p.id           AS pedido_id,
                    m.numero       AS mesa_numero,
                    m.nombre       AS mesa_nombre,
                    pr.nombre      AS producto_nombre,
                    dp.cantidad
                FROM detalle_pedidos dp
                JOIN pedidos p    ON dp.pedido_id    = p.id
                JOIN mesas m      ON p.mesa_id       = m.id
                JOIN productos pr ON dp.producto_id  = pr.id
                WHERE dp.estado = 'listo'
                AND p.estado NOT IN ('cerrado','anulado')
                AND p.usuario_id = :uid
                ORDER BY dp.actualizado_en DESC
                LIMIT 10
            ");
            $stmt->execute([':uid' => $usuario_id]);
            $notifs = $stmt->fetchAll();

            // Contar items pendientes en cocina
            $pend = $db->prepare("
                SELECT COUNT(*) AS total
                FROM detalle_pedidos dp
                JOIN pedidos p ON dp.pedido_id = p.id
                WHERE dp.estado IN ('pendiente','en_preparacion')
                AND p.usuario_id = :uid
                AND p.estado NOT IN ('cerrado','anulado')
            ");
            $pend->execute([':uid' => $usuario_id]);
            $pendientes = (int)$pend->fetch()['total'];

            echo json_encode([
                'ok'         => true,
                'rol'        => $rol,
                'notifs'     => $notifs,
                'pendientes' => $pendientes,
                'hora'       => date('H:i:s'),
            ]);

        } elseif ($rol === 'cocinero') {
            // Para cocina: contar comandas pendientes nuevas
            $stmt = $db->query("
                SELECT COUNT(*) AS total
                FROM detalle_pedidos
                WHERE estado IN ('pendiente','en_preparacion')
            ");
            $total = (int)$stmt->fetch()['total'];

            echo json_encode([
                'ok'         => true,
                'rol'        => $rol,
                'pendientes' => $total,
                'hora'       => date('H:i:s'),
            ]);
        } else {
            echo json_encode(['ok' => true, 'notifs' => [], 'pendientes' => 0]);
        }
        exit;
    }

    // Marcar notificación como vista (item entregado)
    public function entregar($detalle_id = null) {
        requireAuth();
        header('Content-Type: application/json');
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE detalle_pedidos SET estado = 'entregado' WHERE id = :id");
        $ok   = $stmt->execute([':id' => (int)$detalle_id]);
        echo json_encode(['ok' => $ok]);
        exit;
    }
}