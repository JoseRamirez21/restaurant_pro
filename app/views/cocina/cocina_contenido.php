<?php
/** @var array $comandas */
$porPedido = [];
foreach ($comandas as $c) $porPedido[$c['pedido_id']][] = $c;
?>
<style>
.comanda { background:#1a1a2e; color:#fff; border-radius:14px; padding:1.1rem; border:1.5px solid rgba(255,255,255,.1); }
.comanda.urgente { border-color:#e94560; }
.com-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem; }
.mesa-badge { background:#e94560; border-radius:8px; padding:4px 12px; font-weight:600; font-size:14px; }
.item-row { background:rgba(255,255,255,.07); border-radius:8px; padding:.5rem .8rem; margin-bottom:.5rem; display:flex; justify-content:space-between; align-items:center; }
.cant-badge { background:#e94560; border-radius:6px; padding:2px 9px; font-size:13px; font-weight:600; color:#fff; }
.btn-prep  { background:#fd7e14; border:none; color:#fff; border-radius:8px; padding:.35rem; font-size:12px; width:100%; margin-top:.4rem; cursor:pointer; }
.btn-listo { background:#28a745; border:none; color:#fff; border-radius:8px; padding:.5rem; font-size:13px; font-weight:500; width:100%; margin-top:.6rem; cursor:pointer; }
.sin-comandas { text-align:center; padding:3rem; color:#aaa; }
</style>

<div class="d-flex align-items-center justify-content-between mb-3">
    <span class="badge bg-danger fs-6"><?= count($comandas) ?> ítems pendientes</span>
    <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
    </button>
</div>

<?php if (empty($porPedido)): ?>
<div class="sin-comandas">
    <i class="bi bi-check-circle-fill text-success" style="font-size:3rem;"></i>
    <h5 class="mt-3">Todo al día</h5>
    <p>No hay comandas pendientes en cocina</p>
</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($porPedido as $pedido_id => $items):
        $first   = $items[0];
        $minutos = round((time() - strtotime($first['creado_en'])) / 60);
        $urgente = $minutos >= 15 ? 'urgente' : '';
    ?>
    <div class="col-sm-6 col-xl-4">
        <div class="comanda <?= $urgente ?>" id="comanda-<?= $pedido_id ?>">
            <div class="com-header">
                <span class="mesa-badge">Mesa <?= $first['mesa_numero'] ?></span>
                <span style="font-size:12px; opacity:.5;">
                    <i class="bi bi-clock"></i> <?= $minutos ?> min <?= $urgente ? '⚠️' : '' ?>
                </span>
            </div>
            <?php foreach ($items as $item): ?>
            <div class="item-row" id="item-<?= $item['detalle_id'] ?>">
                <div style="flex:1;">
                    <div style="font-size:14px; color:#fff;"><?= htmlspecialchars($item['producto_nombre']) ?></div>
                    <?php if ($item['observaciones']): ?>
                    <div style="font-size:11px; opacity:.5;">📝 <?= htmlspecialchars($item['observaciones']) ?></div>
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
                <i class="bi bi-check-lg me-1"></i>Listo — Mesa <?= $first['mesa_numero'] ?>
            </button>
            <div style="font-size:11px; opacity:.4; margin-top:.5rem;">
                <i class="bi bi-person me-1"></i><?= htmlspecialchars($first['mesero_nombre']) ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
const APP_URL = '<?= APP_URL ?>';
function cambiarEstado(id, estado, btn) {
    fetch(APP_URL + '/cocina/estado/' + id, {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'estado=' + estado
    }).then(r=>r.json()).then(d=>{ if(d.ok){ btn.parentElement.innerHTML += '<span style="font-size:11px;color:#fd7e14;">🔥 En preparación</span>'; btn.remove(); }});
}
function marcarListo(pedidoId, btn) {
    document.querySelectorAll('#comanda-' + pedidoId + ' .item-row').forEach(item => {
        fetch(APP_URL + '/cocina/estado/' + item.id.replace('item-',''), {
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'estado=listo'
        });
    });
    const card = document.getElementById('comanda-' + pedidoId);
    card.style.opacity='.4'; card.style.pointerEvents='none';
    btn.textContent='✅ Listo — notificando mozo...'; btn.style.background='#555';
    setTimeout(()=>card.remove(), 2000);
}
setTimeout(()=>location.reload(), 30000);
</script>