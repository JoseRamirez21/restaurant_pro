<?php
/** @var array $cliente  */
/** @var array $historial */
?>
<style>
.detalle-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; }
.vis-row { display:flex; justify-content:space-between; padding:.6rem 0; border-bottom:1px solid #f7f5f0; font-size:13px; }
.vis-row:last-child { border-bottom:none; }
.stat-pill { background:#f4f1eb; border-radius:10px; padding:.6rem 1rem; text-align:center; }
</style>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="detalle-card">
            <div style="text-align:center;margin-bottom:1rem;">
                <div style="width:64px;height:64px;border-radius:50%;background:#8e44ad;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:600;color:#fff;margin:0 auto .75rem;">
                    <?= strtoupper(substr($cliente['nombre'],0,1)) ?>
                </div>
                <div style="font-size:1.1rem;font-weight:600;">
                    <?= htmlspecialchars($cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '')) ?>
                </div>
                <?php if ($cliente['visitas'] >= 5): ?>
                <span style="font-size:11px;padding:2px 10px;border-radius:20px;background:#e8f5e9;color:#2e7d32;font-weight:600;">
                    ⭐ Cliente VIP
                </span>
                <?php endif; ?>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-4">
                    <div class="stat-pill">
                        <div style="font-size:1.3rem;font-weight:700;"><?= $cliente['visitas'] ?></div>
                        <div style="font-size:11px;color:#888;">Visitas</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-pill">
                        <div style="font-size:1rem;font-weight:700;">S/ <?= number_format($cliente['total_gastado'],0) ?></div>
                        <div style="font-size:11px;color:#888;">Gastado</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-pill">
                        <div style="font-size:1.3rem;font-weight:700;color:#e65100;"><?= $cliente['puntos'] ?></div>
                        <div style="font-size:11px;color:#888;">Puntos</div>
                    </div>
                </div>
            </div>

            <?php if ($cliente['telefono']): ?>
            <div style="font-size:13px;color:#888;margin-bottom:.4rem;">
                <i class="bi bi-telephone me-2"></i><?= htmlspecialchars($cliente['telefono']) ?>
            </div>
            <?php endif; ?>
            <?php if ($cliente['email']): ?>
            <div style="font-size:13px;color:#888;margin-bottom:.4rem;">
                <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($cliente['email']) ?>
            </div>
            <?php endif; ?>
            <?php if ($cliente['fecha_nac']): ?>
            <div style="font-size:13px;color:#888;margin-bottom:.4rem;">
                <i class="bi bi-cake me-2"></i><?= date('d/m/Y', strtotime($cliente['fecha_nac'])) ?>
            </div>
            <?php endif; ?>
            <?php if ($cliente['notas']): ?>
            <div style="background:#f4f1eb;border-radius:10px;padding:.75rem;font-size:13px;color:#555;margin-top:.75rem;">
                <i class="bi bi-chat-left-text me-2"></i><?= htmlspecialchars($cliente['notas']) ?>
            </div>
            <?php endif; ?>

            <div class="d-flex gap-2 mt-3">
                <a href="<?= APP_URL ?>/clientes/editar/<?= $cliente['id'] ?>"
                   class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
                <a href="<?= APP_URL ?>/clientes"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="detalle-card">
            <h6 class="fw-600 mb-3"><i class="bi bi-clock-history me-2"></i>Historial de visitas</h6>
            <?php if (empty($historial)): ?>
            <div style="text-align:center;color:#ccc;padding:2rem;">
                <i class="bi bi-calendar-x" style="font-size:2rem;"></i>
                <p style="font-size:13px;margin-top:.5rem;">Sin visitas registradas aún</p>
            </div>
            <?php else: ?>
            <?php foreach ($historial as $v): ?>
            <div class="vis-row">
                <div>
                    <div style="font-weight:500;"><?= date('d/m/Y', strtotime($v['fecha'])) ?></div>
                    <?php if ($v['mesa_numero']): ?>
                    <div style="font-size:11px;color:#aaa;">
                        <i class="bi bi-table me-1"></i>Mesa <?= $v['mesa_numero'] ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($v['notas']): ?>
                    <div style="font-size:11px;color:#aaa;"><?= htmlspecialchars($v['notas']) ?></div>
                    <?php endif; ?>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:600;color:#2e7d32;">S/ <?= number_format($v['monto'],2) ?></div>
                    <div style="font-size:11px;color:#aaa;">
                        +<?= (int)floor($v['monto']/10) ?> puntos
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>