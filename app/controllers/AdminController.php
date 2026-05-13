<?php
class AdminController {
 
    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        $titulo = 'Panel de administración';
        require_once APP_PATH . '/views/admin/dashboard.php';
    }
}
 