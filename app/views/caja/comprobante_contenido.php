<?php
/** @var array $pedido */
/** @var array $detalle */
?>
<style>
.ticket {
    background:#fff;
    border:1px solid #e8e5df;
    border-radius:16px;
    padding:1.5rem;
    max-width:380px;
    margin:0 auto;
    font-family:'Courier New',monospace;
}

.divider-dash {
    border:none;
    border-top:1px dashed #ccc;
    margin:.75rem 0;
}

.item-row {
    display:flex;
    justify-content:space-between;
    font-size:13px;
    margin-bottom:.3rem;
}

.total-row {
    display:flex;
    justify-content:space-between;
    font-size:12px;
    color:#666;
    margin-bottom:.25rem;
}

.total-final {
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:linear-gradient(135deg,#8e44ad,#6c3483);
    color:#fff;
    padding:.9rem 1rem;
    border-radius:12px;
    font-size:1.1rem;
    font-weight:700;
    margin-top:.5rem;
}

.metodo-tag {
    background:#f4f1eb;
    border-radius:20px;
    padding:2px 10px;
    font-size:11px;
    font-weight:600;
    color:#444;
    text-transform:capitalize;
}

@media print {
    .main,
    .topbar,
    .sidebar,
    .sidebar-overlay,
    .btn-acciones {
        display:none!important;
    }

    body {
        background:#fff;
    }

    .ticket {
        border:none;
        box-shadow:none;
        margin:0;
        max-width:100%;
    }
}
</style>

<div class="ticket">

    <div style="text-align:center; margin-bottom:.5rem;">
        <i class="bi bi-check-circle-fill text-success"
           style="font-size:3rem;"></i>
    </div>

    <div style="text-align:center; font-weight:700; font-size:1.1rem; letter-spacing:.05em;">
        RestaurantePro
    </div>

    <div style="text-align:center; font-size:12px; color:#888; margin-bottom:.5rem;">
        Mesa <?= $pedido['mesa_numero'] ?>
        &nbsp;·&nbsp;
        <?= date('d/m/Y H:i') ?><br>

        Mozo:
        <?= htmlspecialchars($pedido['mesero_nombre']) ?>
    </div>

    <hr class="divider-dash">

    <?php foreach ($detalle as $item): ?>
    <div class="item-row">

        <span>
            <?= $item['cantidad'] ?>x
            <?= htmlspecialchars($item['producto_nombre']) ?>
        </span>

        <span>
            S/ <?= number_format($item['subtotal'], 2) ?>
        </span>

    </div>
    <?php endforeach; ?>

    <hr class="divider-dash">

    <!-- SOLO TOTAL -->
    <div class="total-final">

        <span>TOTAL</span>

        <span>
            S/ <?= number_format($pedido['total'], 2) ?>
        </span>

    </div>

    <hr class="divider-dash">

    <?php
    $iconos = [
        'efectivo'      => '💵',
        'tarjeta'       => '💳',
        'yape'          => '📱',
        'plin'          => '📲',
        'transferencia' => '🏦'
    ];

    $met = $pedido['metodo_pago'] ?? 'efectivo';
    ?>

    <div class="total-row">

        <span>Pago</span>

        <span class="metodo-tag">
            <?= ($iconos[$met] ?? '') ?>
            <?= ucfirst($met) ?>
        </span>

    </div>

    <?php if (($pedido['monto_pagado'] ?? 0) > $pedido['total']): ?>
    <div class="total-row" style="color:#2e7d32;font-weight:600;">

        <span>Vuelto</span>

        <span>
            S/
            <?= number_format($pedido['monto_pagado'] - $pedido['total'], 2) ?>
        </span>

    </div>
    <?php endif; ?>

    <hr class="divider-dash">

    <div style="text-align:center; font-size:11px; color:#aaa;">
        ¡Gracias por su visita!<br>
        Pedido #<?= $pedido['id'] ?>
    </div>

</div>

<div class="btn-acciones d-flex gap-2 justify-content-center mt-3">

    <a href="<?= APP_URL ?>/caja"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Volver a caja
    </a>

    <button onclick="window.print()"
            class="btn btn-outline-dark">
        <i class="bi bi-printer me-1"></i>
        Imprimir
    </button>

</div>