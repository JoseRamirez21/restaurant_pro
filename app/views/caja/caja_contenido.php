<?php
/** @var array $mesas_pendientes */
/** @var array $ventas_hoy */
?>
<style>
.stat-mini { background:#fff; border:1px solid #e8e5df; border-radius:12px; padding:1rem 1.2rem; text-align:center; }
.mesa-row { background:#fff; border:1px solid #e8e5df; border-radius:12px; padding:1rem 1.2rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; transition:all .2s; }
.mesa-row:hover { border-color:#8e44ad; }
.btn-cobrar { background:#8e44ad; color:#fff; border:none; border-radius:8px; padding:.45rem 1.2rem; font-size:14px; font-weight:500; text-decoration:none; display:inline-block; }
.btn-cobrar:hover { background:#7d3c98; color:#fff; }
</style>

<!-- Stats rápidas -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.5rem;font-weight:700;color:#2e7d32;">S/ <?= number_format($ventas_hoy['total_ventas'] ?? 0, 2) ?></div>
            <div style="font-size:12px;color:#888;">Ventas hoy</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.5rem;font-weight:700;color:#1565c0;"><?= $ventas_hoy['total_pedidos'] ?? 0 ?></div>
            <div style="font-size:12px;color:#888;">Cobrados</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.5rem;font-weight:700;color:#e65100;"><?= count($mesas_pendientes) ?></div>
            <div style="font-size:12px;color:#888;">Pendientes</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.5rem;font-weight:700;color:#6a1b9a;">S/ <?= number_format($ventas_hoy['total_propinas'] ?? 0, 2) ?></div>
            <div style="font-size:12px;color:#888;">Propinas</div>
        </div>
    </div>
</div>

<!-- Lista pendientes -->
<div style="background:#fff;border:1px solid #e8e5df;border-radius:14px;overflow:hidden;">
    <div style="padding:.9rem 1.2rem;border-bottom:1px solid #e8e5df;font-size:14px;font-weight:600;">
        <i class="bi bi-receipt me-2"></i>Mesas pendientes de cobro
    </div>
    <?php if (empty($mesas_pendientes)): ?>
    <div style="text-align:center;padding:3rem;color:#aaa;">
        <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
        <p class="mt-2 mb-0">Todo cobrado — no hay cuentas pendientes</p>
    </div>
    <?php else: ?>
    <div class="d-flex flex-column gap-2 p-3">
        <?php foreach ($mesas_pendientes as $p):
            $min = (int)($p['minutos_abierto'] ?? 0);
            $color = $min > 90 ? 'danger' : ($min > 45 ? 'warning' : 'secondary');
        ?>
        <div class="mesa-row">
            <div>
                <div style="font-size:15px;font-weight:600;">
                    <i class="bi bi-table me-1"></i>Mesa <?= $p['mesa_numero'] ?>
                    <span class="badge bg-<?= $color ?> ms-1" style="font-size:11px;"><?= $min ?> min</span>
                </div>
                <div style="font-size:12px;color:#888;">
                    <i class="bi bi-people me-1"></i><?= $p['personas'] ?> pers. &nbsp;·&nbsp;
                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($p['mesero_nombre']) ?>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div style="font-size:1.3rem;font-weight:700;">S/ <?= number_format($p['total'], 2) ?></div>
                <a href="<?= APP_URL ?>/caja/cuenta/<?= $p['id'] ?>" class="btn-cobrar">
                    <i class="bi bi-cash-coin me-1"></i>Cobrar
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
