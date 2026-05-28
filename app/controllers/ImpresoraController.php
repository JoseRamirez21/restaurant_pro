<?php
require_once BASE_PATH . '/app/models/Impresora.php';
require_once BASE_PATH . '/app/models/Pedido.php';

class ImpresoraController {

    // Imprimir boleta desde caja (AJAX)
    public function boleta($pedido_id = null) {
        requireRol('administrador', 'supervisor', 'cajero');
        header('Content-Type: application/json');

        $pedido  = Pedido::buscarPorId((int)$pedido_id);
        $detalle = Pedido::detalle((int)$pedido_id);

        if (!$pedido) {
            echo json_encode(['ok' => false, 'error' => 'Pedido no encontrado']);
            exit;
        }

        $resultado = Impresora::imprimirBoleta($pedido, $detalle);
        echo json_encode($resultado);
        exit;
    }

    // Imprimir comanda desde POS del mozo (AJAX)
    public function comanda($pedido_id = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        header('Content-Type: application/json');

        $pedido  = Pedido::buscarPorId((int)$pedido_id);
        $detalle = Pedido::detalle((int)$pedido_id);

        if (!$pedido) {
            echo json_encode(['ok' => false, 'error' => 'Pedido no encontrado']);
            exit;
        }

        $resultado = Impresora::imprimirComanda($pedido, $detalle);
        echo json_encode($resultado);
        exit;
    }

    // Página de configuración y prueba
    public function configurar($param = null) {
        requireRol('administrador');
        $page_title = 'Configuración de impresora';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/impresora_config.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    // Test de impresión
    public function test($param = null) {
        requireRol('administrador');
        header('Content-Type: application/json');

        // Datos de prueba
        $pedido  = ['id'=>999, 'mesa_numero'=>1, 'mesero_nombre'=>'Test', 'personas'=>2, 'subtotal'=>50.00, 'metodo_pago'=>'efectivo'];
        $detalle = [
            ['producto_nombre'=>'Lomo saltado',    'cantidad'=>1, 'subtotal'=>55.00],
            ['producto_nombre'=>'Chicha morada',   'cantidad'=>2, 'subtotal'=>36.00],
            ['producto_nombre'=>'Suspiro limeño',  'cantidad'=>1, 'subtotal'=>22.00],
        ];

        $resultado = Impresora::imprimirBoleta($pedido, $detalle);
        echo json_encode($resultado);
        exit;
    }
}