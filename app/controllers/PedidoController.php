<?php
require_once BASE_PATH . '/app/models/Pedido.php';
require_once BASE_PATH . '/app/models/Mesa.php';
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Categoria.php';

class PedidoController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor', 'mesero', 'cajero');
        $mesas = Mesa::todas();
        require_once APP_PATH . '/views/mozo/pedidos.php';
    }

    // Ver pedido activo con carta
    public function ver($pedido_id = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        $pedido   = Pedido::buscarPorId((int)$pedido_id);
        if (!$pedido) { header('Location: ' . APP_URL . '/mesas'); exit; }
        $detalle    = Pedido::detalle((int)$pedido_id);
        $categorias = Categoria::todas();
        $productos  = Producto::disponibles();
        require_once APP_PATH . '/views/mozo/tomar_pedido.php';
    }

    // Agregar producto al pedido vía POST (AJAX o form)
    public function agregar($pedido_id = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        header('Content-Type: application/json');
        $producto_id = (int)($_POST['producto_id'] ?? 0);
        $cantidad    = (int)($_POST['cantidad']    ?? 1);
        $obs         = trim($_POST['observaciones'] ?? '');

        if (!$pedido_id || !$producto_id) {
            echo json_encode(['ok' => false, 'msg' => 'Datos incompletos']);
            exit;
        }

        $ok = Pedido::agregarProducto((int)$pedido_id, $producto_id, $cantidad, $obs);
        $pedido = Pedido::buscarPorId((int)$pedido_id);
        echo json_encode([
            'ok'      => $ok,
            'detalle' => Pedido::detalle((int)$pedido_id),
            'totales' => [
                'subtotal' => number_format($pedido['subtotal'], 2),
                'igv'      => number_format($pedido['igv'], 2),
                'servicio' => number_format($pedido['servicio'], 2),
                'total'    => number_format($pedido['total'], 2),
            ]
        ]);
        exit;
    }

    // Quitar producto del pedido
    public function quitar($detalle_id = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        header('Content-Type: application/json');
        $pedido_id = (int)($_POST['pedido_id'] ?? 0);
        $ok = Pedido::quitarProducto((int)$detalle_id, $pedido_id);
        $pedido = Pedido::buscarPorId($pedido_id);
        echo json_encode([
            'ok'      => $ok,
            'detalle' => Pedido::detalle($pedido_id),
            'totales' => [
                'subtotal' => number_format($pedido['subtotal'], 2),
                'igv'      => number_format($pedido['igv'], 2),
                'servicio' => number_format($pedido['servicio'], 2),
                'total'    => number_format($pedido['total'], 2),
            ]
        ]);
        exit;
    }

    // Cerrar pedido (pasar a caja)
    public function cerrar($pedido_id = null) {
        requireRol('administrador', 'supervisor', 'cajero');
        Pedido::cerrar((int)$pedido_id);
        header('Location: ' . APP_URL . '/mesas');
        exit;
    }
}