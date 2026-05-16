<?php
require_once BASE_PATH . '/app/models/Pedido.php';

class CocinaController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor', 'cocinero');
        $comandas          = Pedido::pendientesCocina();
        $cocina_pendientes = count($comandas);
        $esAdmin           = authRol('administrador', 'supervisor');
        $page_title        = 'Cocina — KDS';

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/cocina/cocina_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/cocina/cocina.php';
        }
    }

    public function estado($detalle_id = null) {
        requireRol('administrador', 'supervisor', 'cocinero');
        header('Content-Type: application/json');
        $estado = trim($_POST['estado'] ?? '');
        $estados_validos = ['en_preparacion', 'listo'];
        if (!in_array($estado, $estados_validos)) {
            echo json_encode(['ok' => false, 'msg' => 'Estado inválido']);
            exit;
        }
        $ok = Pedido::actualizarEstadoItem((int)$detalle_id, $estado);
        echo json_encode(['ok' => $ok]);
        exit;
    }
}