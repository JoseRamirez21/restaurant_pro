<?php
require_once BASE_PATH . '/app/models/Dashboard.php';
require_once BASE_PATH . '/app/models/Mesa.php';
require_once BASE_PATH . '/app/models/Pedido.php';

class AdminController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        $datos             = Dashboard::resumen();
        $mesas_ocupadas    = $datos['mesas']['ocupadas']  ?? 0;
        $cocina_pendientes = $datos['cocina']             ?? 0;
        $page_title        = 'Dashboard';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/dashboard_contenido.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }
}