<?php
// Solo para autocompletado del editor — no afecta el sistema
/** @var array $pedido */
/** @var array $detalle */
?>
<!DOCTYPE html>
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
        .ticket { background:#fff; border:1px solid #e0ddd6; border-radius:16px; padding:1.5rem; max-width:380px; margin:1.5rem auto; font-family: 'Courier New', monospace; }
        .ticket-titulo { text-align:center; font-size:1.1rem; font-weight:700; letter-spacing:.05em; }
        .ticket-sub { text-align:center; font-size:12px; color:#888; margin-bottom:.5rem; }
        .divider-dash { border:none; border-top:1px dashed #ccc; margin:.75rem 0; }
        .item-row { display:flex; justify-content:space-between; font-size:13px; margin-bottom:.3rem; }
        .total-row { display:flex; justify-content:space-between; font-size:12px; color:#666; margin-bottom:.25rem; }
        .total-final { display:flex; justify-content:space-between; font-size:1.1rem; font-weight:700; margin-top:.4rem; }
        .check-icon { text-align:center; font-size:3rem; color:#28a745; margin-bottom:.5rem; }
        .metodo-pill { display:inline-block; background:#f0f0f0; border-radius:20px; padding:2px 10px; font-size:12px; text-transform:capitalize; }
        @media print {
            .topbar, .btn-acciones { display:none !important; }
            body { background:#fff; }
            .ticket { border:none; box-shadow:none; margin:0; }
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

<div class="ticket">

    <div class="check-icon"><i class="bi bi-check-circle-fill"></i></div>
    <div class="ticket-titulo">RestaurantePro</div>
    <div class="ticket-sub">
        Mesa <?= $pedido['mesa_numero'] ?> &nbsp;·&nbsp; <?= date('d/m/Y H:i') ?>
    </div>
    <div class="ticket-sub">
        Mozo: <?= htmlspecialchars($pedido['mesero_nombre']) ?>
    </div>

    <hr class="divider-dash">

    <?php foreach ($detalle as $item): ?>
    <div class="item-row">
        <span><?= $item['cantidad'] ?>x <?= htmlspecialchars($item['producto_nombre']) ?></span>
        <span>S/ <?= number_format($item['subtotal'], 2) ?></span>
    </div>
    <?php endforeach; ?>

    <hr class="divider-dash">

    <div class="total-row"><span>Subtotal</span><span>S/ <?= number_format($pedido['subtotal'], 2) ?></span></div>
    <div class="total-row"><span>IGV (18%)</span><span>S/ <?= number_format($pedido['igv'], 2) ?></span></div>
    <div class="total-row"><span>Servicio (10%)</span><span>S/ <?= number_format($pedido['servicio'], 2) ?></span></div>
    <div class="total-final"><span>TOTAL</span><span>S/ <?= number_format($pedido['total'], 2) ?></span></div>

    <hr class="divider-dash">

    <div class="total-row">
        <span>Método de pago</span>
        <span class="metodo-pill"><?= ucfirst($pedido['metodo_pago'] ?? 'efectivo') ?></span>
    </div>
    <div class="total-row">
        <span>Monto recibido</span>
        <span>S/ <?= number_format($pedido['monto_pagado'], 2) ?></span>
    </div>
    <?php if ($pedido['monto_pagado'] > $pedido['total']): ?>
    <div class="total-row" style="color:#2e7d32; font-weight:600;">
        <span>Vuelto</span>
        <span>S/ <?= number_format($pedido['monto_pagado'] - $pedido['total'], 2) ?></span>
    </div>
    <?php endif; ?>

    <hr class="divider-dash">
    <div style="text-align:center; font-size:11px; color:#aaa;">
        ¡Gracias por su visita!<br>
        Pedido #<?= $pedido['id'] ?>
    </div>

</div>

<!-- Botones de acción -->
<div class="btn-acciones d-flex gap-2 justify-content-center mb-4">
    <a href="<?= APP_URL ?>/caja" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver a caja
    </a>
    <button onclick="window.print()" class="btn btn-outline-dark">
        <i class="bi bi-printer me-1"></i>Imprimir
    </button>
</div>

</body>
</html>