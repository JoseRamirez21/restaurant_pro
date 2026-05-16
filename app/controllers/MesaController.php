<?php
require_once BASE_PATH . '/app/models/Mesa.php';
require_once BASE_PATH . '/app/models/Pedido.php';
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Categoria.php';

class MesaController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        $mesas          = Mesa::todas();
        $esAdmin        = authRol('administrador', 'supervisor');
        $mesas_ocupadas = Mesa::resumenEstados()['ocupada'] ?? 0;
        $page_title     = 'Mesas';

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/mozo/mesas_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/mozo/mesas.php';
        }
    }

    public function abrir($mesa_id = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        $mesa = Mesa::buscarPorId((int)$mesa_id);
        if (!$mesa) { header('Location: ' . APP_URL . '/mesas'); exit; }

        if ($mesa['pedido_id']) {
            header('Location: ' . APP_URL . '/pedidos/ver/' . $mesa['pedido_id']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pedido_id = Pedido::crear([
                'mesa_id'    => $mesa_id,
                'usuario_id' => $_SESSION['usuario_id'],
                'personas'   => (int)($_POST['personas'] ?? 1),
            ]);
            header('Location: ' . APP_URL . '/pedidos/ver/' . $pedido_id);
            exit;
        }

        $esAdmin    = authRol('administrador', 'supervisor');
        $page_title = 'Abrir Mesa ' . $mesa['numero'];

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/mozo/abrir_mesa_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/mozo/abrir_mesa.php';
        }
    }
}