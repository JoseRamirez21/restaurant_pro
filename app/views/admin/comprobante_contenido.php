<?php
/** @var array $pedido  */
/** @var array $detalle */
?>
<style>
.ticket {
    background:#fff; border-radius:16px; border:1px solid #e8e5df;
    overflow:hidden; max-width:400px; margin:0 auto;
}
.ticket-header { background:#2c1f3e; color:#fff; padding:1.5rem; text-align:center; }
.ticket-header .check { font-size:2.5rem; margin-bottom:.5rem; }
.rest-nombre { font-size:1.1rem; font-weight:700; letter-spacing:.04em; }
.rest-sub { font-size:11px; opacity:.5; margin-top:3px; }
.ticket-body { padding:1.4rem; }
.mesa-chips { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem; }
.chip { background:#f4f1eb; border-radius:20px; padding:3px 10px; font-size:12px; color:#666; }
.divider { border:none; border-top:1px dashed #ddd; margin:.9rem 0; }
.item-row { display:flex; align-items:baseline; margin-bottom:.5rem; gap:.5rem; }
.item-cant { font-size:13px; color:#aaa; min-width:22px; }
.item-nombre { font-size:14px; color:#333; flex:1; }
.item-precio { font-size:14px; font-weight:500; color:#2c1f3e; }
.total-box { background:linear-gradient(135deg,#8e44ad,#6c3483); border-radius:12px; padding:1rem 1.2rem; display:flex; justify-content:space-between; align-items:center; margin-top:1rem; }
.total-label { font-size:14px; font-weight:600; color:rgba(255,255,255,.8); }
.total-monto { font-size:1.6rem; font-weight:800; color:#fff; }
.pago-row { display:flex; justify-content:space-between; font-size:13px; margin-top:.6rem; color:#666; }
.metodo-tag { background:#f4f1eb; border-radius:20px; padding:2px 10px; font-size:12px; font-weight:500; color:#444; text-transform:capitalize; }
.gracias { text-align:center; padding:1rem; border-top:1px dashed #ddd; margin-top:.5rem; }
.gracias p { font-size:13px; color:#aaa; margin:0; }
@media print {
    .sidebar,.sidebar-overlay,.topbar,.btn-acciones { display:none!important; }
    .main { margin-left:0!important; }
    body { background:#fff; }
}
</style>

<div class="ticket">
    <div class="ticket-header">
        <div class="check">✅</div>
        <div class="rest-nombre">RestaurantePro</div>
        <div class="rest-sub"><?= date('d/m/Y H:i') ?></div>
    </div>
    <div class="ticket-body">
        <div class="mesa-chips">
            <span class="chip"><i class="bi bi-table me-1"></i>Mesa <?= $pedido['mesa_numero'] ?></span>
            <span class="chip"><i class="bi bi-people me-1"></i><?= $pedido['personas'] ?> personas</span>
            <span class="chip"><i class="bi bi-person me-1"></i><?= htmlspecialchars($pedido['mesero_nombre']) ?></span>
        </div>
        <hr class="divider">
        <?php foreach ($detalle as $item): ?>
        <div class="item-row">
            <span class="item-cant"><?= $item['cantidad'] ?>×</span>
            <span class="item-nombre"><?= htmlspecialchars($item['producto_nombre']) ?></span>
            <span class="item-precio">S/ <?= number_format($item['subtotal'], 2) ?></span>
        </div>
        <?php endforeach; ?>
        <div class="total-box">
            <span class="total-label">TOTAL PAGADO</span>
            <span class="total-monto">S/ <?= number_format($pedido['total'], 2) ?></span>
        </div>
        <?php
        $iconos = ['efectivo'=>'💵','tarjeta'=>'💳','yape'=>'📱','plin'=>'📲','transferencia'=>'🏦'];
        $met    = $pedido['metodo_pago'] ?? 'efectivo';
        $vuelto = (float)$pedido['monto_pagado'] - (float)$pedido['total'];
        ?>
        <div class="pago-row">
            <span>Método de pago</span>
            <span class="metodo-tag"><?= ($iconos[$met]??'') . ' ' . ucfirst($met) ?></span>
        </div>
        <?php if ($met === 'efectivo' && $vuelto > 0): ?>
        <div class="pago-row">
            <span style="color:#2e7d32;font-weight:500;">Vuelto entregado</span>
            <span style="color:#2e7d32;font-weight:600;">S/ <?= number_format($vuelto,2) ?></span>
        </div>
        <?php endif; ?>
        <div class="gracias">
            <p>¡Gracias por su visita!</p>
            <p>Esperamos verle pronto 🍽</p>
            <div style="font-size:11px;color:#ccc;margin-top:4px;">Ref: #<?= str_pad($pedido['id'],6,'0',STR_PAD_LEFT) ?></div>
        </div>
    </div>
</div>

<div class="btn-acciones d-flex gap-2 justify-content-center mt-3">
    <a href="<?= APP_URL ?>/caja" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver a caja
    </a>
    <button onclick="window.print()" class="btn btn-dark">
        <i class="bi bi-printer me-1"></i>Imprimir
    </button>
</div>