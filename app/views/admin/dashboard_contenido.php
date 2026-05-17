<?php
/** @var array $datos */
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.stat-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.2rem 1.4rem; height:100%; }
.stat-card .icon { width:46px; height:46px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:21px; flex-shrink:0; }
.stat-card .num  { font-size:1.7rem; font-weight:700; line-height:1; margin-bottom:2px; }
.stat-card .lbl  { font-size:12.5px; color:#888; }
.stat-card .sub  { font-size:11.5px; color:#aaa; margin-top:3px; }
.tabla-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.tabla-card .t-header { padding:.9rem 1.2rem; border-bottom:1px solid #e8e5df; font-size:14px; font-weight:600; display:flex; align-items:center; justify-content:space-between; }
.tabla-card table { width:100%; border-collapse:collapse; }
.tabla-card th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.6rem 1.2rem; border-bottom:1px solid #f0ede7; text-align:left; }
.tabla-card td { font-size:13px; padding:.65rem 1.2rem; border-bottom:1px solid #f7f5f0; }
.tabla-card tr:last-child td { border-bottom:none; }
.estado-pill { font-size:11px; padding:2px 9px; border-radius:20px; font-weight:500; }
.estado-abierto   { background:#e3f2fd; color:#1565c0; }
.estado-en_cocina { background:#fff3e0; color:#e65100; }
.estado-listo     { background:#e8f5e9; color:#2e7d32; }
.chart-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.2rem 1.4rem; }
</style>

<!-- Stats principales -->
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="num" style="color:#2e7d32;">S/ <?= number_format($datos['ventas']['total_ventas'], 2) ?></div>
                <div class="lbl">Ventas hoy</div>
                <div class="sub"><?= $datos['ventas']['total_pedidos'] ?> pedidos cerrados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon" style="background:#fff3e0;color:#e65100;"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="num" style="color:#e65100;"><?= $datos['activos'] ?></div>
                <div class="lbl">Pedidos activos</div>
                <div class="sub"><?= $datos['cocina'] ?> ítem(s) en cocina</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon" style="background:#f3e5f5;color:#6a1b9a;"><i class="bi bi-layout-grid"></i></div>
            <div>
                <div class="num" style="color:#6a1b9a;"><?= $datos['mesas']['ocupadas'] ?>/<?= $datos['mesas']['total'] ?></div>
                <div class="lbl">Mesas ocupadas</div>
                <div class="sub"><?= $datos['mesas']['libres'] ?> libres · <?= $datos['mesas']['reservadas'] ?> reservadas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon" style="background:#e3f2fd;color:#1565c0;"><i class="bi bi-graph-up-arrow"></i></div>
            <div>
                <div class="num" style="color:#1565c0;">S/ <?= number_format($datos['ventas']['ticket_promedio'], 2) ?></div>
                <div class="lbl">Ticket promedio</div>
                <div class="sub">S/ <?= number_format($datos['ventas']['total_propinas'], 2) ?> en propinas</div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="chart-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:1rem;">Ventas últimos 7 días</div>
            <canvas id="chartSemana" height="100"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div style="font-size:14px;font-weight:600;margin-bottom:1rem;">Métodos de pago hoy</div>
            <?php if (empty($datos['metodos'])): ?>
            <div style="text-align:center;color:#ccc;padding:2rem;">
                <i class="bi bi-credit-card" style="font-size:2rem;"></i>
                <p style="font-size:13px;margin-top:.5rem;">Sin cobros hoy aún</p>
            </div>
            <?php else: ?>
            <canvas id="chartMetodos" height="180"></canvas>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tablas -->
<div class="row g-3">
    <div class="col-lg-7">
        <div class="tabla-card">
            <div class="t-header">
                <span><i class="bi bi-receipt me-2"></i>Pedidos activos</span>
                <a href="<?= APP_URL ?>/mesas" style="font-size:12px;color:#8e44ad;text-decoration:none;">Ver mesas →</a>
            </div>
            <?php if (empty($datos['pedidos_activos'])): ?>
            <div style="text-align:center;color:#ccc;padding:2rem;">
                <i class="bi bi-check-circle" style="font-size:2rem;"></i>
                <p style="font-size:13px;margin-top:.5rem;">Sin pedidos activos</p>
            </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>Mesa</th><th>Mozo</th><th>Estado</th><th>Tiempo</th><th>Total</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['pedidos_activos'] as $p): ?>
                    <tr>
                        <td><strong>Mesa <?= $p['mesa_numero'] ?></strong><br><span style="font-size:11px;color:#aaa;"><?= ucfirst($p['zona']) ?></span></td>
                        <td><?= htmlspecialchars($p['mesero']) ?></td>
                        <td><span class="estado-pill estado-<?= $p['estado'] ?>"><?= ucfirst(str_replace('_',' ',$p['estado'])) ?></span></td>
                        <td><?= $p['minutos'] ?> min</td>
                        <td><strong>S/ <?= number_format($p['total'], 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="tabla-card">
            <div class="t-header">
                <span><i class="bi bi-trophy me-2"></i>Top platos</span>
                <span style="font-size:11px;color:#aaa;">Hoy / este mes</span>
            </div>
            <?php if (empty($datos['top_platos'])): ?>
            <div style="text-align:center;color:#ccc;padding:2rem;">
                <i class="bi bi-bar-chart" style="font-size:2rem;"></i>
                <p style="font-size:13px;margin-top:.5rem;">Sin ventas registradas</p>
            </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>#</th><th>Plato</th><th>Cant.</th><th>S/</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['top_platos'] as $i => $p): ?>
                    <tr>
                        <td style="color:#aaa;"><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><strong><?= $p['total_vendido'] ?></strong></td>
                        <td>S/ <?= number_format($p['total_monto'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Gráfico semana
<?php
$labels = []; $montos = [];
$diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
for ($i = 6; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("-$i days"));
    $labels[] = $diasSemana[date('w', strtotime($fecha))];
    $monto = 0;
    foreach ($datos['semana'] as $s) {
        if ($s['dia'] === $fecha) { $monto = (float)$s['ventas']; break; }
    }
    $montos[] = $monto;
}
?>
new Chart(document.getElementById('chartSemana').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{ label:'Ventas (S/)', data: <?= json_encode($montos) ?>,
            backgroundColor:'rgba(142,68,173,.15)', borderColor:'#8e44ad',
            borderWidth:2, borderRadius:8 }]
    },
    options: { responsive:true, plugins:{legend:{display:false}},
        scales:{ y:{beginAtZero:true, ticks:{callback:v=>'S/ '+v}}, x:{grid:{display:false}} } }
});

<?php if (!empty($datos['metodos'])): ?>
new Chart(document.getElementById('chartMetodos').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_map(fn($m) => ucfirst($m['metodo_pago']), $datos['metodos'])) ?>,
        datasets: [{ data: <?= json_encode(array_map(fn($m) => (float)$m['monto'], $datos['metodos'])) ?>,
            backgroundColor:['#8e44ad','#2980b9','#27ae60','#e67e22','#e74c3c'], borderWidth:0 }]
    },
    options: { responsive:true, plugins:{legend:{position:'bottom',labels:{font:{size:12}}}} }
});
<?php endif; ?>

setTimeout(() => location.reload(), 60000);
</script>