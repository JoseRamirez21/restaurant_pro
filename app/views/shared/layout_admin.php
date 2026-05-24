<?php
// Detectar página activa para el sidebar/
/** @var string $essololectura */
$url_actual = $_GET['url'] ?? '';
$seccion    = explode('/', $url_actual)[0] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'RestaurantePro' ?> — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <?php if (!empty($page_styles)) echo $page_styles; ?>
    <style>
        :root {
            --sidebar-w : 240px;
            --purple    : #8e44ad;
            --dark      : #2c1f3e;
            --bg        : #f4f1eb;
            --border    : #e8e5df;
        }
        * { box-sizing: border-box; }
        body { background: var(--bg); margin: 0; font-family: system-ui, sans-serif; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--dark);
            color: #fff;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 300;
            transition: transform .25s ease;
        }
        .sidebar-brand {
            padding: 1.2rem 1.4rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sidebar-brand small {
            display: block;
            font-size: 11px;
            opacity: .4;
            font-weight: 400;
            margin-top: 2px;
        }
        .nav-section {
            font-size: 10px;
            letter-spacing: .08em;
            opacity: .35;
            padding: .9rem 1.4rem .25rem;
            text-transform: uppercase;
        }
        .nav-link {
            color: rgba(255,255,255,.65);
            padding: .55rem 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            text-decoration: none;
            transition: all .15s;
            border-left: 3px solid transparent;
        }
        .nav-link i { font-size: 17px; width: 20px; flex-shrink: 0; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,.07); }
        .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.09);
            border-left-color: var(--purple);
        }
        .nav-badge {
            margin-left: auto;
            background: #e94560;
            color: #fff;
            font-size: 10px;
            padding: 1px 7px;
            border-radius: 20px;
            font-weight: 600;
        }
        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 1.4rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .user-pill {
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--purple);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: .7rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 200;
        }
        .topbar-title {
            font-size: 15px;
            font-weight: 600;
        }
        .topbar-sub {
            font-size: 11px;
            color: #aaa;
        }
        .content { padding: 1.5rem; flex: 1; }

        /* ── OVERLAY móvil ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 299;
        }
        .sidebar-overlay.show { display: block; }

        /* ── MOBILE ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Overlay móvil -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="cerrarSidebar()"></div>

<!-- SIDEBAR -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-shop"></i>
        <div>
            RestaurantePro
            <small>Administración</small>
        </div>
    </div>

    <div class="nav-section">Principal</div>
    <a href="<?= APP_URL ?>/admin"   class="nav-link <?= $seccion === 'admin'   ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i>Dashboard
    </a>
    <a href="<?= APP_URL ?>/mesas"   class="nav-link <?= $seccion === 'mesas'   ? 'active' : '' ?>">
        <i class="bi bi-layout-grid"></i>Mesas
        <?php
        // Mostrar badge si hay mesas ocupadas
        if (!empty($mesas_ocupadas) && $mesas_ocupadas > 0):
        ?>
        <span class="nav-badge"><?= $mesas_ocupadas ?></span>
        <?php endif; ?>
    </a>
        <a href="<?= APP_URL ?>/mesas/gestionar" class="nav-link">
        <i class="bi bi-pencil-square"></i>Gestionar mesas
    </a>
    <a href="<?= APP_URL ?>/cocina"  class="nav-link <?= $seccion === 'cocina'  ? 'active' : '' ?>">
        <i class="bi bi-fire"></i>Cocina
        <?php if (!empty($cocina_pendientes) && $cocina_pendientes > 0): ?>
        <span class="nav-badge"><?= $cocina_pendientes ?></span>
        <?php endif; ?>
    </a>
    <a href="<?= APP_URL ?>/caja"    class="nav-link <?= $seccion === 'caja'    ? 'active' : '' ?>">
        <i class="bi bi-cash-register"></i>Caja
    </a>
    <a href="<?= APP_URL ?>/cierre" class="nav-link <?= $seccion === 'cierre' ? 'active' : '' ?>">
        <i class="bi bi-lock"></i>Cierre de caja
    </a>

    <div class="nav-section">Gestión</div>
    <a href="<?= APP_URL ?>/productos" class="nav-link <?= $seccion === 'productos' ? 'active' : '' ?>">
        <i class="bi bi-menu-button-wide"></i>Carta & Menú
    </a>
    <a href="<?= APP_URL ?>/categorias" class="nav-link <?= $seccion === 'categorias' ? 'active' : '' ?>">
        <i class="bi bi-tags"></i>Categorías
    </a>
    <a href="<?= APP_URL ?>/inventario" class="nav-link <?= $seccion === 'inventario' ? 'active' : '' ?>">
        <i class="bi bi-box-seam"></i>Inventario
    </a>
    <a href="<?= APP_URL ?>/usuarios" class="nav-link <?= $seccion === 'usuarios' ? 'active' : '' ?>">
        <i class="bi bi-people"></i>Personal
    </a>
    <a href="<?= APP_URL ?>/clientes" class="nav-link <?= $seccion === 'clientes' ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i>Clientes & CRM
    </a>
    <a href="<?= APP_URL ?>/reservas" class="nav-link <?= $seccion === 'reservas' ? 'active' : '' ?>">
        <i class="bi bi-calendar-check"></i>Reservas
    </a>

    <div class="nav-section">Reportes</div>
    <a href="<?= APP_URL ?>/reportes" class="nav-link <?= $seccion === 'reportes' ? 'active' : '' ?>">
        <i class="bi bi-bar-chart-line"></i>Reportes & Ventas
    </a>
    <a href="#" class="nav-link">
        <i class="bi bi-graph-up"></i>Analítica
    </a>

    <div class="nav-section">Sistema</div>
    <a href="<?= APP_URL ?>/configuracion" class="nav-link <?= $seccion === 'configuracion' ? 'active' : '' ?>">
        <i class="bi bi-gear"></i>Configuración
    </a>

    <div class="sidebar-footer">
        <div class="user-pill">
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['nombre'] ?? 'A', 0, 1)) ?>
            </div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:13px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?= htmlspecialchars($_SESSION['nombre'] ?? '') ?>
                </div>
                <div style="font-size:11px; opacity:.4;">
                    <?= ucfirst($_SESSION['rol'] ?? '') ?>
                </div>
            </div>
            <a href="<?= APP_URL ?>/perfil" class="btn btn-sm" style="color:rgba(255,255,255,.4);" title="Mi perfil">
                <i class="bi bi-person-circle"></i>
            </a>
            <a href="<?= APP_URL ?>/logout"
               class="btn btn-sm"
               style="color:rgba(255,255,255,.35);"
               title="Cerrar sesión">
                <i class="bi bi-box-arrow-left"></i>
            </a>
        </div>
    </div>
</nav>

<!-- MAIN WRAPPER -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <!-- Botón hamburguesa solo en móvil -->
            <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="abrirSidebar()">
                <i class="bi bi-list" style="font-size:18px;"></i>
            </button>
            <div>
                <div class="topbar-title"><?= $page_title ?? 'Panel' ?></div>
                <div class="topbar-sub" id="reloj-topbar"></div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if (!empty($topbar_extra)) echo $topbar_extra; ?>
            <span class="badge bg-success" style="font-size:11px;">● En línea</span>
            <?php if (esSoloLectura()): ?>
            <span class="badge bg-warning text-dark" style="font-size:11px;">
                <i class="bi bi-eye"></i> Solo lectura
            </span>
            <?php endif; ?>
            <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()" title="Actualizar datos">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    <!-- CONTENIDO — cada vista lo llena -->
    <div class="content">