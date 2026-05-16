
<?php
// Solo para autocompletado del editor — no afecta el sistema
/** @var array $datos */
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root { --sidebar-w: 240px; --purple: #8e44ad; --dark: #2c1f3e; }
        * { box-sizing: border-box; }
        body { background: #f4f1eb; margin: 0; font-family: system-ui, sans-serif; }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w); min-height: 100vh;
            background: var(--dark); color: #fff;
            position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column;
            z-index: 200; transition: transform .3s;
        }
        .sidebar-brand {
            padding: 1.2rem 1.4rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            font-size: 15px; font-weight: 600;
        }
        .sidebar-brand small { display:block; font-size:11px; opacity:.4; font-weight:400; margin-top:2px; }
        .nav-section { font-size:10px; letter-spacing:.08em; opacity:.35; padding:.9rem 1.4rem .25rem; text-transform:uppercase; }
        .nav-link {
            color: rgba(255,255,255,.65); padding: .55rem 1.4rem;
            display: flex; align-items: center; gap: 10px;
            font-size: 13.5px; text-decoration: none; transition: all .15s;
        }
        .nav-link i { font-size: 17px; width: 20px; }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,.09); }
        .nav-link.active { border-left: 3px solid var(--purple); }
        .sidebar-footer { margin-top: auto; padding: 1rem 1.4rem; border-top: 1px solid rgba(255,255,255,.08); }
        .user-pill { display:flex; align-items:center; gap:.6rem; }
        .user-avatar { width:32px; height:32px; border-radius:50%; background:var(--purple); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; flex-shrink:0; }

        /* MAIN */
        .main { margin-left: var(--sidebar-w); display: flex; flex-direction: column; min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e8e5df;
            padding: .7rem 1.5rem; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 100;
        }
        .content { padding: 1.5rem; flex: 1; }

        /* STAT CARDS */
        .stat-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.2rem 1.4rem; height:100%; }
        .stat-card .icon { width:46px; height:46px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:21px; flex-shrink:0; }
        .stat-card .num { font-size:1.7rem; font-weight:700; line-height:1; margin-bottom:2px; }
        .stat-card .lbl { font-size:12.5px; color:#888; }
        .stat-card .sub { font-size:11.5px; color:#aaa; margin-top:3px; }

        /* MESAS MINI */
        .mesa-mini { display:inline-flex; align-items:center; gap:5px; font-size:13px; padding:4px 10px; border-radius:20px; font-weight:500; }
        .mesa-mini.libre      { background:#e8f5e9; color:#2e7d32; }
        .mesa-mini.ocupada    { background:#ffebee; color:#c62828; }
        .mesa-mini.reservada  { background:#fff3e0; color:#e65100; }

        /* TABLA */
        .tabla-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
        .tabla-card .t-header { padding:.9rem 1.2rem; border-bottom:1px solid #e8e5df; font-size:14px; font-weight:600; display:flex; align-items:center; justify-content:space-between; }
        .tabla-card table { width:100%; border-collapse:collapse; }
        .tabla-card th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.6rem 1.2rem; border-bottom:1px solid #f0ede7; text-align:left; }
        .tabla-card td { font-size:13px; padding:.65rem 1.2rem; border-bottom:1px solid #f7f5f0; }
        .tabla-card tr:last-child td { border-bottom:none; }
        .estado-pill { font-size:11px; padding:2px 9px; border-radius:20px; font-weight:500; }
        .estado-abierto    { background:#e3f2fd; color:#1565c0; }
        .estado-en_cocina  { background:#fff3e0; color:#e65100; }
        .estado-listo      { background:#e8f5e9; color:#2e7d32; }

        /* CHART */
        .chart-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.2rem 1.4rem; }
        .chart-card .c-title { font-size:14px; font-weight:600; margin-bottom:1rem; }

        /* MOBILE */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:199; }
            .overlay.show { display:block; }
        }
    </style>
</head>
<body>

<!-- Overlay móvil -->
<div class="overlay" id="overlay" onclick="cerrarSidebar()"></div>

<!-- SIDEBAR -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-shop me-2"></i>RestaurantePro
        <small>Panel de administración</small>
    </div>

    <div class="nav-section">Principal</div>
    <a href="<?= APP_URL ?>/admin"    class="nav-link active"><i class="bi bi-speedometer2"></i>Dashboard</a>
    <a href="<?= APP_URL ?>/mesas"    class="nav-link"><i class="bi bi-layout-grid"></i>Mesas</a>
    <a href="<?= APP_URL ?>/cocina"   class="nav-link"><i class="bi bi-fire"></i>Cocina</a>
    <a href="<?= APP_URL ?>/caja"     class="nav-link"><i class="bi bi-cash-register"></i>Caja</a>

    <div class="nav-section">Gestión</div>
    <a href="<?= APP_URL ?>/productos" class="nav-link"><i class="bi bi-menu-button-wide"></i>Carta & Menú</a>
    <a href="#" class="nav-link"><i class="bi bi-box-seam"></i>Inventario</a>
    <a href="#" class="nav-link"><i class="bi bi-people"></i>Personal</a>
    <a href="#" class="nav-link"><i class="bi bi-calendar-check"></i>Reservas</a>

    <div class="nav-section">Reportes</div>
    <a href="#" class="nav-link"><i class="bi bi-bar-chart-line"></i>Ventas</a>

    <div class="nav-section">Sistema</div>
    <a href="#" class="nav-link"><i class="bi bi-gear"></i>Configuración</a>

    <div class="sidebar-footer">
        <div class="user-pill">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['nombre'] ?? 'A', 0, 1)) ?></div>
            <div>
                <div style="font-size:13px; font-weight:500;"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></div>
                <div style="font-size:11px; opacity:.4;"><?= ucfirst($_SESSION['rol'] ?? '') ?></div>
            </div>
            <a href="<?= APP_URL ?>/logout" class="ms-auto btn btn-sm" style="color:rgba(255,255,255,.4);" title="Cerrar sesión">
                <i class="bi bi-box-arrow-left"></i>
            </a>
        </div>
    </div>
</nav>

<!-- MAIN -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="abrirSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <div style="font-size:15px; font-weight:600;">Dashboard</div>
                <div style="font-size:11px; color:#aaa;" id="fecha-hora"></div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success" style="font-size:11px;">● Sistema activo</span>
            <?php if ($datos['cocina'] > 0): ?>
            <span class="badge bg-warning text-dark" style="font-size:11px;">
                <i class="bi bi-fire"></i> <?= $datos['cocina'] ?> en cocina
            </span>
            <?php endif; ?>
            <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()" title="Actualizar">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    <!-- CONTENIDO -->
    <div class="content">

        <!-- FILA 1: Stat cards principales -->
        <div class="row g-3 mb-3">

            <div class="col-6 col-lg-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="icon" style="background:#e8f5e9; color:#2e7d32;"><i class="bi bi-cash-stack"></i></div>
                    <div>
                        <div class="num" style="color:#2e7d32;">S/ <?= number_format($datos['ventas']['total_ventas'], 2) ?></div>
                        <div class="lbl">Ventas hoy</div>
                        <div class="sub"><?= $datos['ventas']['total_pedidos'] ?> pedidos cerrados</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="icon" style="background:#fff3e0; color:#e65100;"><i class="bi bi-receipt"></i></div>
                    <div>
                        <div class="num" style="color:#e65100;"><?= $datos['activos'] ?></div>
                        <div class="lbl">Pedidos activos</div>
                        <div class="sub"><?= $datos['cocina'] ?> ítem(s) en cocina</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="icon" style="background:#f3e5f5; color:#6a1b9a;"><i class="bi bi-layout-grid"></i></div>
                    <div>
                        <div class="num" style="color:#6a1b9a;"><?= $datos['mesas']['ocupadas'] ?> / <?= $datos['mesas']['total'] ?></div>
                        <div class="lbl">Mesas ocupadas</div>
                        <div class="sub"><?= $datos['mesas']['libres'] ?> libres · <?= $datos['mesas']['reservadas'] ?> reservadas</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="icon" style="background:#e3f2fd; color:#1565c0;"><i class="bi bi-graph-up-arrow"></i></div>
                    <div>
                        <div class="num" style="color:#1565c0;">S/ <?= number_format($datos['ventas']['ticket_promedio'], 2) ?></div>
                        <div class="lbl">Ticket promedio</div>
                        <div class="sub">S/ <?= number_format($datos['ventas']['total_propinas'], 2) ?> en propinas</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- FILA 2: Gráfico semana + Métodos de pago -->
        <div class="row g-3 mb-3">

            <div class="col-lg-8">
                <div class="chart-card">
                    <div class="c-title">Ventas últimos 7 días</div>
                    <canvas id="chartSemana" height="100"></canvas>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="chart-card h-100">
                    <div class="c-title">Métodos de pago hoy</div>
                    <?php if (empty($datos['metodos'])): ?>
                        <div style="text-align:center; color:#ccc; padding:2rem;">
                            <i class="bi bi-credit-card" style="font-size:2rem;"></i>
                            <p style="font-size:13px; margin-top:.5rem;">Sin cobros hoy aún</p>
                        </div>
                    <?php else: ?>
                        <canvas id="chartMetodos" height="180"></canvas>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- FILA 3: Pedidos activos + Top platos -->
        <div class="row g-3">

            <div class="col-lg-7">
                <div class="tabla-card">
                    <div class="t-header">
                        <span><i class="bi bi-receipt me-2"></i>Pedidos activos ahora</span>
                        <a href="<?= APP_URL ?>/mesas" style="font-size:12px; color:#8e44ad; text-decoration:none;">Ver mesas →</a>
                    </div>
                    <?php if (empty($datos['pedidos_activos'])): ?>
                    <div style="text-align:center; color:#ccc; padding:2rem;">
                        <i class="bi bi-check-circle" style="font-size:2rem;"></i>
                        <p style="font-size:13px; margin-top:.5rem;">Sin pedidos activos</p>
                    </div>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Mesa</th>
                                <th>Mozo</th>
                                <th>Estado</th>
                                <th>Tiempo</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datos['pedidos_activos'] as $p): ?>
                            <tr>
                                <td><strong>Mesa <?= $p['mesa_numero'] ?></strong><br><span style="font-size:11px;color:#aaa;"><?= ucfirst($p['zona']) ?></span></td>
                                <td><?= htmlspecialchars($p['mesero']) ?></td>
                                <td><span class="estado-pill estado-<?= $p['estado'] ?>"><?= ucfirst(str_replace('_', ' ', $p['estado'])) ?></span></td>
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
                        <span><i class="bi bi-trophy me-2"></i>Platos más vendidos</span>
                        <span style="font-size:11px; color:#aaa;">Hoy / este mes</span>
                    </div>
                    <?php if (empty($datos['top_platos'])): ?>
                    <div style="text-align:center; color:#ccc; padding:2rem;">
                        <i class="bi bi-bar-chart" style="font-size:2rem;"></i>
                        <p style="font-size:13px; margin-top:.5rem;">Sin ventas registradas</p>
                    </div>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Plato</th>
                                <th>Cant.</th>
                                <th>S/</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datos['top_platos'] as $i => $p): ?>
                            <tr>
                                <td style="color:#aaa;"><?= $i + 1 ?></td>
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

    </div><!-- /content -->
</div><!-- /main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Reloj en topbar
function actualizarFecha() {
    const ahora = new Date();
    document.getElementById('fecha-hora').textContent =
        ahora.toLocaleDateString('es-PE', {weekday:'long', day:'numeric', month:'long'}) +
        ' · ' + ahora.toLocaleTimeString('es-PE');
}
actualizarFecha();
setInterval(actualizarFecha, 1000);

// Sidebar móvil
function abrirSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('overlay').classList.add('show');
}
function cerrarSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
}

// Gráfico ventas semana
<?php
$labels  = [];
$montos  = [];
$diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
// Llenar los 7 días aunque no haya ventas
for ($i = 6; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("-$i days"));
    $dia   = $diasSemana[date('w', strtotime($fecha))];
    $labels[] = $dia;
    $monto = 0;
    foreach ($datos['semana'] as $s) {
        if ($s['dia'] === $fecha) { $monto = (float)$s['ventas']; break; }
    }
    $montos[] = $monto;
}
?>
const ctxSemana = document.getElementById('chartSemana').getContext('2d');
new Chart(ctxSemana, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Ventas (S/)',
            data: <?= json_encode($montos) ?>,
            backgroundColor: 'rgba(142, 68, 173, 0.15)',
            borderColor: '#8e44ad',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v.toFixed(0) } },
            x: { grid: { display: false } }
        }
    }
});

// Gráfico métodos de pago
<?php if (!empty($datos['metodos'])): ?>
const ctxMetodos = document.getElementById('chartMetodos').getContext('2d');
new Chart(ctxMetodos, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_map(fn($m) => ucfirst($m['metodo_pago']), $datos['metodos'])) ?>,
        datasets: [{
            data: <?= json_encode(array_map(fn($m) => (float)$m['monto'], $datos['metodos'])) ?>,
            backgroundColor: ['#8e44ad','#2980b9','#27ae60','#e67e22','#e74c3c'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 12 } } }
        }
    }
});
<?php endif; ?>

// Auto-refresh cada 60 segundos
setTimeout(() => location.reload(), 60000);
</script>
</body>
</html>