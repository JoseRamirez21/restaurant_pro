<?php
require_once BASE_PATH . '/app/models/Reporte.php';
require_once BASE_PATH . '/app/models/CierreCaja.php';

class ExportController {

    // Exportar reporte a PDF
    public function pdf($param = null) {
        requireRol('administrador', 'supervisor');
        $periodo = $_GET['periodo'] ?? 'hoy';
        $desde   = $_GET['desde']   ?? date('Y-m-d');
        $hasta   = $_GET['hasta']   ?? date('Y-m-d');

        switch ($periodo) {
            case 'hoy':
                $desde = $hasta = date('Y-m-d'); break;
            case 'semana':
                $desde = date('Y-m-d', strtotime('monday this week'));
                $hasta = date('Y-m-d'); break;
            case 'mes':
                $desde = date('Y-m-01');
                $hasta = date('Y-m-d'); break;
        }

        $datos      = Reporte::obtener($desde, $hasta);
        $fecha_gen  = date('d/m/Y H:i');

        // Header para forzar descarga como PDF imprimible
        header('Content-Type: text/html; charset=utf-8');
        require_once APP_PATH . '/views/export/reporte_pdf.php';
        exit;
    }

    // Exportar reporte a Excel (CSV)
    public function excel($param = null) {
        requireRol('administrador', 'supervisor');
        $periodo = $_GET['periodo'] ?? 'hoy';
        $desde   = $_GET['desde']   ?? date('Y-m-d');
        $hasta   = $_GET['hasta']   ?? date('Y-m-d');

        switch ($periodo) {
            case 'hoy':
                $desde = $hasta = date('Y-m-d'); break;
            case 'semana':
                $desde = date('Y-m-d', strtotime('monday this week'));
                $hasta = date('Y-m-d'); break;
            case 'mes':
                $desde = date('Y-m-01');
                $hasta = date('Y-m-d'); break;
        }

        $datos = Reporte::obtener($desde, $hasta);
        $r     = $datos['resumen'];

        // Headers para descarga Excel
        $filename = 'reporte_' . $desde . '_' . $hasta . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        // BOM para que Excel abra bien con tildes
        fputs($out, "\xEF\xBB\xBF");

        // RESUMEN
        fputcsv($out, ['REPORTE DE VENTAS — RESTAURANTEPRO'], ';');
        fputcsv($out, ['Período:', $desde . ' al ' . $hasta], ';');
        fputcsv($out, ['Generado:', date('d/m/Y H:i')], ';');
        fputcsv($out, [], ';');

        fputcsv($out, ['RESUMEN GENERAL'], ';');
        fputcsv($out, ['Total ventas', 'S/ ' . number_format($r['total_ventas'], 2)], ';');
        fputcsv($out, ['Pedidos cerrados', $r['total_pedidos']], ';');
        fputcsv($out, ['Ticket promedio', 'S/ ' . number_format($r['ticket_promedio'], 2)], ';');
        fputcsv($out, ['IGV generado', 'S/ ' . number_format($r['total_igv'], 2)], ';');
        fputcsv($out, [], ';');

        // VENTAS POR DÍA
        fputcsv($out, ['VENTAS POR DÍA'], ';');
        fputcsv($out, ['Fecha', 'Pedidos', 'Ventas S/', 'Promedio S/'], ';');
        foreach ($datos['por_dia'] as $d) {
            fputcsv($out, [
                date('d/m/Y', strtotime($d['dia'])),
                $d['pedidos'],
                number_format($d['ventas'], 2),
                number_format($d['promedio'], 2),
            ], ';');
        }
        fputcsv($out, [], ';');

        // TOP PRODUCTOS
        fputcsv($out, ['TOP PRODUCTOS'], ';');
        fputcsv($out, ['Plato', 'Categoría', 'Cantidad', 'Ingresos S/'], ';');
        foreach ($datos['top_productos'] as $p) {
            fputcsv($out, [
                $p['nombre'],
                $p['categoria'],
                $p['cantidad'],
                number_format($p['monto'], 2),
            ], ';');
        }
        fputcsv($out, [], ';');

        // MÉTODOS DE PAGO
        fputcsv($out, ['MÉTODOS DE PAGO'], ';');
        fputcsv($out, ['Método', 'Cantidad', 'Total S/'], ';');
        foreach ($datos['metodos'] as $m) {
            fputcsv($out, [
                ucfirst($m['metodo_pago']),
                $m['cantidad'],
                number_format($m['monto'], 2),
            ], ';');
        }
        fputcsv($out, [], ';');

        // PERFORMANCE MESEROS
        fputcsv($out, ['PERFORMANCE DEL EQUIPO'], ';');
        fputcsv($out, ['Mesero', 'Pedidos', 'Ventas S/', 'Promedio S/'], ';');
        foreach ($datos['por_mesero'] as $m) {
            fputcsv($out, [
                $m['mesero'],
                $m['pedidos'],
                number_format($m['ventas'], 2),
                number_format($m['promedio'], 2),
            ], ';');
        }

        fclose($out);
        exit;
    }

    // Exportar historial de cierres a Excel
    public function cierres($param = null) {
        requireRol('administrador', 'supervisor');
        $historial = CierreCaja::historial(90);

        $filename = 'cierres_caja_' . date('Y-m') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");

        fputcsv($out, ['HISTORIAL DE CIERRES DE CAJA — RESTAURANTEPRO'], ';');
        fputcsv($out, ['Generado:', date('d/m/Y H:i')], ';');
        fputcsv($out, [], ';');
        fputcsv($out, ['Fecha','Cajero','Ventas S/','Pedidos','Efectivo','Tarjeta','Yape','Plin','Propinas'], ';');

        foreach ($historial as $c) {
            fputcsv($out, [
                date('d/m/Y', strtotime($c['fecha'])),
                $c['cajero'] ?? '—',
                number_format($c['total_ventas'],   2),
                $c['total_pedidos'],
                number_format($c['total_efectivo'], 2),
                number_format($c['total_tarjeta'],  2),
                number_format($c['total_yape'],     2),
                number_format($c['total_plin'],     2),
                number_format($c['total_propinas'], 2),
            ], ';');
        }

        fclose($out);
        exit;
    }
}