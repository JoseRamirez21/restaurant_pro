<?php
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Categoria.php';

class ProductoController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        $productos  = Producto::todos();
        $categorias = Categoria::todas();
        $page_title = 'Carta & Menú';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/productos_contenido.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function crear($param = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/productos'); exit;
        }
        Producto::crear([
            'categoria_id'    => (int)$_POST['categoria_id'],
            'nombre'          => trim($_POST['nombre']),
            'descripcion'     => trim($_POST['descripcion']     ?? ''),
            'precio'          => (float)$_POST['precio'],
            'costo'           => (float)($_POST['costo']        ?? 0),
            'alergenos'       => trim($_POST['alergenos']       ?? ''),
            'disponible'      => isset($_POST['disponible'])    ? 1 : 0,
            'destacado'       => isset($_POST['destacado'])     ? 1 : 0,
            'tiempo_prep_min' => (int)($_POST['tiempo_prep_min'] ?? 10),
        ]);
        header('Location: ' . APP_URL . '/productos'); exit;
    }

    public function editar($id = null) {
        requireRol('administrador', 'supervisor');
        $producto   = Producto::buscarPorId((int)$id);
        if (!$producto) { header('Location: ' . APP_URL . '/productos'); exit; }
        $categorias = Categoria::todas();
        $page_title = 'Editar — ' . $producto['nombre'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Producto::actualizar((int)$id, [
                'categoria_id'    => (int)$_POST['categoria_id'],
                'nombre'          => trim($_POST['nombre']),
                'descripcion'     => trim($_POST['descripcion']      ?? ''),
                'precio'          => (float)$_POST['precio'],
                'costo'           => (float)($_POST['costo']         ?? 0),
                'alergenos'       => trim($_POST['alergenos']        ?? ''),
                'disponible'      => isset($_POST['disponible'])     ? 1 : 0,
                'destacado'       => isset($_POST['destacado'])      ? 1 : 0,
                'tiempo_prep_min' => (int)($_POST['tiempo_prep_min'] ?? 10),
            ]);
            header('Location: ' . APP_URL . '/productos'); exit;
        }

        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/producto_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function nuevo($param = null) {
        requireRol('administrador', 'supervisor');
        $producto   = null;
        $categorias = Categoria::todas();
        $page_title = 'Nuevo plato';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/producto_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function toggle($id = null) {
        requireRol('administrador', 'supervisor');
        Producto::toggleDisponible((int)$id);
        header('Location: ' . APP_URL . '/productos'); exit;
    }

    public function eliminar($id = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Producto::eliminar((int)$id);
        }
        header('Location: ' . APP_URL . '/productos'); exit;
    }
}