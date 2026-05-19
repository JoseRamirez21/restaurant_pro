<?php
// Solo para autocompletado del editor — no afecta el sistema
/** @var array $mesas */
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f1eb; }
        .topbar { background:#2c1f3e; color:#fff; padding:.9rem 1.2rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .topbar .brand { font-size:15px; font-weight:500; }
        .mesa-card { background:#fff; border:2px solid #e0ddd6; border-radius:14px; padding:1.1rem; text-align:center; cursor:pointer; transition:all .2s; text-decoration:none; color:inherit; display:block; }
        .mesa-card:hover { transform:translateY(-3px); box-shadow:0 4px 16px rgba(0,0,0,.1); color:inherit; }
        .mesa-card.libre    { border-color:#28a745; }
        .mesa-card.ocupada  { border-color:#dc3545; background:#fff8f8; }
        .mesa-card.reservada{ border-color:#fd7e14; background:#fffaf5; }
        .mesa-card.mantenimiento { border-color:#aaa; background:#f8f8f8; opacity:.6; cursor:not-allowed; }
        .mesa-icon { font-size:2.2rem; margin-bottom:.4rem; }
        .libre     .mesa-icon { color:#28a745; }
        .ocupada   .mesa-icon { color:#dc3545; }
        .reservada .mesa-icon { color:#fd7e14; }
        .mesa-num  { font-size:1.05rem; font-weight:600; margin-bottom:.2rem; }
        .mesa-info { font-size:11px; color:#888; margin-bottom:.4rem; }
        .estado-badge { font-size:11px; padding:3px 10px; border-radius:20px; font-weight:500; }
        .libre     .estado-badge { background:#e8f5e9; color:#2e7d32; }
        .ocupada   .estado-badge { background:#ffebee; color:#c62828; }
        .reservada .estado-badge { background:#fff3e0; color:#e65100; }
        .mantenimiento .estado-badge { background:#f1f1f1; color:#888; }
        .zona-header { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .5rem; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="brand"><i class="bi bi-layout-grid me-2"></i>Mesas</div>
    <div class="d-flex align-items-center gap-3">
        <span style="font-size:12px; opacity:.7;">
            <i class="bi bi-person-circle me-1"></i>
            <?= htmlspecialchars($_SESSION['nombre'] ?? '') ?>
        </span>
        <a href="<?= APP_URL ?>/logout" class="btn btn-sm btn-outline-light" title="Cerrar sesión">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
</div>

<div class="container-fluid p-3">

    <!-- Leyenda -->
    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <span style="font-size:14px; font-weight:500;">Plano de mesas</span>
        <div class="d-flex gap-2 ms-auto flex-wrap">
            <span class="estado-badge" style="background:#e8f5e9;color:#2e7d32;">● Libre</span>
            <span class="estado-badge" style="background:#ffebee;color:#c62828;">● Ocupada</span>
            <span class="estado-badge" style="background:#fff3e0;color:#e65100;">● Reservada</span>
        </div>
    </div>

    <?php
    $zonas = [];
    foreach ($mesas as $m) {
        $zonas[$m['zona']][] = $m;
    }
    $zonas_nombres = [
        'salon_principal' => 'Salón principal',
        'terraza'         => 'Terraza',
        'privado'         => 'Sala privada',
        'bar'             => 'Bar',
    ];
    foreach ($zonas as $zona => $lista):
    ?>
    <div class="zona-header"><?= $zonas_nombres[$zona] ?? $zona ?></div>
    <div class="row g-2 mb-2">
        <?php foreach ($lista as $m):
            $href = $m['estado'] === 'mantenimiento' ? '#' : APP_URL . '/mesas/abrir/' . $m['id'];
        ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a href="<?= $href ?>" class="mesa-card <?= $m['estado'] ?>">
                <div class="mesa-icon">
                    <?php if ($m['estado'] === 'libre'): ?>
                        <i class="bi bi-circle-fill" style="font-size:1rem; color:#28a745;"></i><br>
                        <i class="bi bi-table"></i>
                    <?php elseif ($m['estado'] === 'ocupada'): ?>
                        <i class="bi bi-people-fill"></i>
                    <?php elseif ($m['estado'] === 'reservada'): ?>
                        <i class="bi bi-calendar-check-fill"></i>
                    <?php else: ?>
                        <i class="bi bi-tools"></i>
                    <?php endif; ?>
                </div>
                <div class="mesa-num"><?= htmlspecialchars($m['nombre']) ?></div>
                <div class="mesa-info">
                    <i class="bi bi-people"></i> <?= $m['capacidad'] ?> personas
                </div>
                <?php if ($m['estado'] === 'ocupada' && $m['pedido_id']): ?>
                    <div class="mesa-info text-danger">
                        <i class="bi bi-clock"></i>
                        <?= $m['mesero_nombre'] ?>
                    </div>
                    <div class="mesa-info fw-500">S/ <?= number_format($m['total'], 2) ?></div>
                <?php endif; ?>
                <span class="estado-badge"><?= ucfirst($m['estado']) ?></span>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.APP_URL = "<?= APP_URL ?>";</script>
<script src="<?= APP_URL ?>/public/js/notificaciones.js"></script>
</body>
</html>