<?php
require_once BASE_PATH . '/app/models/Pedido.php';
require_once BASE_PATH . '/app/models/Mesa.php';

class CajaController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor', 'cajero');
        $mesas_pendientes = Pedido::mesasPendientesCobro();
        $ventas_hoy       = Pedido::ventasHoy();
        $esAdmin          = authRol('administrador', 'supervisor');
        $page_title       = 'Caja';

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/caja/caja_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/caja/caja.php';
        }
    }

    public function cuenta($pedido_id = null) {
        requireRol('administrador', 'supervisor', 'cajero');
        $pedido  = Pedido::buscarPorId((int)$pedido_id);
        if (!$pedido) { header('Location: ' . APP_URL . '/caja'); exit; }
        $detalle    = Pedido::detalle((int)$pedido_id);
        $esAdmin    = authRol('administrador', 'supervisor');
        $page_title = 'Cobrar — Mesa ' . $pedido['mesa_numero'];

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/caja/cuenta_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/caja/cuenta.php';
        }
    }

    public function cobrar($pedido_id = null) {
        requireRol('administrador', 'supervisor', 'cajero');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/caja'); exit;
        }
        $pedido = Pedido::buscarPorId((int)$pedido_id);
        if (!$pedido) { header('Location: ' . APP_URL . '/caja'); exit; }

        // Cobrar usando subtotal (precio limpio sin IGV ni servicio)
        Pedido::cobrar(
            (int)$pedido_id,
            $_POST['metodo_pago']  ?? 'efectivo',
            (float)$pedido['subtotal'], // monto real cobrado
            0 // sin propina
        );
        header('Location: ' . APP_URL . '/caja/comprobante/' . $pedido_id);
        exit;
    }

    public function comprobante($pedido_id = null) {
        requireRol('administrador', 'supervisor', 'cajero');
        $pedido     = Pedido::buscarPorId((int)$pedido_id);
        $detalle    = Pedido::detalle((int)$pedido_id);
        $esAdmin    = authRol('administrador', 'supervisor');
        $page_title = 'Comprobante #' . $pedido_id;

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/caja/comprobante_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/caja/comprobante.php';
        }
    }
}