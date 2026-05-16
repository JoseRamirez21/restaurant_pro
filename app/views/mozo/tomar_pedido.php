<?php
// Solo para autocompletado del editor — no afecta el sistema
/** @var array $pedido */
/** @var array $productos */
/** @var array $categorias */
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido — Mesa <?= $pedido['mesa_numero'] ?> — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing:border-box; }
        body { background:#f4f1eb; margin:0; }
        .topbar { background:#2c1f3e; color:#fff; padding:.75rem 1rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:200; }
        .layout { display:flex; height:calc(100vh - 52px); }

        /* CARTA (izquierda) */
        .carta { flex:1; overflow-y:auto; padding:1rem; }
        .cat-tabs { display:flex; gap:.5rem; overflow-x:auto; padding-bottom:.5rem; margin-bottom:.75rem; }
        .cat-tabs::-webkit-scrollbar { height:4px; }
        .cat-tab { border:1.5px solid #ddd; background:#fff; border-radius:20px; padding:.3rem .9rem; font-size:13px; white-space:nowrap; cursor:pointer; transition:all .2s; }
        .cat-tab.active { background:#8e44ad; color:#fff; border-color:#8e44ad; }
        .producto-card { background:#fff; border:1px solid #e0ddd6; border-radius:12px; padding:.9rem; display:flex; justify-content:space-between; align-items:center; margin-bottom:.6rem; cursor:pointer; transition:all .15s; }
        .producto-card:hover { border-color:#8e44ad; transform:translateX(2px); }
        .prod-nombre { font-size:14px; font-weight:500; margin-bottom:2px; }
        .prod-desc { font-size:12px; color:#888; margin-bottom:4px; }
        .prod-precio { font-size:15px; font-weight:600; color:#8e44ad; }
        .prod-tiempo { font-size:11px; color:#aaa; }
        .btn-agregar { background:#8e44ad; color:#fff; border:none; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }

        /* PEDIDO (derecha) */
        .pedido-panel { width:300px; background:#fff; border-left:1px solid #e0ddd6; display:flex; flex-direction:column; }
        .pedido-header { padding:.9rem 1rem; border-bottom:1px solid #e0ddd6; }
        .pedido-header h6 { margin:0; font-weight:600; font-size:14px; }
        .pedido-items { flex:1; overflow-y:auto; padding:.75rem 1rem; }
        .pedido-item { display:flex; align-items:center; gap:.6rem; margin-bottom:.6rem; font-size:13px; }
        .pedido-item .cant { background:#f4f1eb; border-radius:6px; padding:2px 8px; font-weight:600; font-size:12px; }
        .pedido-item .nombre { flex:1; }
        .pedido-item .precio { color:#555; font-size:12px; }
        .pedido-item .btn-quitar { background:none; border:none; color:#ccc; padding:0; cursor:pointer; font-size:16px; }
        .pedido-item .btn-quitar:hover { color:#dc3545; }
        .pedido-totales { padding:.75rem 1rem; border-top:1px solid #e0ddd6; font-size:13px; }
        .pedido-totales .linea { display:flex; justify-content:space-between; margin-bottom:.3rem; color:#666; }
        .pedido-totales .total { display:flex; justify-content:space-between; font-size:16px; font-weight:700; color:#2c1f3e; margin-top:.4rem; }
        .pedido-footer { padding:.75rem 1rem; border-top:1px solid #e0ddd6; }
        .btn-enviar { background:#28a745; color:#fff; border:none; border-radius:10px; padding:.65rem; font-size:14px; font-weight:500; width:100%; }
        .btn-enviar:hover { background:#218838; }
        .empty-pedido { text-align:center; color:#ccc; padding:2rem 1rem; }

        /* Responsive: en móvil el panel baja */
        @media (max-width: 768px) {
            .layout { flex-direction:column; height:auto; }
            .pedido-panel { width:100%; border-left:none; border-top:1px solid #e0ddd6; max-height:50vh; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <a href="<?= APP_URL ?>/mesas" class="btn btn-sm btn-outline-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div style="font-weight:500;">
        Mesa <?= $pedido['mesa_numero'] ?>
        <span style="opacity:.6; font-size:12px; margin-left:6px;"><?= $pedido['personas'] ?> personas</span>
    </div>
    <span style="font-size:12px; opacity:.6;"><?= htmlspecialchars($_SESSION['nombre']) ?></span>
</div>

<div class="layout">

    <!-- CARTA -->
    <div class="carta">
        <div class="cat-tabs" id="catTabs">
            <button class="cat-tab active" onclick="filtrarCategoria(0, this)">Todos</button>
            <?php foreach ($categorias as $cat): ?>
            <button class="cat-tab" onclick="filtrarCategoria(<?= $cat['id'] ?>, this)">
                <?= htmlspecialchars($cat['nombre']) ?>
            </button>
            <?php endforeach; ?>
        </div>

        <div id="listaProductos">
            <?php foreach ($productos as $p): ?>
            <div class="producto-card" data-cat="<?= $p['categoria_id'] ?>" onclick="agregarProducto(<?= $p['id'] ?>, '<?= addslashes($p['nombre']) ?>', <?= $p['precio'] ?>)">
                <div style="flex:1; padding-right:.5rem;">
                    <div class="prod-nombre"><?= htmlspecialchars($p['nombre']) ?></div>
                    <?php if ($p['descripcion']): ?>
                    <div class="prod-desc"><?= htmlspecialchars(mb_substr($p['descripcion'], 0, 60)) ?>...</div>
                    <?php endif; ?>
                    <div class="d-flex align-items-center gap-2">
                        <span class="prod-precio">S/ <?= number_format($p['precio'], 2) ?></span>
                        <span class="prod-tiempo"><i class="bi bi-clock"></i> ~<?= $p['tiempo_prep_min'] ?> min</span>
                    </div>
                </div>
                <button class="btn-agregar"><i class="bi bi-plus"></i></button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- PANEL PEDIDO -->
    <div class="pedido-panel">
        <div class="pedido-header">
            <h6><i class="bi bi-receipt me-2"></i>Pedido #<?= $pedido['id'] ?></h6>
        </div>

        <div class="pedido-items" id="pedidoItems">
            <?php if (empty($detalle)): ?>
            <div class="empty-pedido" id="emptyMsg">
                <i class="bi bi-bag" style="font-size:2rem;"></i>
                <p class="mt-2" style="font-size:13px;">Agrega platos del menú</p>
            </div>
            <?php else: ?>
                <?php foreach ($detalle as $item): ?>
                <div class="pedido-item" id="item-<?= $item['id'] ?>">
                    <span class="cant"><?= $item['cantidad'] ?>x</span>
                    <span class="nombre"><?= htmlspecialchars($item['producto_nombre']) ?></span>
                    <span class="precio">S/ <?= number_format($item['subtotal'], 2) ?></span>
                    <button class="btn-quitar" onclick="quitarItem(<?= $item['id'] ?>)">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="pedido-totales">
            <div class="linea"><span>Subtotal</span><span>S/ <span id="subtotal"><?= number_format($pedido['subtotal'], 2) ?></span></span></div>
            <div class="linea"><span>IGV (18%)</span><span>S/ <span id="igv"><?= number_format($pedido['igv'], 2) ?></span></span></div>
            <div class="linea"><span>Servicio (10%)</span><span>S/ <span id="servicio"><?= number_format($pedido['servicio'], 2) ?></span></span></div>
            <div class="total"><span>TOTAL</span><span>S/ <span id="total"><?= number_format($pedido['total'], 2) ?></span></span></div>
        </div>

        <div class="pedido-footer">
            <button class="btn-enviar" onclick="enviarCocina()" <?= empty($detalle) ? 'disabled' : '' ?> id="btnEnviar">
                <i class="bi bi-send me-2"></i>Enviar a cocina
            </button>
        </div>
    </div>

</div>

<script>
const PEDIDO_ID  = <?= $pedido['id'] ?>;
const APP_URL    = '<?= APP_URL ?>';

function filtrarCategoria(catId, el) {
    document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.producto-card').forEach(card => {
        card.style.display = (catId === 0 || parseInt(card.dataset.cat) === catId) ? '' : 'none';
    });
}

function agregarProducto(prodId, nombre, precio) {
    fetch(APP_URL + '/pedidos/agregar/' + PEDIDO_ID, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'producto_id=' + prodId + '&cantidad=1'
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) renderDetalle(data.detalle, data.totales);
    });
}

function quitarItem(detalleId) {
    fetch(APP_URL + '/pedidos/quitar/' + detalleId, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'pedido_id=' + PEDIDO_ID
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) renderDetalle(data.detalle, data.totales);
    });
}

function renderDetalle(detalle, totales) {
    const box = document.getElementById('pedidoItems');
    document.getElementById('subtotal').textContent = totales.subtotal;
    document.getElementById('igv').textContent      = totales.igv;
    document.getElementById('servicio').textContent = totales.servicio;
    document.getElementById('total').textContent    = totales.total;
    document.getElementById('btnEnviar').disabled   = detalle.length === 0;

    if (detalle.length === 0) {
        box.innerHTML = '<div class="empty-pedido"><i class="bi bi-bag" style="font-size:2rem;"></i><p class="mt-2" style="font-size:13px;">Agrega platos del menú</p></div>';
        return;
    }

    box.innerHTML = detalle.map(item => `
        <div class="pedido-item" id="item-${item.id}">
            <span class="cant">${item.cantidad}x</span>
            <span class="nombre">${item.producto_nombre}</span>
            <span class="precio">S/ ${parseFloat(item.subtotal).toFixed(2)}</span>
            <button class="btn-quitar" onclick="quitarItem(${item.id})"><i class="bi bi-x"></i></button>
        </div>
    `).join('');
}

function enviarCocina() {
    alert('✅ Pedido enviado a cocina');
}
</script>

</body>
</html>