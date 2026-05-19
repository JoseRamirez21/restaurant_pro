<?php
require_once BASE_PATH . '/app/models/Ingrediente.php';
/** @var Reserva $reserva */
class InventarioController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        $ingredientes  = Ingrediente::todos();
        $alertas       = Ingrediente::alertas();
        $page_title    = 'Inventario';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/inventario_contenido.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function nuevo($param = null) {
        requireRol('administrador', 'supervisor');
        $ingrediente = null;
        $page_title  = 'Nuevo ingrediente';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/ingrediente_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function crear($param = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/inventario'); exit;
        }
        Ingrediente::crear([
            'nombre'        => trim($_POST['nombre']),
            'unidad'        => $_POST['unidad'],
            'stock_actual'  => (float)($_POST['stock_actual']  ?? 0),
            'stock_minimo'  => (float)($_POST['stock_minimo']  ?? 0),
            'stock_maximo'  => (float)($_POST['stock_maximo']  ?? 0),
            'costo_unitario'=> (float)($_POST['costo_unitario']?? 0),
            'proveedor'     => trim($_POST['proveedor'] ?? ''),
        ]);
        header('Location: ' . APP_URL . '/inventario'); exit;
    }

    public function editar($id = null) {
        requireRol('administrador', 'supervisor');
        $ingrediente = Ingrediente::buscarPorId((int)$id);
        if (!$ingrediente) { header('Location: ' . APP_URL . '/inventario'); exit; }
        $page_title = 'Editar — ' . $ingrediente['nombre'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Ingrediente::actualizar((int)$id, [
                'nombre'        => trim($_POST['nombre']),
                'unidad'        => $_POST['unidad'],
                'stock_minimo'  => (float)($_POST['stock_minimo']   ?? 0),
                'stock_maximo'  => (float)($_POST['stock_maximo']   ?? 0),
                'costo_unitario'=> (float)($_POST['costo_unitario'] ?? 0),
                'proveedor'     => trim($_POST['proveedor'] ?? ''),
            ]);
            header('Location: ' . APP_URL . '/inventario'); exit;
        }

        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/ingrediente_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    // Ajustar stock manualmente (entrada o salida)
    public function ajustar($id = null) {
        requireRol('administrador', 'supervisor');
        $ingrediente = Ingrediente::buscarPorId((int)$id);
        if (!$ingrediente) { header('Location: ' . APP_URL . '/inventario'); exit; }
        $page_title  = 'Ajustar stock — ' . $ingrediente['nombre'];
        $movimientos = Ingrediente::movimientos((int)$id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Ingrediente::ajustarStock(
                (int)$id,
                (float)$_POST['cantidad'],
                $_POST['tipo'],
                trim($_POST['motivo'] ?? 'Ajuste manual'),
                (int)$_SESSION['usuario_id']
            );
            header('Location: ' . APP_URL . '/inventario'); exit;
        }

        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/inventario_ajuste.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function eliminar($id = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Ingrediente::eliminar((int)$id);
        }
        header('Location: ' . APP_URL . '/inventario'); exit;
    }
}