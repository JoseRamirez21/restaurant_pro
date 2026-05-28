<?php
/** @var array $pedido */
/** @var array $detalle */
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f1eb; }
        .topbar { background:#2c1f3e; color:#fff; padding:.9rem 1.2rem; display:flex; align-items:center; justify-content:space-between; }
        .wrap { max-width:400px; margin:1.5rem auto; padding:0 1rem; }
        .ticket { background:#fff; border-radius:16px; border:1px solid #e8e5df; overflow:hidden; margin-bottom:1rem; }
        .ticket-header { background:#2c1f3e; color:#fff; padding:1.5rem; text-align:center; }
        .check-icon { font-size:2.8rem; margin-bottom:.4rem; }
        .rest-nombre { font-size:1.1rem; font-weight:700; letter-spacing:.04em; }
        .rest-sub { font-size:11px; opacity:.45; margin-top:3px; }
        .ticket-body { padding:1.4rem; }
        .mesa-chips { display:flex; gap:.4rem; flex-wrap:wrap; margin-bottom:1rem; }
        .chip { background:#f4f1eb; border-radius:20px; padding:3px 10px; font-size:12px; color:#666; }
        .divider { border:none; border-top:1px dashed #ddd; margin:.9rem 0; }
        .item-row { display:flex; align-items:baseline; margin-bottom:.5rem; gap:.5rem; }
        .item-cant { font-size:13px; color:#aaa; min-width:22px; }
        .item-nombre { font-size:14px; color:#333; flex:1; }
        .item-precio { font-size:14px; font-weight:500; color:#2c1f3e; }
        .total-box { background:linear-gradient(135deg,#8e44ad,#6c3483); border-radius:12px; padding:1rem 1.2rem; display:flex; justify-content:space-between; align-items:center; margin-top:1rem; }
        .total-label { font-size:14px; font-weight:600; color:rgba(255,255,255,.8); }
        .total-monto { font-size:1.6rem; font-weight:800; color:#fff; }
        .pago-fila { display:flex; justify-content:space-between; font-size:13px; margin-top:.7rem; color:#666; align-items:center; }
        .metodo-tag { background:#f4f1eb; border-radius:20px; padding:3px 12px; font-size:12px; font-weight:500; color:#444; text-transform:capitalize; }
        .gracias { text-align:center; padding:1.1rem; border-top:1px dashed #ddd; margin-top:.75rem; }
        .gracias p { font-size:13px; color:#aaa; margin:0 0 2px; }
        .ref { font-size:11px; color:#ccc; margin-top:6px; }
        .btn-acciones { display:flex; gap:.75rem; }
        .btn-volver { border:1.5px solid #e0ddd6; background:#fff; border-radius:10px; padding:.65rem; font-size:13px; font-weight:500; flex:1; color:#555; text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:5px; }
        .btn-imprimir { background:#2c1f3e; color:#fff; border:none; border-radius:10px; padding:.65rem; font-size:13px; font-weight:500; flex:1; cursor:pointer; }
        @media print {
            .topbar, .btn-acciones { display:none !important; }
            body { background:#fff; }
            .ticket { border:none; }
            .wrap { margin:0; max-width:100%; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <a href="<?= APP_URL ?>/caja" class="btn btn-sm btn-outline-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span style="font-weight:500;">Comprobante</span>
    <button onclick="window.print()" class="btn btn-sm btn-outline-light">
        <i class="bi bi-printer"></i>
    </button>
</div>

<div class="wrap">
    <div class="ticket">

        <!-- Header -->
        <div class="ticket-header">
            <div class="check-icon">✅</div>
            <div class="rest-nombre">RestaurantePro</div>
            <div class="rest-sub"><?= date('d/m/Y H:i') ?></div>
        </div>

        <div class="ticket-body">

            <!-- Info mesa -->
            <div class="mesa-chips">
                <span class="chip"><i class="bi bi-table me-1"></i>Mesa <?= $pedido['mesa_numero'] ?></span>
                <span class="chip"><i class="bi bi-people me-1"></i><?= $pedido['personas'] ?> personas</span>
                <span class="chip"><i class="bi bi-person me-1"></i><?= htmlspecialchars($pedido['mesero_nombre']) ?></span>
            </div>

            <hr class="divider">

            <!-- Solo platos -->
            <?php foreach ($detalle as $item): ?>
            <div class="item-row">
                <span class="item-cant"><?= $item['cantidad'] ?>×</span>
                <span class="item-nombre"><?= htmlspecialchars($item['producto_nombre']) ?></span>
                <span class="item-precio">S/ <?= number_format($item['subtotal'], 2) ?></span>
            </div>
            <?php endforeach; ?>

            <!-- Total -->
            <div class="total-box">
                <span class="total-label">TOTAL PAGADO</span>
                <span class="total-monto">S/ <?= number_format($pedido['subtotal'], 2) ?></span>
            </div>

            <!-- Método de pago -->
            <?php
            $iconos = ['efectivo'=>'💵','tarjeta'=>'💳','yape'=>'📱','plin'=>'📲','transferencia'=>'🏦'];
            $met    = $pedido['metodo_pago'] ?? 'efectivo';
            ?>
            <div class="pago-fila">
                <span>Método de pago</span>
                <span class="metodo-tag">
                    <?= ($iconos[$met] ?? '') ?> <?= ucfirst($met) ?>
                </span>
            </div>

            <!-- Gracias -->
            <div class="gracias">
                <p>¡Gracias por su visita! 🍽</p>
                <p>Esperamos verle pronto</p>
                <div class="ref">Ref: #<?= str_pad($pedido['id'], 6, '0', STR_PAD_LEFT) ?></div>
            </div>

        </div>
    </div>

    <!-- Botones -->
    <div class="btn-acciones">
        <a href="<?= APP_URL ?>/caja" class="btn-volver">
            <i class="bi bi-arrow-left"></i> Volver a caja
        </a>
        <button onclick="window.print()" class="btn-imprimir">
            <i class="bi bi-printer me-1"></i> Imprimir
        </button>
    </div>
</div>

<script>
function imprimirTermica() {
    const btn = document.getElementById('btnTermica');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Imprimiendo...';
    fetch('<?= APP_URL ?>/impresora/boleta/<?= $pedido['id'] ?>', { method: 'POST' })
    .then(r => r.json())
    .then(d => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-receipt me-1"></i> Térmica';
        if (d.ok) {
            alert('✅ Boleta enviada a la impresora');
        } else {
            alert('❌ Error: ' + (d.error || 'No se pudo imprimir'));
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-receipt me-1"></i> Térmica';
        alert('❌ No se pudo conectar con la impresora');
    });
}
</script>
</body>
</html>