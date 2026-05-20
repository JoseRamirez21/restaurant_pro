<?php
require_once BASE_PATH . '/app/models/Cliente.php';

class ClienteController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        $clientes   = Cliente::todos();
        $stats      = Cliente::estadisticas();
        $page_title = 'Clientes & CRM';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/clientes_contenido.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function ver($id = null) {
        requireRol('administrador', 'supervisor');
        $cliente    = Cliente::buscarPorId((int)$id);
        if (!$cliente) { header('Location: ' . APP_URL . '/clientes'); exit; }
        $historial  = Cliente::historialVisitas((int)$id);
        $page_title = $cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '');
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/cliente_detalle.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function nuevo($param = null) {
        requireRol('administrador', 'supervisor');
        $cliente    = null;
        $error      = '';
        $page_title = 'Nuevo cliente';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/cliente_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function crear($param = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/clientes'); exit;
        }
        Cliente::crear([
            'nombre'    => trim($_POST['nombre']),
            'apellido'  => trim($_POST['apellido']  ?? ''),
            'telefono'  => trim($_POST['telefono']  ?? ''),
            'email'     => trim($_POST['email']      ?? ''),
            'fecha_nac' => $_POST['fecha_nac']       ?? null,
            'notas'     => trim($_POST['notas']      ?? ''),
        ]);
        header('Location: ' . APP_URL . '/clientes'); exit;
    }

    public function editar($id = null) {
        requireRol('administrador', 'supervisor');
        $cliente = Cliente::buscarPorId((int)$id);
        if (!$cliente) { header('Location: ' . APP_URL . '/clientes'); exit; }
        $error      = '';
        $page_title = 'Editar — ' . $cliente['nombre'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Cliente::actualizar((int)$id, [
                'nombre'    => trim($_POST['nombre']),
                'apellido'  => trim($_POST['apellido']  ?? ''),
                'telefono'  => trim($_POST['telefono']  ?? ''),
                'email'     => trim($_POST['email']      ?? ''),
                'fecha_nac' => $_POST['fecha_nac']       ?? null,
                'notas'     => trim($_POST['notas']      ?? ''),
            ]);
            header('Location: ' . APP_URL . '/clientes'); exit;
        }

        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/cliente_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function eliminar($id = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Cliente::eliminar((int)$id);
        }
        header('Location: ' . APP_URL . '/clientes'); exit;
    }

    // Búsqueda AJAX
    public function buscar($param = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        echo json_encode(Cliente::buscar($q));
        exit;
    }
}