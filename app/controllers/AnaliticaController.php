<?php
require_once BASE_PATH . '/app/models/Analitica.php';

class AnaliticaController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        $datos      = Analitica::resumenCompleto();
        $page_title = 'Analítica';
        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/analitica_contenido.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }
}
