
<?php
// Solo para autocompletado del editor — no afecta el sistema
/** @var array $mesas_pendientes */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caja — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f1eb; }
        .topbar { background:#2c1f3e; color:#fff; padding:.9rem 1.2rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .brand { font-size:15px; font-weight:500; }
        .stat-card { background:#fff; border:1px solid #e0ddd6; border-radius:12px; padding:1.25rem 1.5rem; }
        .mesa-row { background:#fff; border:1px solid #e0ddd6; border-radius:12px; padding:1rem 1.2rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; transition:all .2s; }
        .mesa-row:hover { border-color:#8e44ad; }
        .mesa-num { font-size:1.1rem; font-weight:600; }
        .mesa-meta { font-size:12px; color:#888; }
        .total-grande { font-size:1.4rem; font-weight:700; color:#2c1f3e; }
        .btn-cobrar { background:#8e44ad; color:#fff; border:none; border-radius:8px; padding:.45rem 1.2rem; font-size:14px; font-weight:500; white-space:nowrap; }
        .btn-cobrar:hover { background:#7d3c98; color:#fff; }
        .sin-pendientes { text-align:center; padding:3rem; color:#aaa; }
        .badge-estado { font-size:11px; padding:3px 9px; border-radius:20px; }
        .tiempo-badge { font-size:11px; padding:2px 8px; border-radius:20px; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="brand"><i class="bi bi-cash-register me-2"></i>Caja</div>
    <div class="d-flex align-items-center gap-2">
        <span style="font-size:12px; opacity:.7;"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
        <a href="<?= APP_URL ?>/logout" class="btn btn-sm btn-outline-light">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
</div>

<div class="container-fluid p-3">

    <!-- Stats del día -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div style="font-size:1.6rem; font-weight:600; color:#2e7d32;">
                    S/ <?= number_format($ventas_hoy['total_ventas'] ?? 0, 2) ?>
                </div>
                <div style="font-size:13px; color:#888;">Ventas hoy</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div style="font-size:1.6rem; font-weight:600; color:#1565c0;">
                    <?= $ventas_hoy['total_pedidos'] ?? 0 ?>
                </div>
                <div style="font-size:13px; color:#888;">Cuentas cobradas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div style="font-size:1.6rem; font-weight:600; color:#e65100;">
                    <?= count($mesas_pendientes) ?>
                </div>
                <div style="font-size:13px; color:#888;">Pendientes de cobro</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div style="font-size:1.6rem; font-weight:600; color:#6a1b9a;">
                    S/ <?= number_format($ventas_hoy['total_propinas'] ?? 0, 2) ?>
                </div>
                <div style="font-size:13px; color:#888;">Propinas hoy</div>
            </div>
        </div>
    </div>

    <!-- Lista de mesas pendientes -->
    <div class="stat-card">
        <h6 class="fw-600 mb-3">
            <i class="bi bi-receipt me-2"></i>Mesas pendientes de cobro
        </h6>

        <?php if (empty($mesas_pendientes)): ?>
        <div class="sin-pendientes">
            <i class="bi bi-check-circle" style="font-size:2.5rem; color:#28a745;"></i>
            <p class="mt-2 mb-0">Todo cobrado — no hay cuentas pendientes</p>
        </div>
        <?php else: ?>
        <div class="d-flex flex-column gap-2">
            <?php foreach ($mesas_pendientes as $p):
                $min = (int)($p['minutos_abierto'] ?? 0);
                $color_tiempo = $min > 90 ? 'danger' : ($min > 45 ? 'warning' : 'secondary');
            ?>
            <div class="mesa-row">
                <div>
                    <div class="mesa-num">
                        <i class="bi bi-table me-1"></i>Mesa <?= $p['mesa_numero'] ?>
                        <span class="badge bg-<?= $color_tiempo ?> tiempo-badge ms-1">
                            <?= $min ?> min
                        </span>
                    </div>
                    <div class="mesa-meta">
                        <i class="bi bi-people me-1"></i><?= $p['personas'] ?> personas &nbsp;·&nbsp;
                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($p['mesero_nombre']) ?> &nbsp;·&nbsp;
                        <?= ucfirst($p['zona']) ?>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="total-grande">S/ <?= number_format($p['total'], 2) ?></div>
                    <a href="<?= APP_URL ?>/caja/cuenta/<?= $p['id'] ?>" class="btn-cobrar btn">
                        <i class="bi bi-cash-coin me-1"></i>Cobrar
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>