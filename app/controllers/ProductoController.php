<?php
class ProductoController {
    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        require_once APP_PATH . '/views/admin/productos.php';
    }
}