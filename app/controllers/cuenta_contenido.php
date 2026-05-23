<?php
/** @var array $pedido  */
/** @var array $detalle */
?>
<style>
.ticket { background:#fff; border-radius:16px; border:1px solid #e8e5df; overflow:hidden; max-width:480px; margin:0 auto; }
.ticket-header-cuenta { padding:1.2rem 1.4rem; border-bottom:1px solid #f0ede7; }
.divider { border:none; border-top:1px dashed #ddd; margin:.9rem 0; }
.item-row { display:flex; align-items:baseline; margin-bottom:.5rem; gap:.5rem; }
.item-cant { font-size:13px; color:#aaa; min-width:22px; }
.item-nombre { font-size:14px; color:#333; flex:1; }
.item-precio { font-size:14px; font-weight:500; color:#2c1f3e; }
.total-box { background:linear-gradient(135deg,#8e44ad,#6c3483); border-radius:12px; padding:1rem 1.2rem; display:flex; justify-content:space-between; align-items:center; margin-top:1rem; }
.total-label { font-size:14px; font-weight:600; color:rgba(255,255,255,.8); }
.total-monto { font-size:1.6rem; font-weight:800; color:#fff; }
.metodo-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.6rem; margin:1rem 0; }
.metodo-btn { border:2px solid #e8e5df; border-radius:12px; padding:.75rem .5rem; text-align:center; cursor:pointer; transition:all .2s; background:#fff; }
.metodo-btn:hover { border-color:#8e44ad; }
.metodo-btn.selected { border-color:#8e44ad; background:#f9f5fd; }
.metodo-btn input { display:none; }
.metodo-btn i { font-size:1.4rem; display:block; margin-bottom:.3rem; color:#8e44ad; }
.metodo-btn span { font-size:12px; font-weight:500; }
.vuelto-box { background:#e8f5e9; border-radius:12px; padding:.9rem; text-align:center; display:none; margin-bottom:1rem; }
.btn-cobrar-final { background:#28a745; color:#fff; border:none; border-radius:12px; padding:.85rem; font-size:14px; font-weight:600; width:100%; }
.btn-cobrar-final:hover { background:#218838; }
.chip { background:#f4f1eb; border-radius:20px; padding:3px 10px; font-size:12px; color:#666; margin-right:.4rem; }
</style>

<div class="ticket">
    <div class="ticket-header-cuenta">
        <h6 class="fw-600 mb-2">RestaurantePro</h6>
        <div>
            <span class="chip"><i class="bi bi-table me-1"></i>Mesa <?= $pedido['mesa_numero'] ?></span>
            <span class="chip"><i class="bi bi-people me-1"></i><?= $pedido['personas'] ?> personas</span>
            <span class="chip"><?= date('d/m/Y H:i') ?></span>
        </div>
    </div>

    <div style="padding:1.2rem 1.4rem;">
        <?php foreach ($detalle as $item): ?>
        <div class="item-row">
            <span class="item-cant"><?= $item['cantidad'] ?>×</span>
            <span class="item-nombre"><?= htmlspecialchars($item['producto_nombre']) ?></span>
            <span class="item-precio">S/ <?= number_format($item['subtotal'], 2) ?></span>
        </div>
        <?php endforeach; ?>

        <div class="total-box">
            <span class="total-label">TOTAL A PAGAR</span>
            <span class="total-monto">S/ <?= number_format($pedido['total'], 2) ?></span>
        </div>
    </div>
</div>

<div style="background:#fff;border:1px solid #e8e5df;border-radius:14px;padding:1.2rem;margin-top:1rem;max-width:480px;margin-left:auto;margin-right:auto;">
    <form method="POST" action="<?= APP_URL ?>/caja/cobrar/<?= $pedido['id'] ?>">
        <input type="hidden" name="metodo_pago"  id="metodo_pago" value="efectivo">
        <input type="hidden" name="monto_pagado" id="monto_pagado_hidden" value="<?= $pedido['total'] ?>">
        <input type="hidden" name="propina" value="0">

        <p style="font-size:13px;font-weight:500;color:#555;margin-bottom:.6rem;">¿Cómo va a pagar?</p>

        <div class="metodo-grid">
            <?php foreach ([
                ['efectivo','bi-cash','Efectivo'],
                ['tarjeta','bi-credit-card','Tarjeta'],
                ['yape','bi-phone','Yape'],
                ['plin','bi-phone-vibrate','Plin'],
                ['transferencia','bi-bank','Transfer.'],
            ] as $m): ?>
            <label class="metodo-btn <?= $m[0]==='efectivo'?'selected':'' ?>" onclick="selMetodo('<?= $m[0] ?>',this)">
                <input type="radio" name="_metodo" value="<?= $m[0] ?>" <?= $m[0]==='efectivo'?'checked':'' ?>>
                <i class="bi <?= $m[1] ?>"></i>
                <span><?= $m[2] ?></span>
            </label>
            <?php endforeach; ?>
        </div>

        <div id="efectivoBox" class="mb-3">
            <label style="font-size:13px;font-weight:500;color:#555;display:block;margin-bottom:.4rem;">
                Monto entregado (S/)
            </label>
            <input type="number" id="montoEfectivo" class="form-control"
                   style="border-radius:10px;font-size:1.1rem;"
                   step="0.50" min="<?= $pedido['total'] ?>"
                   value="<?= ceil($pedido['total']) ?>"
                   oninput="calcVuelto()">
        </div>

        <div class="vuelto-box" id="vueltoBox">
            <div style="font-size:12px;color:#555;">Vuelto a entregar</div>
            <div style="font-size:1.6rem;font-weight:700;color:#2e7d32;">
                S/ <span id="vueltoNum">0.00</span>
            </div>
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
    const ef = document.getElementById('efectivoBox');
    const vb = document.getElementById('vueltoBox');
    if (m === 'efectivo') { ef.style.display=''; calcVuelto(); }
    else { ef.style.display='none'; vb.style.display='none'; document.getElementById('monto_pagado_hidden').value=total.toFixed(2); }
}
function calcVuelto() {
    const pagado = parseFloat(document.getElementById('montoEfectivo').value)||0;
    const vuelto = pagado - total;
    document.getElementById('monto_pagado_hidden').value = pagado.toFixed(2);
    const box = document.getElementById('vueltoBox');
    if (pagado >= total && vuelto > 0) { box.style.display=''; document.getElementById('vueltoNum').textContent=vuelto.toFixed(2); }
    else { box.style.display='none'; }
}
calcVuelto();
</script>