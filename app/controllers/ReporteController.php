<?php
require_once BASE_PATH . '/app/models/Reporte.php';

class ReporteController {

    public function index($param = null) {
        requireRol('administrador', 'supervisor');
        $periodo    = $_GET['periodo'] ?? 'hoy';
        $desde      = $_GET['desde']   ?? date('Y-m-d');
        $hasta      = $_GET['hasta']   ?? date('Y-m-d');

        // Definir rango según período
        switch ($periodo) {
            case 'hoy':
                $desde = $hasta = date('Y-m-d');
                break;
            case 'semana':
                $desde = date('Y-m-d', strtotime('monday this week'));
                $hasta = date('Y-m-d');
                break;
            case 'mes':
                $desde = date('Y-m-01');
                $hasta = date('Y-m-d');
                break;
            case 'personalizado':
                $desde = $_GET['desde'] ?? date('Y-m-d');
                $hasta = $_GET['hasta'] ?? date('Y-m-d');
                break;
        }

        $datos      = Reporte::obtener($desde, $hasta);
        $page_title = 'Reportes & Ventas';

        require_once APP_PATH . '/views/shared/layout_admin.php';
        require_once APP_PATH . '/views/admin/reportes_contenido.php';
        require_once APP_PATH . '/views/shared/layout_admin_footer.php';
    }
}
