<?php
/** @var array $pedido */
/** @var array $detalle */
?>
<style>
.ticket-box { background:#fff; border:1px solid #e8e5df; border-radius:16px; padding:1.5rem; max-width:520px; margin:0 auto; }
.ticket-header { text-align:center; border-bottom:1px dashed #ddd; padding-bottom:1rem; margin-bottom:1rem; }
.item-row { display:flex; justify-content:space-between; font-size:14px; margin-bottom:.4rem; }
.divider-dash { border:none; border-top:1px dashed #ddd; margin:.75rem 0; }
.total-row { display:flex; justify-content:space-between; font-size:13px; color:#666; margin-bottom:.3rem; }
.total-final { display:flex; justify-content:space-between; font-size:1.2rem; font-weight:700; color:#2c1f3e; margin-top:.4rem; }
.metodo-btn { border:2px solid #e0ddd6; border-radius:10px; padding:.75rem .5rem; text-align:center; cursor:pointer; transition:all .2s; background:#fff; }
.metodo-btn:hover, .metodo-btn.selected { border-color:#8e44ad; background:#f9f5fd; }
.metodo-btn i { font-size:1.4rem; display:block; margin-bottom:.3rem; color:#8e44ad; }
.metodo-btn span { font-size:12px; font-weight:500; }
.vuelto-box { background:#e8f5e9; border-radius:10px; padding:1rem; text-align:center; display:none; }
.btn-cobrar-final { background:#28a745; color:#fff; border:none; border-radius:10px; padding:.8rem; font-size:1rem; font-weight:500; width:100%; }
.btn-cobrar-final:hover { background:#218838; }
</style>

<div class="ticket-box">
    <div class="ticket-header">
        <h5 class="fw-600 mb-1">RestaurantePro</h5>
        <div style="font-size:13px; color:#888;">
            Mesa <?= $pedido['mesa_numero'] ?> &nbsp;·&nbsp;
            <?= $pedido['personas'] ?> personas &nbsp;·&nbsp;
            Mozo: <?= htmlspecialchars($pedido['mesero_nombre']) ?>
        </div>
        <div style="font-size:12px; color:#aaa;"><?= date('d/m/Y H:i') ?></div>
    </div>

    <?php foreach ($detalle as $item): ?>
    <div class="item-row">
        <div class="d-flex gap-2">
            <span style="color:#888; min-width:30px;"><?= $item['cantidad'] ?>x</span>
            <span><?= htmlspecialchars($item['producto_nombre']) ?></span>
        </div>
        <span>S/ <?= number_format($item['subtotal'], 2) ?></span>
    </div>
    <?php endforeach; ?>

    <hr class="divider-dash">
    <div class="total-row"><span>Subtotal</span><span>S/ <?= number_format($pedido['subtotal'], 2) ?></span></div>
    <div class="total-row"><span>IGV (18%)</span><span>S/ <?= number_format($pedido['igv'], 2) ?></span></div>
    <div class="total-row"><span>Servicio (10%)</span><span>S/ <?= number_format($pedido['servicio'], 2) ?></span></div>
    <div class="total-final"><span>TOTAL</span><span>S/ <?= number_format($pedido['total'], 2) ?></span></div>
    <hr class="divider-dash">

    <form method="POST" action="<?= APP_URL ?>/caja/cobrar/<?= $pedido['id'] ?>">
        <input type="hidden" name="metodo_pago" id="metodo_pago" value="efectivo">

        <label class="form-label fw-500 mb-2">Método de pago</label>
        <div class="row g-2 mb-3">
            <?php foreach ([
                ['efectivo','bi-cash','Efectivo'],
                ['tarjeta','bi-credit-card','Tarjeta'],
                ['yape','bi-phone','Yape'],
                ['plin','bi-phone-vibrate','Plin'],
                ['transferencia','bi-bank','Transfer.'],
            ] as $m): ?>
            <div class="col">
                <div class="metodo-btn <?= $m[0]==='efectivo'?'selected':'' ?>" onclick="selMetodo('<?= $m[0] ?>',this)">
                    <i class="bi <?= $m[1] ?>"></i><span><?= $m[2] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mb-3" id="montoBox">
            <label class="form-label fw-500">Monto recibido (S/)</label>
            <input type="number" name="monto_pagado" id="monto_pagado" class="form-control form-control-lg"
                   step="0.10" min="<?= $pedido['total'] ?>" value="<?= ceil($pedido['total']) ?>"
                   oninput="calcVuelto()" required>
        </div>

        <div class="vuelto-box mb-3" id="vueltoBox">
            <div style="font-size:13px;color:#555;">Vuelto</div>
            <div style="font-size:1.5rem;font-weight:700;color:#2e7d32;">S/ <span id="vueltoNum">0.00</span></div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-500">Propina (S/) <span style="color:#aaa;font-weight:400;">opcional</span></label>
            <input type="number" name="propina" class="form-control" step="0.50" min="0" value="0">
        </div>

        <button type="submit" class="btn-cobrar-final btn">
            <i class="bi bi-check-circle me-2"></i>Confirmar cobro — S/ <?= number_format($pedido['total'], 2) ?>
        </button>
    </form>
</div>

<script>
const total = <?= $pedido['total'] ?>;
function selMetodo(m, el) {
    document.querySelectorAll('.metodo-btn').forEach(b=>b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('metodo_pago').value = m;
    document.getElementById('montoBox').style.display = m==='efectivo' ? '' : 'none';
    if (m !== 'efectivo') { document.getElementById('vueltoBox').style.display='none'; document.querySelector('[name=monto_pagado]').value=total.toFixed(2); }
}
function calcVuelto() {
    const pagado = parseFloat(document.getElementById('monto_pagado').value)||0;
    const vuelto = pagado - total;
    document.getElementById('vueltoBox').style.display = pagado>=total ? '' : 'none';
    document.getElementById('vueltoNum').textContent = vuelto.toFixed(2);
}
calcVuelto();
</script>