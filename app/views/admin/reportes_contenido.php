<?php
/** @var array  $datos   */
/** @var string $periodo */
/** @var string $desde   */
/** @var string $hasta   */
$r = $datos['resumen'];
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.stat-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.2rem 1.4rem; }
.stat-num  { font-size:1.7rem; font-weight:700; line-height:1; }
.stat-lbl  { font-size:12px; color:#888; margin-top:3px; }
.tabla-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.tabla-card .t-hdr { padding:.9rem 1.2rem; border-bottom:1px solid #e8e5df; font-size:14px; font-weight:600; display:flex; align-items:center; justify-content:space-between; }
.tabla-card table { width:100%; border-collapse:collapse; }
.tabla-card th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.6rem 1rem; border-bottom:1px solid #f0ede7; text-align:left; background:#faf9f6; }
.tabla-card td { font-size:13px; padding:.65rem 1rem; border-bottom:1px solid #f7f5f0; }
.tabla-card tr:last-child td { border-bottom:none; }
.chart-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.2rem 1.4rem; }
.periodo-btn { border:1.5px solid #e8e5df; background:#fff; border-radius:8px; padding:.35rem .9rem; font-size:13px; cursor:pointer; transition:all .15s; text-decoration:none; color:#555; }
.periodo-btn.active, .periodo-btn:hover { background:#8e44ad; color:#fff; border-color:#8e44ad; }
.cat-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:5px; }
.sin-datos { text-align:center; padding:2.5rem; color:#ccc; }
</style>

<!-- Filtros de período -->
<div class="d-flex flex-wrap align-items-center gap-2 mb-4">
    <span style="font-size:13px;font-weight:500;color:#555;">Período:</span>
    <?php foreach (['hoy'=>'Hoy','semana'=>'Esta semana','mes'=>'Este mes','personalizado'=>'Personalizado'] as $p => $l): ?>
    <a href="<?= APP_URL ?>/reportes?periodo=<?= $p ?>"
       class="periodo-btn <?= $periodo === $p ? 'active' : '' ?>">
        <?= $l ?>
    </a>
    <?php endforeach; ?>

    <?php if ($periodo === 'personalizado'): ?>
    <form method="GET" action="<?= APP_URL ?>/reportes" class="d-flex gap-2 align-items-center ms-2">
        <input type="hidden" name="periodo" value="personalizado">
        <input type="date" name="desde" value="<?= $desde ?>" class="form-control form-control-sm" style="border-radius:8px;">
        <span style="color:#aaa;">—</span>
        <input type="date" name="hasta" value="<?= $hasta ?>" class="form-control form-control-sm" style="border-radius:8px;">
        <button type="submit" class="btn btn-sm" style="background:#8e44ad;color:#fff;border-radius:8px;">
            <i class="bi bi-search"></i>
        </button>
    </form>
    <?php endif; ?>

    <div class="d-flex gap-2 ms-auto flex-wrap">
        <a href="<?= APP_URL ?>/export/pdf?periodo=<?= $periodo ?>&desde=<?= $desde ?>&hasta=<?= $hasta ?>"
           target="_blank"
           class="btn btn-sm" style="background:#c62828;color:#fff;border-radius:8px;">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>
        <a href="<?= APP_URL ?>/export/excel?periodo=<?= $periodo ?>&desde=<?= $desde ?>&hasta=<?= $hasta ?>"
           class="btn btn-sm" style="background:#2e7d32;color:#fff;border-radius:8px;">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </a>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>Imprimir
        </button>
    </div>
</div>

<div style="font-size:12px;color:#aaa;margin-bottom:1rem;">
    Mostrando: <?= date('d/m/Y', strtotime($desde)) ?> — <?= date('d/m/Y', strtotime($hasta)) ?>
</div>

<!-- Stats resumen -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-num" style="color:#2e7d32;">S/ <?= number_format($r['total_ventas'], 2) ?></div>
            <div class="stat-lbl">Total ventas</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-num" style="color:#1565c0;"><?= $r['total_pedidos'] ?></div>
            <div class="stat-lbl">Pedidos cerrados</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-num" style="color:#e65100;">S/ <?= number_format($r['ticket_promedio'], 2) ?></div>
            <div class="stat-lbl">Ticket promedio</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-num" style="color:#6a1b9a;">S/ <?= number_format($r['total_igv'], 2) ?></div>
            <div class="stat-lbl">IGV generado</div>
        </div>
    </div>
</div>

<?php if ($r['total_pedidos'] == 0): ?>
<div class="sin-datos">
    <i class="bi bi-bar-chart" style="font-size:3rem;"></i>
    <h5 class="mt-3">Sin datos para este período</h5>
    <p style="font-size:13px;">No hay pedidos cerrados en el rango seleccionado.</p>
</div>
<?php else: ?>

<!-- Gráficos -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:1rem;">Ventas por día</div>
            <canvas id="chartDias" height="100"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div style="font-size:14px;font-weight:600;margin-bottom:1rem;">Horas pico</div>
            <canvas id="chartHoras" height="180"></canvas>
        </div>
    </div>
</div>

<!-- Tablas -->
<div class="row g-3 mb-4">

    <!-- Top productos -->
    <div class="col-lg-6">
        <div class="tabla-card">
            <div class="t-hdr">
                <span><i class="bi bi-trophy me-2"></i>Top 10 platos</span>
            </div>
            <table>
                <thead><tr><th>#</th><th>Plato</th><th>Cant.</th><th>S/</th></tr></thead>
                <tbody>
                <?php foreach ($datos['top_productos'] as $i => $p): ?>
                <tr>
                    <td style="color:#aaa;"><?= $i+1 ?></td>
                    <td>
                        <span class="cat-dot" style="background:<?= $p['categoria_color'] ?>"></span>
                        <?= htmlspecialchars($p['nombre']) ?>
                        <div style="font-size:11px;color:#aaa;"><?= htmlspecialchars($p['categoria']) ?></div>
                    </td>
                    <td><strong><?= $p['cantidad'] ?></strong></td>
                    <td>S/ <?= number_format($p['monto'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Performance meseros -->
    <div class="col-lg-6">
        <div class="tabla-card">
            <div class="t-hdr">
                <span><i class="bi bi-person-check me-2"></i>Performance del equipo</span>
            </div>
            <table>
                <thead><tr><th>Mesero</th><th>Pedidos</th><th>Ventas</th><th>Promedio</th></tr></thead>
                <tbody>
                <?php foreach ($datos['por_mesero'] as $m): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($m['mesero']) ?></strong></td>
                    <td><?= $m['pedidos'] ?></td>
                    <td>S/ <?= number_format($m['ventas'], 2) ?></td>
                    <td style="color:#888;">S/ <?= number_format($m['promedio'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="row g-3 mb-4">

    <!-- Ventas por categoría -->
    <div class="col-lg-6">
        <div class="tabla-card">
            <div class="t-hdr"><span><i class="bi bi-tags me-2"></i>Ventas por categoría</span></div>
            <table>
                <thead><tr><th>Categoría</th><th>Items</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($datos['por_categoria'] as $c): ?>
                <tr>
                    <td>
                        <span class="cat-dot" style="background:<?= $c['color'] ?>"></span>
                        <?= htmlspecialchars($c['categoria']) ?>
                    </td>
                    <td><?= $c['cantidad'] ?></td>
                    <td><strong>S/ <?= number_format($c['monto'], 2) ?></strong></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Métodos de pago -->
    <div class="col-lg-6">
        <div class="tabla-card">
            <div class="t-hdr"><span><i class="bi bi-credit-card me-2"></i>Métodos de pago</span></div>
            <table>
                <thead><tr><th>Método</th><th>Cantidad</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($datos['metodos'] as $m): ?>
                <tr>
                    <td style="text-transform:capitalize;font-weight:500;"><?= $m['metodo_pago'] ?></td>
                    <td><?= $m['cantidad'] ?> cobros</td>
                    <td><strong>S/ <?= number_format($m['monto'], 2) ?></strong></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
// Gráfico ventas por día
const diasLabels = <?= json_encode(array_map(fn($d) => date('d/m', strtotime($d['dia'])), $datos['por_dia'])) ?>;
const diasData   = <?= json_encode(array_map(fn($d) => (float)$d['ventas'], $datos['por_dia'])) ?>;
new Chart(document.getElementById('chartDias').getContext('2d'), {
    type: 'bar',
    data: {
        labels: diasLabels,
        datasets: [{
            label: 'Ventas S/',
            data: diasData,
            backgroundColor: 'rgba(142,68,173,.15)',
            borderColor: '#8e44ad',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v } },
            x: { grid: { display: false } }
        }
    }
});

// Gráfico horas pico
const horasLabels = <?= json_encode(array_map(fn($h) => $h['hora'] . ':00', $datos['horas_pico'])) ?>;
const horasData   = <?= json_encode(array_map(fn($h) => (int)$h['pedidos'], $datos['horas_pico'])) ?>;
new Chart(document.getElementById('chartHoras').getContext('2d'), {
    type: 'bar',
    data: {
        labels: horasLabels,
        datasets: [{
            label: 'Pedidos',
            data: horasData,
            backgroundColor: 'rgba(41,128,185,.15)',
            borderColor: '#2980b9',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php endif; ?>

<style>
@media print {
    .sidebar, .sidebar-overlay, .topbar, .periodo-btn, form, button { display: none !important; }
    .main { margin-left: 0 !important; }
    body { background: #fff; }
}
</style>