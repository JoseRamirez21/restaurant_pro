<?php
require_once BASE_PATH . '/app/models/Dashboard.php';
require_once BASE_PATH . '/app/models/Mesa.php';
require_once BASE_PATH . '/app/models/Pedido.php';

class AdminController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        $datos = Dashboard::resumen();
        require_once APP_PATH . '/views/admin/dashboard.php';
    }
}