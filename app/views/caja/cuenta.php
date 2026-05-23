<?php
/** @var array $pedido */
/** @var array $detalle */
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobrar Mesa <?= $pedido['mesa_numero'] ?> — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f1eb; }
        .topbar { background:#2c1f3e; color:#fff; padding:.9rem 1.2rem; display:flex; align-items:center; justify-content:space-between; }
        .wrap { max-width:460px; margin:1.5rem auto; padding:0 1rem; }
        .ticket { background:#fff; border-radius:16px; border:1px solid #e8e5df; padding:1.6rem; margin-bottom:1rem; }
        .rest-nombre { font-size:1.1rem; font-weight:700; color:#2c1f3e; text-align:center; }
        .rest-sub { font-size:12px; color:#aaa; text-align:center; margin-top:2px; }
        .mesa-chips { display:flex; gap:.5rem; justify-content:center; flex-wrap:wrap; margin:1rem 0; }
        .chip { background:#f4f1eb; border-radius:20px; padding:3px 10px; font-size:12px; color:#666; }
        .divider { border:none; border-top:1px dashed #ddd; margin:.9rem 0; }
        .item-row { display:flex; align-items:baseline; margin-bottom:.6rem; gap:.5rem; }
        .item-cant { font-size:13px; color:#aaa; min-width:24px; }
        .item-nombre { font-size:14px; color:#333; flex:1; }
        .item-precio { font-size:14px; font-weight:500; color:#2c1f3e; white-space:nowrap; }
        .total-box { background:linear-gradient(135deg,#8e44ad,#6c3483); border-radius:12px; padding:1.1rem 1.3rem; display:flex; justify-content:space-between; align-items:center; }
        .total-label { font-size:14px; font-weight:600; color:rgba(255,255,255,.85); }
        .total-monto { font-size:1.8rem; font-weight:800; color:#fff; }
        .metodo-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.6rem; margin-bottom:1.2rem; }
        .metodo-btn { border:2px solid #e8e5df; border-radius:12px; padding:.8rem .4rem; text-align:center; cursor:pointer; transition:all .2s; background:#fff; }
        .metodo-btn:hover { border-color:#8e44ad; }
        .metodo-btn.selected { border-color:#8e44ad; background:#f9f5fd; }
        .metodo-btn input { display:none; }
        .metodo-btn i { font-size:1.4rem; display:block; margin-bottom:.3rem; color:#8e44ad; }
        .metodo-btn span { font-size:12px; font-weight:500; color:#444; }
        .btn-cobrar { background:#8e44ad; color:#fff; border:none; border-radius:12px; padding:.9rem; font-size:1rem; font-weight:600; width:100%; }
        .btn-cobrar:hover { background:#7d3c98; }
    </style>
</head>
<body>

<div class="topbar">
    <a href="<?= APP_URL ?>/caja" class="btn btn-sm btn-outline-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span style="font-weight:500;">Cobrar — Mesa <?= $pedido['mesa_numero'] ?></span>
    <div></div>
</div>

<div class="wrap">

    <!-- BOLETA LIMPIA -->
    <div class="ticket">
        <div class="rest-nombre">RestaurantePro</div>
        <div class="rest-sub"><?= date('d/m/Y H:i') ?></div>

        <div class="mesa-chips">
            <span class="chip"><i class="bi bi-table me-1"></i>Mesa <?= $pedido['mesa_numero'] ?></span>
            <span class="chip"><i class="bi bi-people me-1"></i><?= $pedido['personas'] ?> personas</span>
            <span class="chip"><i class="bi bi-person me-1"></i><?= htmlspecialchars($pedido['mesero_nombre']) ?></span>
        </div>

        <hr class="divider">

        <!-- Solo platos y precios -->
        <?php foreach ($detalle as $item): ?>
        <div class="item-row">
            <span class="item-cant"><?= $item['cantidad'] ?>×</span>
            <span class="item-nombre"><?= htmlspecialchars($item['producto_nombre']) ?></span>
            <span class="item-precio">S/ <?= number_format($item['subtotal'], 2) ?></span>
        </div>
        <?php endforeach; ?>

        <hr class="divider">

        <!-- Solo el total -->
        <div class="total-box">
            <span class="total-label">Total a pagar</span>
            <span class="total-monto">S/ <?= number_format($pedido['subtotal'], 2) ?></span>
        </div>
    </div>

    <!-- MÉTODO DE PAGO -->
    <div class="ticket">
        <p style="font-size:13px;font-weight:500;color:#555;margin-bottom:.75rem;">
            ¿Cómo va a pagar?
        </p>

        <form method="POST" action="<?= APP_URL ?>/caja/cobrar/<?= $pedido['id'] ?>">
            <input type="hidden" name="metodo_pago"  id="metodo_pago"         value="efectivo">
            <input type="hidden" name="monto_pagado" id="monto_pagado_hidden"  value="<?= $pedido['subtotal'] ?>">
            <input type="hidden" name="propina"      value="0">

            <div class="metodo-grid">
                <?php foreach ([
                    ['efectivo',      'bi-cash',           'Efectivo'],
                    ['tarjeta',       'bi-credit-card',    'Tarjeta'],
                    ['yape',          'bi-phone',          'Yape'],
                    ['plin',          'bi-phone-vibrate',  'Plin'],
                    ['transferencia', 'bi-bank',           'Transfer.'],
                ] as $m): ?>
                <label class="metodo-btn <?= $m[0]==='efectivo'?'selected':'' ?>"
                       onclick="selMetodo('<?= $m[0] ?>',this)">
                    <input type="radio" name="_metodo" value="<?= $m[0] ?>"
                           <?= $m[0]==='efectivo'?'checked':'' ?>>
                    <i class="bi <?= $m[1] ?>"></i>
                    <span><?= $m[2] ?></span>
                </label>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-cobrar btn">
                <i class="bi bi-check-circle me-2"></i>
                Confirmar cobro — S/ <?= number_format($pedido['subtotal'], 2) ?>
            </button>
        </form>
    </div>

</div>

<script>
function selMetodo(metodo, el) {
    document.querySelectorAll('.metodo-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('metodo_pago').value = metodo;
}
</script>
</body>
</html>