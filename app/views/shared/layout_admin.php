<?php
$url_actual = $_GET['url'] ?? '';
$seccion    = explode('/', $url_actual)[0] ?? 'admin';
$solo_lec   = esSoloLectura();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Panel' ?> — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    
:root{
    --sidebar-w:220px;
    --purple:#8e44ad;
    --dark:#2c1f3e;
    --bg:#f4f1eb;
    --border:#e8e5df;
}

*{
    box-sizing:border-box;
}

body{
    background:var(--bg);
    margin:0;
    font-family:system-ui,sans-serif;
}

/* SIDEBAR */
.sidebar{
    width:var(--sidebar-w);
    height:100vh;
    background:var(--dark);
    color:#fff;
    position:fixed;
    top:0;
    left:0;
    display:flex;
    flex-direction:column;
    z-index:300;
    transition:transform .25s ease;
    overflow:hidden;
}

/* LOGO */
.sidebar-brand{
    padding:.7rem 1rem;
    border-bottom:1px solid rgba(255,255,255,.08);
    font-size:14px;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:8px;
}

.sidebar-brand small{
    display:block;
    font-size:10px;
    opacity:.4;
    font-weight:400;
    margin-top:1px;
}

/* TITULOS */
.nav-section{
    font-size:9px;
    letter-spacing:.06em;
    opacity:.35;
    padding:.35rem 1rem .10rem;
    text-transform:uppercase;
}

/* LINKS */
.nav-link{
    color:rgba(255,255,255,.65);
    padding:.38rem 1rem;
    display:flex;
    align-items:center;
    gap:8px;
    font-size:12px;
    text-decoration:none;
    transition:all .15s;
    border-left:3px solid transparent;
    min-height:30px;
}

.nav-link i{
    font-size:14px;
    width:18px;
    flex-shrink:0;
}

.nav-link:hover{
    color:#fff;
    background:rgba(255,255,255,.07);
}

.nav-link.active{
    color:#fff;
    background:rgba(255,255,255,.09);
    border-left-color:var(--purple);
}

.nav-badge{
    margin-left:auto;
    background:#e94560;
    color:#fff;
    font-size:9px;
    padding:1px 6px;
    border-radius:20px;
    font-weight:600;
}

/* FOOTER */
.sidebar-footer{
    margin-top:auto;
    padding:.7rem 1rem;
    border-top:1px solid rgba(255,255,255,.08);
}

.user-pill{
    display:flex;
    align-items:center;
    gap:.5rem;
}

.user-avatar{
    width:28px;
    height:28px;
    border-radius:50%;
    background:var(--purple);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
    font-weight:600;
    flex-shrink:0;
}

/* MAIN */
.main{
    margin-left:var(--sidebar-w);
    display:flex;
    flex-direction:column;
    min-height:100vh;
}

.topbar{
    background:#fff;
    border-bottom:1px solid var(--border);
    padding:.7rem 1.5rem;
    display:flex;
    align-items:center;
    justify-content:space-between;
    position:sticky;
    top:0;
    z-index:200;
}

.topbar-title{
    font-size:15px;
    font-weight:600;
}

.topbar-sub{
    font-size:11px;
    color:#aaa;
}

.content{
    padding:1.5rem;
    flex:1;
}

/* SOLO LECTURA */
.banner-solo-lec{
    background:#fff3cd;
    border-bottom:1px solid #ffc107;
    padding:.5rem 1.5rem;
    font-size:12px;
    color:#856404;
    display:flex;
    align-items:center;
    gap:.5rem;
}

/* OVERLAY */
.sidebar-overlay{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.45);
    z-index:299;
}

.sidebar-overlay.show{
    display:block;
}

/* RESPONSIVE */
@media (max-width:768px){

    .sidebar{
        transform:translateX(calc(-1 * var(--sidebar-w)));
    }

    .sidebar.open{
        transform:translateX(0);
    }

    .main{
        margin-left:0;
    }
}

    </style>
    <?php if (!empty($page_styles)) echo $page_styles; ?>
</head>
<body>
 
<div class="sidebar-overlay" id="sidebarOverlay" onclick="cerrarSidebar()"></div>
 
<!-- SIDEBAR -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-shop"></i>
        <div>
            RestaurantePro
            <small><?= $solo_lec ? 'Supervisor' : 'Administración' ?></small>
        </div>
    </div>
 
    <div class="nav-section">Principal</div>
    <a href="<?= APP_URL ?>/admin"   class="nav-link <?= $seccion==='admin'  ?'active':'' ?>"><i class="bi bi-speedometer2"></i>Dashboard</a>
    <a href="<?= APP_URL ?>/mesas"   class="nav-link <?= $seccion==='mesas'  ?'active':'' ?>"><i class="bi bi-layout-grid"></i>Mesas</a>
    <?php if (!$solo_lec): ?>
    <a href="<?= APP_URL ?>/mesas/gestionar" class="nav-link"><i class="bi bi-pencil-square"></i>Gestionar mesas</a>
    <?php endif; ?>
    <a href="<?= APP_URL ?>/cocina"  class="nav-link <?= $seccion==='cocina' ?'active':'' ?>"><i class="bi bi-fire"></i>Cocina</a>
    <a href="<?= APP_URL ?>/caja"    class="nav-link <?= $seccion==='caja'   ?'active':'' ?>"><i class="bi bi-cash-register"></i>Caja</a>
    <a href="<?= APP_URL ?>/cierre"  class="nav-link <?= $seccion==='cierre' ?'active':'' ?>"><i class="bi bi-lock"></i>Cierre de caja</a>
 
    <div class="nav-section">Gestión</div>
    <a href="<?= APP_URL ?>/carta/qr" class="nav-link <?= $seccion==='carta' ?'active':'' ?>">
        <i class="bi bi-qr-code"></i>QR Carta digital
    </a>
    <a href="<?= APP_URL ?>/productos"  class="nav-link <?= $seccion==='productos'  ?'active':'' ?>"><i class="bi bi-menu-button-wide"></i>Carta & Menú</a>
    <a href="<?= APP_URL ?>/categorias" class="nav-link <?= $seccion==='categorias' ?'active':'' ?>"><i class="bi bi-tags"></i>Categorías</a>
    <a href="<?= APP_URL ?>/inventario" class="nav-link <?= $seccion==='inventario' ?'active':'' ?>"><i class="bi bi-box-seam"></i>Inventario</a>
    <a href="<?= APP_URL ?>/reservas"   class="nav-link <?= $seccion==='reservas'   ?'active':'' ?>"><i class="bi bi-calendar-check"></i>Reservas</a>
    <a href="<?= APP_URL ?>/clientes"   class="nav-link <?= $seccion==='clientes'   ?'active':'' ?>"><i class="bi bi-people-fill"></i>Clientes & CRM</a>
    <?php if (!$solo_lec): ?>
    <a href="<?= APP_URL ?>/usuarios"   class="nav-link <?= $seccion==='usuarios'   ?'active':'' ?>"><i class="bi bi-people"></i>Personal</a>
    <?php endif; ?>
 
    <div class="nav-section">Reportes</div>
    <a href="<?= APP_URL ?>/reportes"  class="nav-link <?= $seccion==='reportes'  ?'active':'' ?>"><i class="bi bi-bar-chart-line"></i>Reportes & Ventas</a>
    <a href="<?= APP_URL ?>/analitica" class="nav-link <?= $seccion==='analitica' ?'active':'' ?>"><i class="bi bi-graph-up"></i>Analítica</a>
 
    <?php if (!$solo_lec): ?>
    <div class="nav-section">Sistema</div>
    <a href="<?= APP_URL ?>/configuracion" class="nav-link <?= $seccion==='configuracion' ?'active':'' ?>"><i class="bi bi-gear"></i>Configuración</a>
    <?php endif; ?>
 
    <div class="sidebar-footer">
        <div class="user-pill">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['nombre']??'A',0,1)) ?></div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <?= htmlspecialchars($_SESSION['nombre']??'') ?>
                </div>
                <div style="font-size:11px;opacity:.4;"><?= ucfirst($_SESSION['rol']??'') ?></div>
            </div>
            <a href="<?= APP_URL ?>/perfil" class="btn btn-sm" style="color:rgba(255,255,255,.35);" title="Mi perfil">
                <i class="bi bi-person-circle"></i>
            </a>
            <a href="<?= APP_URL ?>/logout" class="btn btn-sm" style="color:rgba(255,255,255,.35);" title="Cerrar sesión">
                <i class="bi bi-box-arrow-left"></i>
            </a>
        </div>
    </div>
</nav>
 
<!-- MAIN -->
<div class="main">
 
    <!-- Banner solo lectura -->
    <?php if ($solo_lec): ?>
    <div class="banner-solo-lec">
        <i class="bi bi-eye"></i>
        <strong>Modo supervisor</strong> — Puedes ver todo pero no modificar datos del sistema.
    </div>
    <?php endif; ?>
 
    <!-- TOPBAR -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
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
            <?php if ($solo_lec): ?>
            <span class="badge bg-warning text-dark" style="font-size:11px;">
                <i class="bi bi-eye me-1"></i>Solo lectura
            </span>
            <?php else: ?>
            <span class="badge bg-success" style="font-size:11px;">● En línea</span>
            <?php endif; ?>
            <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()" title="Actualizar">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
 
    <div class="content">
    <div class="content">