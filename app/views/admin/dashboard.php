<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f4f1eb; }
        .sidebar {
            width: 240px; min-height: 100vh;
            background: #2c1f3e; color: #fff;
            position: fixed; top: 0; left: 0;
            padding: 1.5rem 0; z-index: 100;
            transition: transform .3s;
        }
        .sidebar .brand {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            font-size: 1.1rem; font-weight: 500;
        }
        .sidebar .brand small { display:block; font-size:11px; opacity:.5; margin-top:2px; }
        .sidebar .nav-link {
            color: rgba(255,255,255,.7); padding: .6rem 1.5rem;
            border-radius: 0; display: flex; align-items: center; gap: 10px;
            font-size: 14px; transition: all .2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff; background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link i { font-size: 18px; }
        .sidebar .nav-section {
            font-size: 10px; letter-spacing: .08em; opacity: .4;
            padding: 1rem 1.5rem .3rem; text-transform: uppercase;
        }
        .main-content { margin-left: 240px; padding: 1.5rem; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e0ddd6;
            padding: .75rem 1.5rem; display: flex;
            align-items: center; justify-content: space-between;
            margin: -1.5rem -1.5rem 1.5rem; position: sticky; top: 0; z-index: 50;
        }
        .stat-card {
            background: #fff; border: 1px solid #e0ddd6;
            border-radius: 12px; padding: 1.25rem 1.5rem;
        }
        .stat-card .num { font-size: 2rem; font-weight: 500; }
        .stat-card .lbl { font-size: 13px; color: #888; }
        .stat-card .icon {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-240px); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="brand">
        <i class="bi bi-shop me-2"></i>RestaurantePro
        <small>Panel de administración</small>
    </div>

    <div class="nav-section">Principal</div>
    <a href="#" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="#" class="nav-link"><i class="bi bi-layout-grid"></i> Mesas</a>
    <a href="#" class="nav-link"><i class="bi bi-receipt"></i> Pedidos</a>

    <div class="nav-section">Gestión</div>
    <a href="#" class="nav-link"><i class="bi bi-menu-button-wide"></i> Carta & Menú</a>
    <a href="#" class="nav-link"><i class="bi bi-box-seam"></i> Inventario</a>
    <a href="#" class="nav-link"><i class="bi bi-people"></i> Personal</a>
    <a href="#" class="nav-link"><i class="bi bi-calendar-check"></i> Reservas</a>

    <div class="nav-section">Reportes</div>
    <a href="#" class="nav-link"><i class="bi bi-bar-chart-line"></i> Ventas</a>
    <a href="#" class="nav-link"><i class="bi bi-graph-up"></i> Analítica</a>

    <div class="nav-section">Sistema</div>
    <a href="#" class="nav-link"><i class="bi bi-gear"></i> Configuración</a>
    <a href="<?= APP_URL ?>/logout" class="nav-link text-danger">
        <i class="bi bi-box-arrow-left"></i> Cerrar sesión
    </a>
</div>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <span style="font-size:15px; font-weight:500;">Dashboard</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success">● Sistema activo</span>
            <div class="d-flex align-items-center gap-2">
                <div style="width:32px;height:32px;border-radius:50%;background:#8e44ad;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:500;">
                    <?= strtoupper(substr($_SESSION['nombre'] ?? 'A', 0, 1)) ?>
                </div>
                <div style="font-size:13px; line-height:1.2;">
                    <div style="font-weight:500;"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></div>
                    <div style="color:#888;font-size:11px;"><?= ucfirst($_SESSION['rol'] ?? '') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- TARJETAS DE ESTADÍSTICAS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="icon" style="background:#e8f5e9; color:#2e7d32;">
                    <i class="bi bi-layout-grid"></i>
                </div>
                <div>
                    <div class="num">10</div>
                    <div class="lbl">Mesas totales</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="icon" style="background:#fff3e0; color:#e65100;">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="num">0</div>
                    <div class="lbl">Pedidos hoy</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="icon" style="background:#f3e5f5; color:#6a1b9a;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div class="num">S/ 0</div>
                    <div class="lbl">Ventas hoy</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="icon" style="background:#e3f2fd; color:#1565c0;">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="num">5</div>
                    <div class="lbl">Personal activo</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ACCESOS RÁPIDOS -->
    <div class="row g-3">
        <div class="col-12">
            <div class="stat-card">
                <h6 class="mb-3" style="font-weight:500;">Accesos rápidos</h6>
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <a href="#" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center gap-1 py-3">
                            <i class="bi bi-layout-grid" style="font-size:1.5rem;"></i>
                            <span style="font-size:13px;">Ver mesas</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="#" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center gap-1 py-3">
                            <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
                            <span style="font-size:13px;">Nuevo pedido</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="#" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center gap-1 py-3">
                            <i class="bi bi-menu-button-wide" style="font-size:1.5rem;"></i>
                            <span style="font-size:13px;">Gestionar carta</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="#" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center gap-1 py-3">
                            <i class="bi bi-bar-chart-line" style="font-size:1.5rem;"></i>
                            <span style="font-size:13px;">Ver reportes</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}
</script>
</body>
</html>