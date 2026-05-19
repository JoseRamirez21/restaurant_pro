<?php
require_once BASE_PATH . '/app/models/Categoria.php';

class CategoriaController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        $categorias = Categoria::todasConConteo();
        $page_title = 'Categorías';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/categorias_contenido.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function nueva($param = null) {
        requireRol('administrador', 'supervisor');
        $categoria  = null;
        $error      = '';
        $page_title = 'Nueva categoría';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/categoria_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function crear($param = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/categorias'); exit;
        }
        Categoria::crear([
            'nombre'      => trim($_POST['nombre']),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'icono'       => trim($_POST['icono']       ?? 'bi-grid'),
            'color'       => trim($_POST['color']       ?? '#6c757d'),
            'orden'       => (int)($_POST['orden']      ?? 0),
        ]);
        header('Location: ' . APP_URL . '/categorias'); exit;
    }

    public function editar($id = null) {
        requireRol('administrador', 'supervisor');
        $categoria = Categoria::buscarPorId((int)$id);
        if (!$categoria) { header('Location: ' . APP_URL . '/categorias'); exit; }

        $error      = '';
        $page_title = 'Editar — ' . $categoria['nombre'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Categoria::actualizar((int)$id, [
                'nombre'      => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'icono'       => trim($_POST['icono']       ?? 'bi-grid'),
                'color'       => trim($_POST['color']       ?? '#6c757d'),
                'orden'       => (int)($_POST['orden']      ?? 0),
            ]);
            header('Location: ' . APP_URL . '/categorias'); exit;
        }

        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/categoria_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function toggle($id = null) {
        requireRol('administrador', 'supervisor');
        Categoria::toggleActivo((int)$id);
        header('Location: ' . APP_URL . '/categorias'); exit;
    }

    public function eliminar($id = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Categoria::eliminar((int)$id);
        }
        header('Location: ' . APP_URL . '/categorias'); exit;
    }
}