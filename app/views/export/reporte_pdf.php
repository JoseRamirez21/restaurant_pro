<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte <?= $desde ?> al <?= $hasta ?></title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family: Arial, sans-serif; font-size:12px; color:#333; background:#fff; }
        .page { max-width:800px; margin:0 auto; padding:2rem; }

        /* Header */
        .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #8e44ad; padding-bottom:1rem; margin-bottom:1.5rem; }
        .header-left h1 { font-size:1.4rem; font-weight:700; color:#2c1f3e; }
        .header-left p  { font-size:11px; color:#888; margin-top:3px; }
        .header-right { text-align:right; }
        .header-right .periodo { background:#8e44ad; color:#fff; border-radius:6px; padding:4px 12px; font-size:11px; font-weight:600; }
        .header-right .fecha { font-size:11px; color:#888; margin-top:4px; }

        /* Stats */
        .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
        .stat-box { border:1px solid #e8e5df; border-radius:8px; padding:.75rem; text-align:center; }
        .stat-box .num { font-size:1.3rem; font-weight:700; color:#8e44ad; }
        .stat-box .lbl { font-size:10px; color:#888; margin-top:2px; }

        /* Tablas */
        .seccion { margin-bottom:1.5rem; }
        .seccion h2 { font-size:13px; font-weight:700; color:#2c1f3e; border-left:4px solid #8e44ad; padding-left:.5rem; margin-bottom:.75rem; text-transform:uppercase; letter-spacing:.04em; }
        table { width:100%; border-collapse:collapse; }
        th { background:#2c1f3e; color:#fff; font-size:10px; padding:.4rem .6rem; text-align:left; font-weight:600; text-transform:uppercase; letter-spacing:.03em; }
        td { padding:.4rem .6rem; border-bottom:1px solid #f0ede7; font-size:11px; }
        tr:nth-child(even) td { background:#faf9f6; }
        .text-right { text-align:right; }
        .text-center { text-align:center; }
        .total-row td { font-weight:700; background:#f4f1eb; border-top:2px solid #e8e5df; }

        /* Footer */
        .footer { border-top:1px solid #e8e5df; padding-top:.75rem; margin-top:1.5rem; display:flex; justify-content:space-between; font-size:10px; color:#aaa; }

        /* Botón imprimir — solo en pantalla */
        .btn-print { position:fixed; bottom:1.5rem; right:1.5rem; background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.75rem 1.5rem; font-size:14px; font-weight:600; cursor:pointer; box-shadow:0 4px 16px rgba(142,68,173,.4); }
        .btn-volver { position:fixed; bottom:1.5rem; left:1.5rem; background:#2c1f3e; color:#fff; border:none; border-radius:10px; padding:.75rem 1.5rem; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; }

        @media print {
            .btn-print, .btn-volver { display:none; }
            body { font-size:11px; }
            .page { padding:1rem; }
        }
    </style>
</head>
<body>
<div class="page">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <h1>🍽 RestaurantePro</h1>
            <p>Reporte de ventas generado el <?= $fecha_gen ?></p>
        </div>
        <div class="header-right">
            <div class="periodo">
                <?= date('d/m/Y', strtotime($desde)) ?>
                <?= $desde !== $hasta ? ' — ' . date('d/m/Y', strtotime($hasta)) : '' ?>
            </div>
            <div class="fecha">Lima, Perú</div>
        </div>
    </div>

    <!-- STATS -->
    <?php $r = $datos['resumen']; ?>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="num">S/ <?= number_format($r['total_ventas'],2) ?></div>
            <div class="lbl">Total ventas</div>
        </div>
        <div class="stat-box">
            <div class="num"><?= $r['total_pedidos'] ?></div>
            <div class="lbl">Pedidos</div>
        </div>
        <div class="stat-box">
            <div class="num">S/ <?= number_format($r['ticket_promedio'],2) ?></div>
            <div class="lbl">Ticket promedio</div>
        </div>
        <div class="stat-box">
            <div class="num">S/ <?= number_format($r['total_igv'],2) ?></div>
            <div class="lbl">IGV</div>
        </div>
    </div>

    <!-- VENTAS POR DÍA -->
    <?php if (!empty($datos['por_dia'])): ?>
    <div class="seccion">
        <h2>Ventas por día</h2>
        <table>
            <thead><tr><th>Fecha</th><th class="text-center">Pedidos</th><th class="text-right">Ventas S/</th><th class="text-right">Promedio S/</th></tr></thead>
            <tbody>
            <?php foreach ($datos['por_dia'] as $d): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($d['dia'])) ?></td>
                <td class="text-center"><?= $d['pedidos'] ?></td>
                <td class="text-right">S/ <?= number_format($d['ventas'],2) ?></td>
                <td class="text-right">S/ <?= number_format($d['promedio'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td>TOTAL</td>
                <td class="text-center"><?= $r['total_pedidos'] ?></td>
                <td class="text-right">S/ <?= number_format($r['total_ventas'],2) ?></td>
                <td class="text-right">S/ <?= number_format($r['ticket_promedio'],2) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- TOP PRODUCTOS -->
    <?php if (!empty($datos['top_productos'])): ?>
    <div class="seccion">
        <h2>Top productos</h2>
        <table>
            <thead><tr><th>#</th><th>Plato</th><th>Categoría</th><th class="text-center">Cant.</th><th class="text-right">Ingresos S/</th></tr></thead>
            <tbody>
            <?php foreach ($datos['top_productos'] as $i => $p): ?>
            <tr>
                <td class="text-center"><?= $i+1 ?></td>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['categoria']) ?></td>
                <td class="text-center"><?= $p['cantidad'] ?></td>
                <td class="text-right">S/ <?= number_format($p['monto'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- MÉTODOS DE PAGO -->
    <?php if (!empty($datos['metodos'])): ?>
    <div class="seccion">
        <h2>Métodos de pago</h2>
        <table>
            <thead><tr><th>Método</th><th class="text-center">Cobros</th><th class="text-right">Total S/</th></tr></thead>
            <tbody>
            <?php foreach ($datos['metodos'] as $m): ?>
            <tr>
                <td style="text-transform:capitalize;"><?= $m['metodo_pago'] ?></td>
                <td class="text-center"><?= $m['cantidad'] ?></td>
                <td class="text-right">S/ <?= number_format($m['monto'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- PERFORMANCE MESEROS -->
    <?php if (!empty($datos['por_mesero'])): ?>
    <div class="seccion">
        <h2>Performance del equipo</h2>
        <table>
            <thead><tr><th>Mesero</th><th class="text-center">Pedidos</th><th class="text-right">Ventas S/</th><th class="text-right">Promedio S/</th></tr></thead>
            <tbody>
            <?php foreach ($datos['por_mesero'] as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['mesero']) ?></td>
                <td class="text-center"><?= $m['pedidos'] ?></td>
                <td class="text-right">S/ <?= number_format($m['ventas'],2) ?></td>
                <td class="text-right">S/ <?= number_format($m['promedio'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- FOOTER -->
    <div class="footer">
        <span>RestaurantePro — Sistema de gestión interno</span>
        <span>Reporte generado el <?= $fecha_gen ?></span>
    </div>

</div>

<!-- Botones flotantes -->
<button class="btn-print" onclick="window.print()">
    🖨️ Imprimir / Guardar PDF
</button>
<a href="javascript:history.back()" class="btn-volver">
    ← Volver
</a>

<script>
// Auto-abrir diálogo de impresión
window.onload = () => {
    setTimeout(() => window.print(), 800);
};
</script>
</body>
</html>