<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mesa <?= $pedido['mesa_numero'] ?> — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --purple: #8e44ad;
            --dark:   #2c1f3e;
            --bg:     #f4f1eb;
        }
        * { box-sizing:border-box; -webkit-tap-highlight-color: transparent; }
        html, body { height:100%; margin:0; background:var(--bg); font-family:system-ui,sans-serif; overflow:hidden; }

        /* TOPBAR */
        .topbar {
            background:var(--dark); color:#fff;
            padding:.6rem 1rem;
            display:flex; align-items:center; justify-content:space-between;
            height:52px; position:fixed; top:0; left:0; right:0; z-index:200;
        }
        .topbar-mesa { font-size:15px; font-weight:600; }
        .topbar-meta { font-size:12px; opacity:.6; }

        /* LAYOUT PRINCIPAL */
        .layout {
            display:flex;
            height:calc(100vh - 52px);
            margin-top:52px;
        }

        /* ── CARTA (izquierda) ── */
        .carta {
            flex:1;
            display:flex;
            flex-direction:column;
            overflow:hidden;
            border-right:1px solid #e0ddd6;
            background:#fff;
        }

        /* Tabs de categoría — scrollables */
        .cat-scroll {
            display:flex;
            gap:.4rem;
            overflow-x:auto;
            padding:.6rem .75rem;
            border-bottom:1px solid #f0ede7;
            background:#faf9f6;
            -webkit-overflow-scrolling:touch;
            scrollbar-width:none;
        }
        .cat-scroll::-webkit-scrollbar { display:none; }
        .cat-tab {
            flex-shrink:0;
            border:2px solid #e0ddd6;
            background:#fff;
            border-radius:20px;
            padding:.35rem .9rem;
            font-size:13px;
            font-weight:500;
            cursor:pointer;
            transition:all .15s;
            white-space:nowrap;
        }
        .cat-tab.active {
            background:var(--purple);
            color:#fff;
            border-color:var(--purple);
        }

        /* Grid de productos */
        .productos-grid {
            flex:1;
            overflow-y:auto;
            padding:.75rem;
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(150px,1fr));
            gap:.6rem;
            align-content:start;
            -webkit-overflow-scrolling:touch;
        }
        .prod-card {
            background:#fff;
            border:2px solid #e8e5df;
            border-radius:12px;
            padding:.8rem .7rem;
            cursor:pointer;
            transition:all .15s;
            display:flex;
            flex-direction:column;
            gap:4px;
            user-select:none;
        }
        .prod-card:active { transform:scale(.96); background:#f9f5fd; border-color:var(--purple); }
        .prod-card.sin-stock { opacity:.45; pointer-events:none; }
        .prod-nombre { font-size:13px; font-weight:600; line-height:1.3; }
        .prod-precio { font-size:14px; font-weight:700; color:var(--purple); }
        .prod-tiempo { font-size:11px; color:#aaa; }

        /* ── PANEL PEDIDO (derecha) ── */
        .pedido-panel {
            width:280px;
            display:flex;
            flex-direction:column;
            background:#fff;
        }
        .pedido-header {
            padding:.75rem 1rem;
            border-bottom:1px solid #f0ede7;
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .pedido-header h6 { margin:0; font-size:14px; font-weight:600; }
        .pedido-items {
            flex:1;
            overflow-y:auto;
            padding:.5rem .75rem;
            -webkit-overflow-scrolling:touch;
        }
        .pedido-item {
            display:flex;
            align-items:center;
            gap:.5rem;
            padding:.45rem 0;
            border-bottom:1px solid #f7f5f0;
            font-size:13px;
        }
        .pedido-item:last-child { border-bottom:none; }
        .item-cant {
            background:#f4f1eb;
            border-radius:6px;
            padding:2px 7px;
            font-weight:600;
            font-size:12px;
            flex-shrink:0;
        }
        .item-nombre { flex:1; font-size:13px; line-height:1.2; }
        .item-precio { font-size:12px; color:#888; flex-shrink:0; }
        .btn-quitar {
            background:none; border:none;
            color:#ccc; font-size:18px;
            padding:0 2px; cursor:pointer;
            line-height:1; flex-shrink:0;
        }
        .btn-quitar:active { color:#dc3545; }

        /* Controles cantidad inline */
        .ctrl-cant {
            display:flex; align-items:center; gap:3px;
        }
        .ctrl-btn {
            width:24px; height:24px;
            border-radius:50%;
            border:1.5px solid #e0ddd6;
            background:#fff;
            font-size:14px;
            display:flex; align-items:center; justify-content:center;
            cursor:pointer; flex-shrink:0;
            transition:all .1s;
        }
        .ctrl-btn:active { background:var(--purple); color:#fff; border-color:var(--purple); }

        /* Totales */
        .pedido-totales {
            padding:.75rem 1rem;
            border-top:1px solid #f0ede7;
            background:#faf9f6;
        }
        .tot-row { display:flex; justify-content:space-between; font-size:12px; color:#888; margin-bottom:2px; }
        .tot-final { display:flex; justify-content:space-between; font-size:16px; font-weight:700; color:var(--dark); margin-top:4px; }

        /* Botones de acción */
        .pedido-footer { padding:.75rem; display:flex; flex-direction:column; gap:.5rem; }
        .btn-cocina {
            background:#28a745; color:#fff;
            border:none; border-radius:10px;
            padding:.7rem; font-size:14px; font-weight:600;
            width:100%; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:6px;
        }
        .btn-cocina:active { background:#218838; }
        .btn-cocina:disabled { background:#ccc; cursor:not-allowed; }
        .btn-cuenta {
            background:#f4f1eb; color:var(--dark);
            border:1.5px solid #e0ddd6;
            border-radius:10px;
            padding:.55rem; font-size:13px; font-weight:500;
            width:100%; cursor:pointer;
        }
        .btn-cuenta:active { background:#e0ddd6; }

        /* Empty state */
        .empty-cart { text-align:center; padding:2rem 1rem; color:#ccc; }
        .empty-cart i { font-size:2.5rem; }

        /* TOAST rápido */
        .toast-rapido {
            position:fixed; bottom:1rem; left:50%; transform:translateX(-50%);
            background:#2c1f3e; color:#fff;
            padding:.5rem 1.2rem; border-radius:20px;
            font-size:13px; font-weight:500;
            z-index:999; opacity:0;
            transition:opacity .2s;
            pointer-events:none;
        }
        .toast-rapido.show { opacity:1; }

        /* RESPONSIVE tablet portrait */
        @media (max-width: 600px) {
            .pedido-panel { width:100%; position:fixed; bottom:0; left:0; right:0; height:45vh; border-top:2px solid var(--purple); z-index:100; }
            .carta { height:55vh; }
            .layout { flex-direction:column; height:auto; }
            .productos-grid { grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); }
        }
    </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <div>
        <div class="topbar-mesa">
            <a href="<?= APP_URL ?>/mesas" style="color:rgba(255,255,255,.5);text-decoration:none;margin-right:8px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            Mesa <?= $pedido['mesa_numero'] ?>
        </div>
        <div class="topbar-meta"><?= $pedido['personas'] ?> personas · <?= htmlspecialchars($_SESSION['nombre']) ?></div>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span style="font-size:12px;opacity:.5;">Pedido #<?= $pedido['id'] ?></span>
        <a href="<?= APP_URL ?>/logout" style="color:rgba(255,255,255,.4);font-size:18px;">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
</div>

<div class="layout">

    <!-- ── CARTA ── -->
    <div class="carta">

        <!-- Tabs categorías -->
        <div class="cat-scroll" id="catScroll">
            <button class="cat-tab active" onclick="filtrar(0,this)">
                <i class="bi bi-grid-fill me-1"></i>Todos
            </button>
            <?php foreach ($categorias as $cat): ?>
            <button class="cat-tab" onclick="filtrar(<?= $cat['id'] ?>,this)"
                    style="--cat:<?= $cat['color'] ?>">
                <i class="bi <?= $cat['icono'] ?? 'bi-grid' ?> me-1"></i>
                <?= htmlspecialchars($cat['nombre']) ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Grid productos -->
        <div class="productos-grid" id="gridProductos">
            <?php foreach ($productos as $p): ?>
            <div class="prod-card"
                 data-cat="<?= $p['categoria_id'] ?>"
                 data-id="<?= $p['id'] ?>"
                 data-nombre="<?= htmlspecialchars(addslashes($p['nombre'])) ?>"
                 data-precio="<?= $p['precio'] ?>"
                 onclick="agregarProducto(<?= $p['id'] ?>,'<?= addslashes($p['nombre']) ?>',<?= $p['precio'] ?>)">
                <div class="prod-nombre"><?= htmlspecialchars($p['nombre']) ?></div>
                <div class="prod-precio">S/ <?= number_format($p['precio'],2) ?></div>
                <div class="prod-tiempo"><i class="bi bi-clock"></i> ~<?= $p['tiempo_prep_min'] ?> min</div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>

    <!-- ── PANEL PEDIDO ── -->
    <div class="pedido-panel">

        <div class="pedido-header">
            <h6><i class="bi bi-receipt me-2"></i>Pedido</h6>
            <span style="font-size:12px;color:#aaa;" id="contadorItems">0 items</span>
        </div>

        <div class="pedido-items" id="pedidoItems">
            <?php if (empty($detalle)): ?>
            <div class="empty-cart" id="emptyCart">
                <i class="bi bi-basket"></i>
                <p style="font-size:13px;margin-top:.5rem;">Toca un plato para agregar</p>
            </div>
            <?php else: ?>
            <?php foreach ($detalle as $item): ?>
            <div class="pedido-item" id="pitem-<?= $item['id'] ?>">
                <div class="ctrl-cant">
                    <button class="ctrl-btn" onclick="cambiarCant(<?= $item['id'] ?>,<?= $pedido['id'] ?>,-1)">−</button>
                    <span class="item-cant" id="cant-<?= $item['id'] ?>"><?= $item['cantidad'] ?></span>
                    <button class="ctrl-btn" onclick="cambiarCant(<?= $item['id'] ?>,<?= $pedido['id'] ?>,1)">+</button>
                </div>
                <span class="item-nombre"><?= htmlspecialchars($item['producto_nombre']) ?></span>
                <span class="item-precio">S/ <?= number_format($item['subtotal'],2) ?></span>
                <button class="btn-quitar" onclick="quitarItem(<?= $item['id'] ?>,<?= $pedido['id'] ?>)">×</button>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="pedido-totales">
            <div class="tot-final"><span>TOTAL</span><span>S/ <span id="total"><?= number_format($pedido['subtotal'],2) ?></span></span></div>
        </div>

        <div class="pedido-footer">
            <button class="btn-cocina" id="btnCocina"
                    onclick="enviarCocina()"
                    <?= empty($detalle) ? 'disabled' : '' ?>>
                <i class="bi bi-fire"></i>Enviar a cocina
            </button>
            <?php if (!empty($detalle) && $pedido['total'] > 0): ?>
            <button class="btn-cuenta" onclick="location.href='<?= APP_URL ?>/caja/cuenta/<?= $pedido['id'] ?>'">
                <i class="bi bi-receipt me-1"></i>Pedir cuenta
            </button>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Toast rápido -->
<div class="toast-rapido" id="toastRapido"></div>

<script>
const PEDIDO_ID = <?= $pedido['id'] ?>;
const APP_URL   = '<?= APP_URL ?>';

// Filtrar por categoría
function filtrar(catId, btn) {
    document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.prod-card').forEach(c => {
        c.style.display = (!catId || parseInt(c.dataset.cat) === catId) ? '' : 'none';
    });
}

// Mostrar toast rápido
function toast(msg) {
    const el = document.getElementById('toastRapido');
    el.textContent = msg;
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 1800);
}

// Agregar producto
function agregarProducto(prodId, nombre, precio) {
    // Feedback táctil inmediato
    toast('+ ' + nombre);

    fetch(APP_URL + '/pedidos/agregar/' + PEDIDO_ID, {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'producto_id=' + prodId + '&cantidad=1'
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) renderPedido(data.detalle, data.totales);
    });
}

// Cambiar cantidad
function cambiarCant(detalleId, pedidoId, delta) {
    const cantEl = document.getElementById('cant-' + detalleId);
    const actual = parseInt(cantEl?.textContent || '1');

    if (actual + delta <= 0) {
        quitarItem(detalleId, pedidoId);
        return;
    }

    // Actualizar visual inmediato
    if (cantEl) cantEl.textContent = actual + delta;

    fetch(APP_URL + '/pedidos/agregar/' + pedidoId, {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'producto_id=0&cantidad=' + delta + '&detalle_id=' + detalleId
    })
    .then(r => r.json())
    .then(data => { if (data.ok) actualizarTotales(data.totales); });
}

// Quitar item
function quitarItem(detalleId, pedidoId) {
    const el = document.getElementById('pitem-' + detalleId);
    if (el) { el.style.opacity='.3'; el.style.pointerEvents='none'; }

    fetch(APP_URL + '/pedidos/quitar/' + detalleId, {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'pedido_id=' + pedidoId
    })
    .then(r => r.json())
    .then(data => { if (data.ok) renderPedido(data.detalle, data.totales); });
}

// Renderizar lista del pedido
function renderPedido(detalle, totales) {
    const box = document.getElementById('pedidoItems');
    const btn = document.getElementById('btnCocina');

    actualizarTotales(totales);
    document.getElementById('contadorItems').textContent =
        detalle.length + (detalle.length === 1 ? ' item' : ' items');

    if (detalle.length === 0) {
        box.innerHTML = '<div class="empty-cart"><i class="bi bi-basket"></i><p style="font-size:13px;margin-top:.5rem;">Toca un plato para agregar</p></div>';
        btn.disabled = true;
        return;
    }

    btn.disabled = false;
    box.innerHTML = detalle.map(item => `
        <div class="pedido-item" id="pitem-${item.id}">
            <div class="ctrl-cant">
                <button class="ctrl-btn" onclick="cambiarCant(${item.id},${PEDIDO_ID},-1)">−</button>
                <span class="item-cant" id="cant-${item.id}">${item.cantidad}</span>
                <button class="ctrl-btn" onclick="cambiarCant(${item.id},${PEDIDO_ID},1)">+</button>
            </div>
            <span class="item-nombre">${item.producto_nombre}</span>
            <span class="item-precio">S/ ${parseFloat(item.subtotal).toFixed(2)}</span>
            <button class="btn-quitar" onclick="quitarItem(${item.id},${PEDIDO_ID})">×</button>
        </div>
    `).join('');
}

function actualizarTotales(totales) {
    document.getElementById('total').textContent = totales.subtotal;
}

function enviarCocina() {
    toast('✅ Enviado a cocina');
    document.getElementById('btnCocina').textContent = '✅ En cocina';
    document.getElementById('btnCocina').disabled = true;
    setTimeout(() => {
        document.getElementById('btnCocina').innerHTML = '<i class="bi bi-fire"></i> Enviar a cocina';
        document.getElementById('btnCocina').disabled = false;
    }, 2000);
}

// Contador inicial
document.getElementById('contadorItems').textContent =
    <?= count($detalle) ?> + (<?= count($detalle) ?> === 1 ? ' item' : ' items');
</script>

<script>window.APP_URL = "<?= APP_URL ?>";</script>
<script src="<?= APP_URL ?>/public/js/notificaciones.js"></script>
</body>
</html>