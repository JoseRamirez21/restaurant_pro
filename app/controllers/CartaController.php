<?php
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Categoria.php';
require_once BASE_PATH . '/app/models/Mesa.php';

class CartaController {

    // Carta pública — accesible sin login por QR
    public function index($mesa_id = null) {
        $categorias = Categoria::todas();
        $productos  = Producto::disponibles();
        $mesa       = $mesa_id ? Mesa::buscarPorId((int)$mesa_id) : null;
        require_once APP_PATH . '/views/carta/carta_publica.php';
    }

    // Vista QR en el panel admin
    public function qr($param = null) {
        requireRol('administrador', 'supervisor');
        $mesas      = Mesa::todas();
        $page_title = 'Códigos QR de mesas';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/qr_mesas.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }
}