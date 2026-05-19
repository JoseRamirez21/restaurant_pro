<?php
/** @var array $ingrediente */
/** @var array $movimientos */
$unidades = ['kg'=>'kg','g'=>'g','l'=>'L','ml'=>'ml','unidad'=>'Unid.','docena'=>'Doc.','caja'=>'Caja','bolsa'=>'Bolsa'];
?>
<style>
.form-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:560px; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control, .form-select { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus, .form-select:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; }
.stock-actual-box { background:#f4f1eb; border-radius:12px; padding:1rem 1.2rem; margin-bottom:1.2rem; }
.tipo-btn { border:2px solid #e8e5df; border-radius:10px; padding:.75rem 1rem; cursor:pointer; transition:all .15s; text-align:center; }
.tipo-btn.entrada.selected { border-color:#28a745; background:#e8f5e9; }
.tipo-btn.salida.selected  { border-color:#dc3545; background:#ffebee; }
.tipo-btn:hover { border-color:#8e44ad; }
.tipo-btn input { display:none; }
.mov-row { display:flex; justify-content:space-between; font-size:13px; padding:.5rem 0; border-bottom:1px solid #f7f5f0; }
.mov-row:last-child { border-bottom:none; }
.tag-entrada { color:#2e7d32; font-weight:500; }
.tag-salida  { color:#c62828; font-weight:500; }
.tag-ajuste  { color:#1565c0; font-weight:500; }
</style>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="form-card">
            <div class="d-flex align-items-center gap-3 mb-3">
                <a href="<?= APP_URL ?>/inventario" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h5 class="mb-0 fw-600">Ajustar stock</h5>
            </div>

            <div class="stock-actual-box">
                <div style="font-size:12px;color:#888;margin-bottom:2px;">Stock actual de</div>
                <div style="font-size:16px;font-weight:600;"><?= htmlspecialchars($ingrediente['nombre']) ?></div>
                <div style="font-size:2rem;font-weight:700;color:#2c1f3e;margin-top:4px;">
                    <?= number_format($ingrediente['stock_actual'],2) ?>
                    <span style="font-size:14px;color:#888;"><?= $unidades[$ingrediente['unidad']] ?? $ingrediente['unidad'] ?></span>
                </div>
            </div>

            <form method="POST" action="<?= APP_URL ?>/inventario/ajustar/<?= $ingrediente['id'] ?>">
                <input type="hidden" name="tipo" id="tipoInput" value="entrada">

                <label class="form-label mb-2">Tipo de movimiento</label>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="tipo-btn entrada selected d-block" onclick="selTipo('entrada',this)">
                            <input type="radio" name="_tipo" value="entrada" checked>
                            <i class="bi bi-arrow-up-circle-fill text-success" style="font-size:1.5rem;display:block;margin-bottom:4px;"></i>
                            <div style="font-size:13px;font-weight:500;">Entrada</div>
                            <div style="font-size:11px;color:#888;">Compra / recepción</div>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="tipo-btn salida d-block" onclick="selTipo('salida',this)">
                            <input type="radio" name="_tipo" value="salida">
                            <i class="bi bi-arrow-down-circle-fill text-danger" style="font-size:1.5rem;display:block;margin-bottom:4px;"></i>
                            <div style="font-size:13px;font-weight:500;">Salida</div>
                            <div style="font-size:11px;color:#888;">Uso / merma</div>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad *</label>
                    <div class="input-group">
                        <input type="number" name="cantidad" class="form-control"
                               step="0.01" min="0.01" placeholder="0.00" required>
                        <span class="input-group-text" style="font-size:13px;">
                            <?= $unidades[$ingrediente['unidad']] ?? $ingrediente['unidad'] ?>
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Motivo</label>
                    <input type="text" name="motivo" class="form-control"
                           placeholder="Ej: Compra semana, merma, inventario...">
                </div>

                <button type="submit" class="btn-guardar btn w-100">
                    <i class="bi bi-check-circle me-2"></i>Registrar movimiento
                </button>
            </form>
        </div>
    </div>

    <!-- Historial de movimientos -->
    <div class="col-lg-6">
        <div class="form-card">
            <h6 class="fw-600 mb-3"><i class="bi bi-clock-history me-2"></i>Últimos movimientos</h6>
            <?php if (empty($movimientos)): ?>
            <div style="text-align:center;color:#ccc;padding:2rem;">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <p style="font-size:13px;margin-top:.5rem;">Sin movimientos aún</p>
            </div>
            <?php else: ?>
            <?php foreach ($movimientos as $m): ?>
            <div class="mov-row">
                <div>
                    <span class="tag-<?= $m['tipo'] ?>">
                        <?= $m['tipo'] === 'entrada' ? '↑' : ($m['tipo'] === 'salida' ? '↓' : '~') ?>
                        <?= number_format($m['cantidad'],2) ?>
                    </span>
                    <span style="color:#888;font-size:12px;margin-left:6px;">
                        <?= htmlspecialchars($m['motivo'] ?? '—') ?>
                    </span>
                    <div style="font-size:11px;color:#aaa;">
                        <?= $m['usuario_nombre'] ?? 'Sistema' ?> &nbsp;·&nbsp;
                        <?= date('d/m H:i', strtotime($m['creado_en'])) ?>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:12px;color:#aaa;">
                        <?= number_format($m['stock_anterior'],2) ?> → <strong><?= number_format($m['stock_nuevo'],2) ?></strong>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function selTipo(tipo, card) {
    document.querySelectorAll('.tipo-btn').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('tipoInput').value = tipo;
}
</script>