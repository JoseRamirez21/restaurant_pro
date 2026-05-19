<?php
require_once BASE_PATH . '/app/models/Usuario.php';

class PerfilController {

    public function index($param = null) {
        requireAuth();
        $usuario    = Usuario::buscarPorId((int)$_SESSION['usuario_id']);
        $exito      = '';
        $error      = '';
        $page_title = 'Mi perfil';
        $esAdmin    = authRol('administrador', 'supervisor');

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/admin/perfil_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/auth/perfil.php';
        }
    }

    public function guardar($param = null) {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/perfil'); exit;
        }

        $id      = (int)$_SESSION['usuario_id'];
        $usuario = Usuario::buscarPorId($id);
        $error   = '';
        $exito   = '';

        // Verificar email único
        $emailExiste = Usuario::buscarPorEmail(trim($_POST['email']));
        if ($emailExiste && $emailExiste['id'] != $id) {
            $error = 'Ese correo ya está en uso.';
        }

        // Verificar contraseña actual si quiere cambiarla
        if (!$error && !empty($_POST['password_nuevo'])) {
            if (empty($_POST['password_actual'])) {
                $error = 'Debes ingresar tu contraseña actual para cambiarla.';
            } elseif (md5($_POST['password_actual']) !== $usuario['password']) {
                $error = 'La contraseña actual es incorrecta.';
            } elseif (strlen($_POST['password_nuevo']) < 3) {
                $error = 'La nueva contraseña debe tener al menos 3 caracteres.';
            }
        }

        if (!$error) {
            $datos = [
                'nombre'   => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'email'    => trim($_POST['email']),
                'rol'      => $usuario['rol'], // el rol no se cambia desde perfil
            ];
            if (!empty($_POST['password_nuevo'])) {
                $datos['password'] = md5($_POST['password_nuevo']);
            }
            Usuario::actualizar($id, $datos);

            // Actualizar sesión
            $_SESSION['nombre']  = $datos['nombre'];
            $_SESSION['apellido'] = $datos['apellido'];
            $_SESSION['email']   = $datos['email'];

            $exito = 'Perfil actualizado correctamente.';
        }

        $usuario    = Usuario::buscarPorId($id);
        $page_title = 'Mi perfil';
        $esAdmin    = authRol('administrador', 'supervisor');

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/admin/perfil_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/auth/perfil.php';
        }
    }
}