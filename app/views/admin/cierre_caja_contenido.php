<?php
/** @var array      $resumen    */
/** @var array|bool $cierre_hoy */
/** @var array      $historial  */
?>
<style>
.cierre-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; }
.metodo-row { display:flex; justify-content:space-between; align-items:center; padding:.6rem 0; border-bottom:1px solid #f7f5f0; font-size:14px; }
.metodo-row:last-child { border-bottom:none; }
.metodo-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.total-box { background:#f4f1eb; border-radius:12px; padding:1rem 1.2rem; }
.stat-mini { background:#fff; border:1px solid #e8e5df; border-radius:12px; padding:.9rem; text-align:center; }
.hist-row { display:flex; justify-content:space-between; align-items:center; padding:.6rem 0; border-bottom:1px solid #f7f5f0; font-size:13px; }
.hist-row:last-child { border-bottom:none; }
</style>

<!-- Stats rápidas -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.6rem;font-weight:700;color:#2e7d32;">
                S/ <?= number_format($resumen['total_ventas'],2) ?>
            </div>
            <div style="font-size:12px;color:#888;">Ventas del día</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.6rem;font-weight:700;color:#1565c0;">
                <?= $resumen['total_pedidos'] ?>
            </div>
            <div style="font-size:12px;color:#888;">Pedidos cerrados</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.6rem;font-weight:700;color:#e65100;">
                S/ <?= number_format($resumen['metodos']['efectivo'],2) ?>
            </div>
            <div style="font-size:12px;color:#888;">En efectivo</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.6rem;font-weight:700;color:#6a1b9a;">
                S/ <?= number_format($resumen['total_propinas'],2) ?>
            </div>
            <div style="font-size:12px;color:#888;">Propinas</div>
        </div>
    </div>
</div>

<div class="row g-3">

    <!-- Resumen por método -->
    <div class="col-lg-5">
        <div class="cierre-card">
            <h6 class="fw-600 mb-3">
                <i class="bi bi-credit-card me-2"></i>Desglose por método
            </h6>
            <?php
            $metodosInfo = [
                'efectivo'      => ['icon'=>'bi-cash',          'bg'=>'#e8f5e9','color'=>'#2e7d32', 'label'=>'Efectivo'],
                'tarjeta'       => ['icon'=>'bi-credit-card',   'bg'=>'#e3f2fd','color'=>'#1565c0', 'label'=>'Tarjeta'],
                'yape'          => ['icon'=>'bi-phone',         'bg'=>'#f3e5f5','color'=>'#6a1b9a', 'label'=>'Yape'],
                'plin'          => ['icon'=>'bi-phone-vibrate', 'bg'=>'#fff3e0','color'=>'#e65100', 'label'=>'Plin'],
                'transferencia' => ['icon'=>'bi-bank',          'bg'=>'#fce4ec','color'=>'#c62828', 'label'=>'Transferencia'],
            ];
            foreach ($metodosInfo as $key => $info):
            ?>
            <div class="metodo-row">
                <div class="d-flex align-items-center gap-2">
                    <div class="metodo-icon" style="background:<?= $info['bg'] ?>;color:<?= $info['color'] ?>;">
                        <i class="bi <?= $info['icon'] ?>"></i>
                    </div>
                    <span><?= $info['label'] ?></span>
                </div>
                <strong>S/ <?= number_format($resumen['metodos'][$key],2) ?></strong>
            </div>
            <?php endforeach; ?>

            <div class="total-box mt-3">
                <div style="display:flex;justify-content:space-between;font-size:1.1rem;font-weight:700;">
                    <span>TOTAL DEL DÍA</span>
                    <span style="color:#2e7d32;">S/ <?= number_format($resumen['total_ventas'],2) ?></span>
                </div>
            </div>

            <?php if (!$cierre_hoy): ?>
            <form method="POST" action="<?= APP_URL ?>/cierre/cerrar" class="mt-3">
                <div class="mb-3">
                    <label style="font-size:13px;font-weight:500;" class="form-label">Observaciones del cierre</label>
                    <textarea name="observaciones" class="form-control" rows="2"
                              style="font-size:13px;border-radius:8px;"
                              placeholder="Notas adicionales del cajero..."></textarea>
                </div>
                <button type="submit" class="btn w-100"
                        style="background:#2e7d32;color:#fff;border-radius:10px;font-weight:500;"
                        onclick="return confirm('¿Confirmar cierre de caja del día?')">
                    <i class="bi bi-lock-fill me-2"></i>Cerrar caja del día
                </button>
            </form>
            <?php else: ?>
            <div class="alert alert-success mt-3 py-2" style="font-size:13px;border-radius:10px;">
                <i class="bi bi-check-circle me-2"></i>
                Caja cerrada hoy a las <?= date('H:i', strtotime($cierre_hoy['creado_en'])) ?>
                <?php if ($cierre_hoy['observaciones']): ?>
                <div style="margin-top:4px;opacity:.7;"><?= htmlspecialchars($cierre_hoy['observaciones']) ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top productos + historial -->
    <div class="col-lg-7">

        <!-- Top productos hoy -->
        <?php if (!empty($resumen['top_productos'])): ?>
        <div class="cierre-card mb-3">
            <h6 class="fw-600 mb-3"><i class="bi bi-trophy me-2"></i>Top platos del día</h6>
            <?php foreach ($resumen['top_productos'] as $i => $p): ?>
            <div class="metodo-row">
                <div class="d-flex align-items-center gap-2">
                    <span style="color:#aaa;font-size:13px;min-width:20px;"><?= $i+1 ?></span>
                    <span><?= htmlspecialchars($p['nombre']) ?></span>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:600;"><?= $p['cant'] ?> vendidos</div>
                    <div style="font-size:11px;color:#aaa;">S/ <?= number_format($p['monto'],2) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Historial de cierres -->
        <div class="cierre-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-600 mb-0"><i class="bi bi-clock-history me-2"></i>Historial de cierres</h6>
            <a href="<?= APP_URL ?>/export/cierres"
               class="btn btn-sm" style="background:#2e7d32;color:#fff;border-radius:8px;font-size:12px;">
                <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
            </a>
        </div>
            <?php if (empty($historial)): ?>
            <div style="text-align:center;color:#ccc;padding:1.5rem;">Sin cierres registrados</div>
            <?php else: ?>
            <?php foreach (array_slice($historial,0,10) as $h): ?>
            <div class="hist-row">
                <div>
                    <div style="font-weight:500;"><?= date('d/m/Y', strtotime($h['fecha'])) ?></div>
                    <div style="font-size:11px;color:#aaa;"><?= htmlspecialchars($h['cajero'] ?? '—') ?></div>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:600;color:#2e7d32;">S/ <?= number_format($h['total_ventas'],2) ?></div>
                    <div style="font-size:11px;color:#aaa;"><?= $h['total_pedidos'] ?> pedidos</div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>