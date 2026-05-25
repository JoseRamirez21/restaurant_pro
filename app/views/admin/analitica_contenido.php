<?php
/** @var array $datos */
$sa = $datos['semana_actual'];
$sp = $datos['semana_anterior'];
$vv = $datos['var_ventas'];
$vp = $datos['var_pedidos'];
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.stat-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.2rem 1.4rem; height:100%; }
.stat-num  { font-size:1.6rem; font-weight:700; line-height:1; }
.stat-lbl  { font-size:12px; color:#888; margin-top:3px; }
.stat-var  { font-size:12px; margin-top:5px; font-weight:500; }
.var-sube  { color:#2e7d32; }
.var-baja  { color:#c62828; }
.chart-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.2rem 1.4rem; }
.chart-title { font-size:14px; font-weight:600; margin-bottom:1rem; }
.tabla-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.tabla-card .t-hdr { padding:.9rem 1.2rem; border-bottom:1px solid #e8e5df; font-size:14px; font-weight:600; display:flex; align-items:center; justify-content:space-between; }
.tabla-card table { width:100%; border-collapse:collapse; }
.tabla-card th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.6rem 1rem; border-bottom:1px solid #f0ede7; text-align:left; background:#faf9f6; }
.tabla-card td { font-size:13px; padding:.65rem 1rem; border-bottom:1px solid #f7f5f0; }
.tabla-card tr:last-child td { border-bottom:none; }
.margen-bar { height:6px; border-radius:3px; background:#e8e5df; overflow:hidden; margin-top:3px; }
.margen-fill { height:100%; border-radius:3px; }
.alerta-card { background:#fff8f0; border:1px solid #fd7e14; border-radius:14px; padding:1rem 1.2rem; }
.sin-mov-item { font-size:13px; padding:.4rem 0; border-bottom:1px solid #f7f5f0; display:flex; justify-content:space-between; }
.sin-mov-item:last-child { border-bottom:none; }
.periodo-tag { font-size:11px; color:#aaa; margin-bottom:1.2rem; }
</style>

<div class="periodo-tag">
    <i class="bi bi-calendar3 me-1"></i>
    Análisis basado en los últimos 30 días · Comparativa vs semana anterior
</div>

<!-- FILA 1: Comparativa semana -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-num" style="color:#2e7d32;">
                S/ <?= number_format($sa['ventas'],2) ?>
            </div>
            <div class="stat-lbl">Ventas esta semana</div>
            <div class="stat-var <?= $vv['sube']?'var-sube':'var-baja' ?>">
                <i class="bi bi-arrow-<?= $vv['sube']?'up':'down' ?>"></i>
                <?= $vv['pct'] ?>% vs semana anterior
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-num" style="color:#1565c0;">
                <?= $sa['pedidos'] ?>
            </div>
            <div class="stat-lbl">Pedidos esta semana</div>
            <div class="stat-var <?= $vp['sube']?'var-sube':'var-baja' ?>">
                <i class="bi bi-arrow-<?= $vp['sube']?'up':'down' ?>"></i>
                <?= $vp['pct'] ?>% vs semana anterior
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-num" style="color:#e65100;">
                <?= $datos['clientes_stats']['total_clientes'] ?? 0 ?>
            </div>
            <div class="stat-lbl">Clientes registrados</div>
            <div class="stat-var var-sube">
                <i class="bi bi-star-fill"></i>
                <?= $datos['clientes_stats']['frecuentes'] ?? 0 ?> clientes VIP
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-num" style="color:#6a1b9a;">
                S/ <?= number_format($datos['clientes_stats']['gasto_promedio'] ?? 0, 2) ?>
            </div>
            <div class="stat-lbl">Gasto promedio cliente</div>
            <div class="stat-var" style="color:#aaa;">
                <i class="bi bi-people"></i>
                <?= $datos['clientes_stats']['nuevos'] ?? 0 ?> clientes nuevos
            </div>
        </div>
    </div>
</div>

<!-- FILA 2: Gráficos -->
<div class="row g-3 mb-3">

    <!-- Ventas por mes -->
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-title">Ventas por mes — últimos 6 meses</div>
            <canvas id="chartMeses" height="100"></canvas>
        </div>
    </div>

    <!-- Días más rentables -->
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-title">Días más rentables</div>
            <canvas id="chartDias" height="200"></canvas>
        </div>
    </div>

</div>

<!-- FILA 3: Horas pico + Métodos -->
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-title">Horas pico — últimos 30 días</div>
            <canvas id="chartHoras" height="100"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-title">Métodos de pago</div>
            <?php if (!empty($datos['metodos_30d'])): ?>
            <canvas id="chartMetodos" height="200"></canvas>
            <?php else: ?>
            <div style="text-align:center;color:#ccc;padding:2rem;">Sin datos</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- FILA 4: Tablas -->
<div class="row g-3 mb-3">

    <!-- Top por margen -->
    <div class="col-lg-6">
        <div class="tabla-card">
            <div class="t-hdr">
                <span><i class="bi bi-graph-up me-2"></i>Platos por margen de ganancia</span>
                <span style="font-size:11px;color:#aaa;">Últimos 30 días</span>
            </div>
            <table>
                <thead><tr><th>Plato</th><th>Precio</th><th>Margen</th><th>Vendidos</th></tr></thead>
                <tbody>
                <?php foreach ($datos['top_margen'] as $p): ?>
                <tr>
                    <td style="font-weight:500;"><?= htmlspecialchars($p['nombre']) ?></td>
                    <td>S/ <?= number_format($p['precio'],2) ?></td>
                    <td>
                        <div style="font-weight:600;color:<?= $p['margen_pct']>=60?'#2e7d32':($p['margen_pct']<30?'#c62828':'#e65100') ?>">
                            <?= $p['margen_pct'] ?>%
                        </div>
                        <div class="margen-bar">
                            <div class="margen-fill" style="width:<?= min(100,$p['margen_pct']) ?>%;background:<?= $p['margen_pct']>=60?'#28a745':($p['margen_pct']<30?'#dc3545':'#fd7e14') ?>;"></div>
                        </div>
                    </td>
                    <td style="color:#888;"><?= $p['vendidos'] ?? 0 ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ranking meseros -->
    <div class="col-lg-6">
        <div class="tabla-card">
            <div class="t-hdr">
                <span><i class="bi bi-trophy me-2"></i>Ranking de meseros</span>
                <span style="font-size:11px;color:#aaa;">Últimos 30 días</span>
            </div>
            <table>
                <thead><tr><th>#</th><th>Mesero</th><th>Pedidos</th><th>Ventas</th><th>Ticket prom.</th></tr></thead>
                <tbody>
                <?php foreach ($datos['ranking_meseros'] as $i => $m): ?>
                <tr>
                    <td>
                        <?php if ($i === 0): ?>
                        <span style="font-size:16px;">🥇</span>
                        <?php elseif ($i === 1): ?>
                        <span style="font-size:16px;">🥈</span>
                        <?php elseif ($i === 2): ?>
                        <span style="font-size:16px;">🥉</span>
                        <?php else: ?>
                        <span style="color:#aaa;"><?= $i+1 ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:500;"><?= htmlspecialchars($m['nombre']) ?></td>
                    <td><?= $m['pedidos'] ?></td>
                    <td><strong>S/ <?= number_format($m['ventas'],2) ?></strong></td>
                    <td style="color:#888;">S/ <?= number_format($m['ticket_prom'],2) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Platos sin movimiento -->
<?php if (!empty($datos['sin_movimiento'])): ?>
<div class="alerta-card mb-3">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:18px;"></i>
        <strong style="font-size:14px;">
            <?= count($datos['sin_movimiento']) ?> plato(s) sin ventas en los últimos 30 días
        </strong>
    </div>
    <?php foreach ($datos['sin_movimiento'] as $p): ?>
    <div class="sin-mov-item">
        <span><?= htmlspecialchars($p['nombre']) ?> <span style="font-size:11px;color:#aaa;">— <?= htmlspecialchars($p['categoria']) ?></span></span>
        <span style="color:#888;">S/ <?= number_format($p['precio'],2) ?></span>
    </div>
    <?php endforeach; ?>
    <div style="font-size:12px;color:#aaa;margin-top:.5rem;">
        Considera actualizar precios, descripciones o desactivar estos platos.
    </div>
</div>
<?php endif; ?>

<script>
// Ventas por mes
<?php
$meses_labels  = array_map(fn($m) => $m['mes_label'], $datos['por_mes']);
$meses_ventas  = array_map(fn($m) => (float)$m['ventas'],  $datos['por_mes']);
$meses_pedidos = array_map(fn($m) => (int)$m['pedidos'],   $datos['por_mes']);
?>
new Chart(document.getElementById('chartMeses').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($meses_labels) ?>,
        datasets: [
            {
                label: 'Ventas S/',
                data: <?= json_encode($meses_ventas) ?>,
                backgroundColor: 'rgba(142,68,173,.15)',
                borderColor: '#8e44ad',
                borderWidth: 2,
                borderRadius: 8,
                yAxisID: 'y',
            },
            {
                label: 'Pedidos',
                data: <?= json_encode($meses_pedidos) ?>,
                type: 'line',
                borderColor: '#2980b9',
                backgroundColor: 'transparent',
                borderWidth: 2,
                pointRadius: 4,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'top', labels: { font: { size: 12 } } } },
        scales: {
            y:  { beginAtZero: true, ticks: { callback: v => 'S/ ' + v }, grid: { display: false } },
            y1: { beginAtZero: true, position: 'right', ticks: { stepSize: 1 }, grid: { display: false } },
            x:  { grid: { display: false } }
        }
    }
});

// Días de la semana
<?php
$dias_es   = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
$dias_data = array_fill(0, 7, 0);
foreach ($datos['por_dia_semana'] as $d) {
    $idx = (int)$d['dia_num'];
    if ($idx >= 0 && $idx <= 6) $dias_data[$idx] = (float)$d['ventas'];
}
?>
new Chart(document.getElementById('chartDias').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($dias_es) ?>,
        datasets: [{
            data: <?= json_encode($dias_data) ?>,
            backgroundColor: [
                'rgba(142,68,173,.2)','rgba(142,68,173,.4)','rgba(142,68,173,.6)',
                'rgba(142,68,173,.8)','rgba(142,68,173,1)','rgba(142,68,173,.7)','rgba(142,68,173,.3)'
            ],
            borderRadius: 6,
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true, indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v }, grid: { display: false } },
            y: { grid: { display: false } }
        }
    }
});

// Horas pico
<?php
$horas_labels = [];
$horas_data   = [];
foreach ($datos['horas_pico'] as $h) {
    $horas_labels[] = $h['hora'] . ':00';
    $horas_data[]   = (int)$h['pedidos'];
}
?>
new Chart(document.getElementById('chartHoras').getContext('2d'), {
    type: 'line',
    data: {
        labels: <?= json_encode($horas_labels) ?>,
        datasets: [{
            label: 'Pedidos',
            data: <?= json_encode($horas_data) ?>,
            borderColor: '#e67e22',
            backgroundColor: 'rgba(230,126,34,.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0ede7' } },
            x: { grid: { display: false } }
        }
    }
});

// Métodos de pago
<?php if (!empty($datos['metodos_30d'])): ?>
new Chart(document.getElementById('chartMetodos').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_map(fn($m) => ucfirst($m['metodo_pago']), $datos['metodos_30d'])) ?>,
        datasets: [{
            data: <?= json_encode(array_map(fn($m) => (float)$m['monto'], $datos['metodos_30d'])) ?>,
            backgroundColor: ['#8e44ad','#2980b9','#27ae60','#e67e22','#e74c3c'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 12 } } } }
    }
});
<?php endif; ?>
</script>
