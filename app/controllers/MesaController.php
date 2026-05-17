<?php
require_once BASE_PATH . '/app/models/Mesa.php';
require_once BASE_PATH . '/app/models/Pedido.php';
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Categoria.php';

class MesaController {

    // Vista principal — plano de mesas
    public function index($param = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        $mesas          = Mesa::todas();
        $resumen        = Mesa::resumenEstados();
        $mesas_ocupadas = $resumen['ocupada'] ?? 0;
        $esAdmin        = authRol('administrador', 'supervisor');
        $page_title     = 'Mesas';

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/mozo/mesas_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/mozo/mesas.php';
        }
    }

    // Abrir mesa para tomar pedido
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
        $page_title = 'Abrir — ' . $mesa['nombre'];

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/mozo/abrir_mesa_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/mozo/abrir_mesa.php';
        }
    }

    // CRUD — listar mesas para admin
    public function gestionar($param = null) {
        requireRol('administrador', 'supervisor');
        $mesas      = Mesa::todasAdmin();
        $page_title = 'Gestión de mesas';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/mesas_crud.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    // CRUD — formulario nueva mesa
    public function nueva($param = null) {
        requireRol('administrador', 'supervisor');
        $mesa       = null;
        $page_title = 'Nueva mesa';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/mesa_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    // CRUD — guardar nueva mesa
    public function guardar($param = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/mesas/gestionar'); exit;
        }
        Mesa::crear([
            'numero'    => (int)$_POST['numero'],
            'nombre'    => trim($_POST['nombre']),
            'capacidad' => (int)$_POST['capacidad'],
            'zona'      => $_POST['zona'],
        ]);
        header('Location: ' . APP_URL . '/mesas/gestionar'); exit;
    }

    // CRUD — formulario editar mesa
    public function editar($mesa_id = null) {
        requireRol('administrador', 'supervisor');
        $mesa = Mesa::buscarPorId((int)$mesa_id);
        if (!$mesa) { header('Location: ' . APP_URL . '/mesas/gestionar'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Mesa::actualizar((int)$mesa_id, [
                'numero'    => (int)$_POST['numero'],
                'nombre'    => trim($_POST['nombre']),
                'capacidad' => (int)$_POST['capacidad'],
                'zona'      => $_POST['zona'],
            ]);
            header('Location: ' . APP_URL . '/mesas/gestionar'); exit;
        }

        $page_title = 'Editar — ' . $mesa['nombre'];
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/mesa_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    // CRUD — cambiar estado manualmente
    public function estado($mesa_id = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Mesa::cambiarEstado((int)$mesa_id, $_POST['estado']);
        }
        header('Location: ' . APP_URL . '/mesas/gestionar'); exit;
    }

    // CRUD — eliminar (desactivar) mesa
    public function eliminar($mesa_id = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Mesa::eliminar((int)$mesa_id);
        }
        header('Location: ' . APP_URL . '/mesas/gestionar'); exit;
    }
}