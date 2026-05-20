<?php
require_once BASE_PATH . '/app/models/CierreCaja.php';

class CierreCajaController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor', 'cajero');
        $resumen    = CierreCaja::generarResumen();
        $cierre_hoy = CierreCaja::existeHoy();
        $historial  = CierreCaja::historial(30);
        $page_title = 'Cierre de caja';
        $esAdmin    = authRol('administrador', 'supervisor');

        if ($esAdmin) {
            require_once APP_PATH . '/views/shared/layout_admin.php';
            require_once APP_PATH . '/views/admin/cierre_caja_contenido.php';
            require_once APP_PATH . '/views/shared/layout_admin_footer.php';
        } else {
            require_once APP_PATH . '/views/caja/cierre_caja.php';
        }
    }

    public function cerrar($param = null) {
        requireRol('administrador', 'supervisor', 'cajero');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/cierre'); exit;
        }
        $resumen = CierreCaja::generarResumen();
        CierreCaja::cerrar(
            $resumen,
            (int)$_SESSION['usuario_id'],
            trim($_POST['observaciones'] ?? '')
        );
        header('Location: ' . APP_URL . '/cierre'); exit;
    }
}
