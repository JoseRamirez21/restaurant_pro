
<?php
// Solo para autocompletado del editor — no afecta el sistema
/** @var array $comandas */
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cocina — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#1a1a2e; color:#fff; margin:0; }
        .topbar { background:#16213e; padding:.9rem 1.2rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,.1); position:sticky; top:0; z-index:100; }
        .brand { font-size:15px; font-weight:500; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:1rem; padding:1rem; }
        .comanda { background:#0f3460; border-radius:14px; padding:1.1rem; border:1.5px solid rgba(255,255,255,.1); }
        .comanda.urgente { border-color:#e94560; }
        .com-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem; }
        .mesa-badge { background:#e94560; border-radius:8px; padding:4px 12px; font-weight:600; font-size:14px; }
        .tiempo-badge { font-size:11px; opacity:.5; }
        .item-row { background:rgba(255,255,255,.07); border-radius:8px; padding:.5rem .8rem; margin-bottom:.5rem; display:flex; justify-content:space-between; align-items:center; }
        .item-nombre { font-size:14px; }
        .item-obs { font-size:11px; opacity:.5; }
        .cant-badge { background:#e94560; border-radius:6px; padding:2px 9px; font-size:13px; font-weight:600; }
        .btn-prep { background:#fd7e14; border:none; color:#fff; border-radius:8px; padding:.4rem; font-size:12px; width:100%; margin-top:.4rem; cursor:pointer; }
        .btn-listo { background:#28a745; border:none; color:#fff; border-radius:8px; padding:.5rem; font-size:13px; font-weight:500; width:100%; margin-top:.6rem; cursor:pointer; }
        .mesero { font-size:11px; opacity:.45; margin-top:.5rem; }
        .sin-comandas { text-align:center; padding:4rem 1rem; opacity:.3; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="brand"><i class="bi bi-fire me-2"></i>Cocina — KDS</div>
    <div class="d-flex align-items-center gap-3">
        <span id="reloj" style="font-size:13px; opacity:.6;"></span>
        <span class="badge bg-danger" id="totalPendientes">
            <?= count($comandas) ?> pendientes
        </span>
        <a href="<?= APP_URL ?>/logout" class="btn btn-sm btn-outline-light">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
</div>

<?php if (empty($comandas)): ?>
<div class="sin-comandas">
    <i class="bi bi-check-circle" style="font-size:3.5rem;"></i>
    <h5 class="mt-3">Todo al día</h5>
    <p>No hay comandas pendientes</p>
</div>
<?php else: ?>

<?php
// Agrupar por pedido
$porPedido = [];
foreach ($comandas as $c) {
    $porPedido[$c['pedido_id']][] = $c;
}
?>

<div class="grid">
    <?php foreach ($porPedido as $pedido_id => $items):
        $first   = $items[0];
        $minutos = round((time() - strtotime($first['creado_en'])) / 60);
        $urgente = $minutos >= 15 ? 'urgente' : '';
    ?>
    <div class="comanda <?= $urgente ?>" id="comanda-<?= $pedido_id ?>">
        <div class="com-header">
            <span class="mesa-badge">Mesa <?= $first['mesa_numero'] ?></span>
            <span class="tiempo-badge">
                <i class="bi bi-clock"></i> <?= $minutos ?> min
                <?= $urgente ? '⚠️' : '' ?>
            </span>
        </div>

        <?php foreach ($items as $item): ?>
        <div class="item-row" id="item-<?= $item['detalle_id'] ?>">
            <div>
                <div class="item-nombre"><?= htmlspecialchars($item['producto_nombre']) ?></div>
                <?php if ($item['observaciones']): ?>
                <div class="item-obs">📝 <?= htmlspecialchars($item['observaciones']) ?></div>
                <?php endif; ?>
                <?php if ($item['item_estado'] === 'pendiente'): ?>
                <button class="btn-prep" onclick="cambiarEstado(<?= $item['detalle_id'] ?>, 'en_preparacion', this)">
                    Iniciar preparación
                </button>
                <?php else: ?>
                <span style="font-size:11px; color:#fd7e14;">🔥 En preparación</span>
                <?php endif; ?>
            </div>
            <span class="cant-badge"><?= $item['cantidad'] ?>x</span>
        </div>
        <?php endforeach; ?>

        <button class="btn-listo" onclick="marcarListo(<?= $pedido_id ?>, this)">
            <i class="bi bi-check-lg me-1"></i> Todo listo — Mesa <?= $first['mesa_numero'] ?>
        </button>
        <div class="mesero"><i class="bi bi-person me-1"></i><?= htmlspecialchars($first['mesero_nombre']) ?></div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
const APP_URL = '<?= APP_URL ?>';

function cambiarEstado(detalleId, estado, btn) {
    fetch(APP_URL + '/cocina/estado/' + detalleId, {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'estado=' + estado
    })
    .then(r => r.json())
    .then(d => {
        if (d.ok) {
            btn.parentElement.innerHTML += '<span style="font-size:11px;color:#fd7e14;">🔥 En preparación</span>';
            btn.remove();
        }
    });
}

function marcarListo(pedidoId, btn) {
    // Marcar todos los items del pedido como listos
    const items = document.querySelectorAll('#comanda-' + pedidoId + ' .item-row');
    items.forEach(item => {
        const id = item.id.replace('item-', '');
        fetch(APP_URL + '/cocina/estado/' + id, {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'estado=listo'
        });
    });
    const card = document.getElementById('comanda-' + pedidoId);
    card.style.opacity = '.4';
    card.style.pointerEvents = 'none';
    btn.textContent = '✅ Listo — notificando mozo...';
    btn.style.background = '#555';
    setTimeout(() => card.remove(), 2000);
}

// Reloj
function reloj() {
    document.getElementById('reloj').textContent = new Date().toLocaleTimeString('es-PE');
}
reloj(); setInterval(reloj, 1000);

// Auto-refrescar cada 30 segundos
setTimeout(() => location.reload(), 30000);
</script>
<script>window.APP_URL = "<?= APP_URL ?>";</script>
<script src="<?= APP_URL ?>/public/js/notificaciones.js"></script>
</body>
</html>