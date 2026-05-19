<?php
require_once BASE_PATH . '/app/models/Reserva.php';
require_once BASE_PATH . '/app/models/Mesa.php';
/** @var Reserva $reserva */
class ReservaController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        $fecha      = $_GET['fecha'] ?? date('Y-m-d');
        $reservas   = Reserva::porFecha($fecha);
        $proximas   = Reserva::proximas(7);
        $resumen    = Reserva::resumenHoy();
        $mesas      = Mesa::todas();
        $page_title = 'Reservas';
        $esAdmin    = authRol('administrador', 'supervisor');

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/admin/reservas_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/mozo/reservas.php';
        }
    }

    public function nueva($param = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        $reserva    = null;
        $mesas      = Mesa::todas();
        $error      = '';
        $page_title = 'Nueva reserva';
        $esAdmin    = authRol('administrador', 'supervisor');

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/admin/reserva_form.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/mozo/reserva_form.php';
        }
    }

    public function crear($param = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/reservas'); exit;
        }
        Reserva::crear([
            'mesa_id'        => (int)($_POST['mesa_id'] ?? 0),
            'nombre_cliente' => trim($_POST['nombre_cliente']),
            'telefono'       => trim($_POST['telefono']      ?? ''),
            'email'          => trim($_POST['email']         ?? ''),
            'fecha'          => $_POST['fecha'],
            'hora'           => $_POST['hora'],
            'personas'       => (int)($_POST['personas']    ?? 2),
            'notas'          => trim($_POST['notas']         ?? ''),
            'usuario_id'     => $_SESSION['usuario_id'],
        ]);
        header('Location: ' . APP_URL . '/reservas'); exit;
    }

    public function editar($id = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        $reserva = Reserva::buscarPorId((int)$id);
        if (!$reserva) { header('Location: ' . APP_URL . '/reservas'); exit; }

        $mesas      = Mesa::todas();
        $error      = '';
        $page_title = 'Editar reserva — ' . $reserva['nombre_cliente'];
        $esAdmin    = authRol('administrador', 'supervisor');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Reserva::actualizar((int)$id, [
                'mesa_id'        => (int)($_POST['mesa_id'] ?? 0),
                'nombre_cliente' => trim($_POST['nombre_cliente']),
                'telefono'       => trim($_POST['telefono']   ?? ''),
                'email'          => trim($_POST['email']      ?? ''),
                'fecha'          => $_POST['fecha'],
                'hora'           => $_POST['hora'],
                'personas'       => (int)($_POST['personas'] ?? 2),
                'notas'          => trim($_POST['notas']      ?? ''),
            ]);
            header('Location: ' . APP_URL . '/reservas'); exit;
        }

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/admin/reserva_form.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/mozo/reserva_form.php';
        }
    }

    public function estado($id = null) {
        requireRol('administrador', 'supervisor', 'mesero');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Reserva::cambiarEstado((int)$id, $_POST['estado']);
        }
        header('Location: ' . APP_URL . '/reservas'); exit;
    }

    public function eliminar($id = null) {
        requireRol('administrador', 'supervisor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Reserva::eliminar((int)$id);
        }
        header('Location: ' . APP_URL . '/reservas'); exit;
    }
}