<?php
require_once BASE_PATH . '/app/models/Usuario.php';

class UsuarioController {

    public function index($param = null) {
        requireRol('administrador');
        $usuarios   = Usuario::todos();
        $page_title = 'Gestión de personal';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/usuarios_contenido.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function nuevo($param = null) {
        requireRol('administrador');
        $usuario    = null;
        $error      = '';
        $page_title = 'Nuevo usuario';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/usuario_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function crear($param = null) {
        requireRol('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/usuarios'); exit;
        }

        $error = '';

        // Validar email único
        if (Usuario::buscarPorEmail(trim($_POST['email']))) {
            $error = 'Ese correo ya está registrado.';
        }

        // Validar contraseña
        if (empty($_POST['password']) || strlen($_POST['password']) < 3) {
            $error = 'La contraseña debe tener al menos 3 caracteres.';
        }

        if ($error) {
            $usuario    = null;
            $page_title = 'Nuevo usuario';
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/admin/usuario_form.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
            return;
        }

        Usuario::crear([
            'nombre'   => trim($_POST['nombre']),
            'apellido' => trim($_POST['apellido']),
            'email'    => trim($_POST['email']),
            'password' => md5($_POST['password']),
            'rol'      => $_POST['rol'],
        ]);

        header('Location: ' . APP_URL . '/usuarios'); exit;
    }

    public function editar($id = null) {
        requireRol('administrador');
        $usuario = Usuario::buscarPorId((int)$id);
        if (!$usuario) { header('Location: ' . APP_URL . '/usuarios'); exit; }

        $error      = '';
        $page_title = 'Editar — ' . $usuario['nombre'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificar email único si cambió
            $emailExiste = Usuario::buscarPorEmail(trim($_POST['email']));
            if ($emailExiste && $emailExiste['id'] != $id) {
                $error = 'Ese correo ya está en uso por otro usuario.';
            }

            if (!$error) {
                $datos = [
                    'nombre'   => trim($_POST['nombre']),
                    'apellido' => trim($_POST['apellido']),
                    'email'    => trim($_POST['email']),
                    'rol'      => $_POST['rol'],
                ];
                // Solo actualizar contraseña si se ingresó una nueva
                if (!empty($_POST['password'])) {
                    $datos['password'] = md5($_POST['password']);
                }
                Usuario::actualizar((int)$id, $datos);
                header('Location: ' . APP_URL . '/usuarios'); exit;
            }
        }

        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/usuario_form.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function toggle($id = null) {
        requireRol('administrador');
        // No permitir desactivar al propio admin logueado
        if ((int)$id === (int)$_SESSION['usuario_id']) {
            header('Location: ' . APP_URL . '/usuarios'); exit;
        }
        Usuario::toggleActivo((int)$id);
        header('Location: ' . APP_URL . '/usuarios'); exit;
    }

    public function eliminar($id = null) {
        requireRol('administrador');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ((int)$id !== (int)$_SESSION['usuario_id']) {
                Usuario::eliminar((int)$id);
            }
        }
        header('Location: ' . APP_URL . '/usuarios'); exit;
    }
}