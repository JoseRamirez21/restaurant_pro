<?php
require_once BASE_PATH . '/app/models/Configuracion.php';

class ConfiguracionController {

    public function index($param = null) {
        requireRol('administrador');
        $grupos     = Configuracion::porGrupo();
        $page_title = 'Configuración del sistema';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/configuracion_contenido.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }

    public function guardar($param = null) {
        requireRol('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/configuracion'); exit;
        }

        // Guardar cada campo del formulario
        foreach ($_POST as $clave => $valor) {
            if ($clave === 'csrf') continue;
            Configuracion::set($clave, trim($valor));
        }

        // Checkboxes booleanos — si no vienen en POST es porque están en false
        $booleanos = ['reservas_activo', 'inventario_activo'];
        foreach ($booleanos as $b) {
            if (!isset($_POST[$b])) {
                Configuracion::set($b, '0');
            }
        }

        header('Location: ' . APP_URL . '/configuracion?guardado=1'); exit;
    }
}