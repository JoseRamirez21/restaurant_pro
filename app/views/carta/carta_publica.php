<?php
/** @var array $productos */
/** @var array $mesa */
/** @var array $categorias */
?>
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --purple:#8e44ad; --dark:#2c1f3e; }
        * { box-sizing:border-box; }
        body { background:#f4f1eb; font-family:system-ui,sans-serif; padding-bottom:2rem; }

        /* HEADER */
        .carta-header {
            background:var(--dark);
            color:#fff;
            padding:1.5rem 1rem 1rem;
            text-align:center;
            position:sticky;
            top:0;
            z-index:100;
        }
        .carta-header h1 { font-size:1.3rem; font-weight:700; margin:0 0 2px; }
        .carta-header p  { font-size:12px; opacity:.5; margin:0 0 .75rem; }
        <?php if ($mesa): ?>
        .mesa-tag { display:inline-flex; align-items:center; gap:5px; background:rgba(255,255,255,.1); border-radius:20px; padding:4px 14px; font-size:13px; margin-bottom:.75rem; }
        <?php endif; ?>

        /* CATEGORÍAS */
        .cat-scroll { display:flex; gap:.4rem; overflow-x:auto; padding:.6rem 1rem; background:#fff; border-bottom:1px solid #e8e5df; -webkit-overflow-scrolling:touch; scrollbar-width:none; position:sticky; top:88px; z-index:99; }
        .cat-scroll::-webkit-scrollbar { display:none; }
        .cat-tab { flex-shrink:0; border:2px solid #e0ddd6; background:#fff; border-radius:20px; padding:.3rem .9rem; font-size:13px; font-weight:500; cursor:pointer; white-space:nowrap; transition:all .15s; }
        .cat-tab.active { background:var(--purple); color:#fff; border-color:var(--purple); }

        /* SECCIÓN DE CATEGORÍA */
        .cat-section { padding:1rem; }
        .cat-title { font-size:11px; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:.07em; margin-bottom:.75rem; display:flex; align-items:center; gap:.5rem; }
        .cat-title::after { content:''; flex:1; height:1px; background:#e8e5df; }

        /* TARJETA DE PRODUCTO */
        .prod-card { background:#fff; border-radius:14px; border:1px solid #e8e5df; padding:1rem; margin-bottom:.6rem; display:flex; gap:.75rem; }
        .prod-info { flex:1; }
        .prod-nombre { font-size:14px; font-weight:600; color:#2c1f3e; margin-bottom:3px; }
        .prod-desc { font-size:12px; color:#888; line-height:1.4; margin-bottom:.4rem; }
        .prod-footer { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }
        .prod-precio { font-size:16px; font-weight:700; color:var(--purple); }
        .prod-tiempo { font-size:11px; color:#aaa; }
        .prod-alerg  { font-size:11px; color:#e65100; background:#fff3e0; border-radius:20px; padding:1px 8px; }
        .prod-dest   { font-size:10px; background:#fff3cd; color:#856404; border-radius:20px; padding:1px 8px; font-weight:600; }
        .prod-img { width:72px; height:72px; border-radius:10px; object-fit:cover; flex-shrink:0; background:#f4f1eb; display:flex; align-items:center; justify-content:center; font-size:2rem; }

        /* Footer */
        .carta-footer { text-align:center; padding:1.5rem 1rem; color:#aaa; font-size:12px; }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="carta-header">
    <h1>🍽 RestaurantePro</h1>
    <p>Carta digital</p>
    <?php if ($mesa): ?>
    <div class="mesa-tag">
        <i class="bi bi-table"></i> <?= htmlspecialchars($mesa['nombre']) ?>
    </div>
    <?php endif; ?>
</div>

<!-- TABS CATEGORÍAS -->
<div class="cat-scroll" id="catScroll">
    <?php foreach ($categorias as $i => $cat): ?>
    <button class="cat-tab <?= $i===0?'active':'' ?>"
            onclick="irCategoria('cat-<?= $cat['id'] ?>',this)">
        <i class="bi <?= $cat['icono'] ?? 'bi-grid' ?> me-1"></i>
        <?= htmlspecialchars($cat['nombre']) ?>
    </button>
    <?php endforeach; ?>
</div>

<!-- CONTENIDO POR CATEGORÍA -->
<div id="contenido">
<?php
// Agrupar productos por categoría
$porCat = [];
foreach ($productos as $p) {
    $porCat[$p['categoria_id']][] = $p;
}
foreach ($categorias as $cat):
    if (empty($porCat[$cat['id']])) continue;
?>
<div class="cat-section" id="cat-<?= $cat['id'] ?>">
    <div class="cat-title">
        <i class="bi <?= $cat['icono'] ?? 'bi-grid' ?>" style="color:<?= $cat['color'] ?>;font-size:15px;"></i>
        <?= htmlspecialchars($cat['nombre']) ?>
    </div>

    <?php foreach ($porCat[$cat['id']] as $p): ?>
    <div class="prod-card">
        <div class="prod-info">
            <div class="prod-nombre">
                <?= htmlspecialchars($p['nombre']) ?>
                <?php if ($p['destacado']): ?>
                <span class="prod-dest ms-1">⭐ Destacado</span>
                <?php endif; ?>
            </div>
            <?php if ($p['descripcion']): ?>
            <div class="prod-desc"><?= htmlspecialchars($p['descripcion']) ?></div>
            <?php endif; ?>
            <div class="prod-footer">
                <span class="prod-precio">S/ <?= number_format($p['precio'],2) ?></span>
                <span class="prod-tiempo"><i class="bi bi-clock me-1"></i>~<?= $p['tiempo_prep_min'] ?> min</span>
                <?php if ($p['alergenos']): ?>
                <span class="prod-alerg"><i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($p['alergenos']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <!-- Imagen o emoji de categoría -->
        <?php if ($p['imagen']): ?>
        <img src="<?= APP_URL ?>/public/img/platos/<?= htmlspecialchars($p['imagen']) ?>"
             class="prod-img" alt="<?= htmlspecialchars($p['nombre']) ?>">
        <?php else: ?>
        <div class="prod-img">
            <?php
            $emojis = ['Entradas'=>'🥗','Sopas'=>'🍲','Fondos'=>'🍽','Parrillas'=>'🥩','Postres'=>'🍮','Bebidas'=>'🥤','Cócteles'=>'🍹','Menú del día'=>'📋'];
            echo $emojis[$cat['nombre']] ?? '🍴';
            ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>
</div>

<div class="carta-footer">
    <i class="bi bi-qr-code me-1"></i>
    Escanea el QR de tu mesa para ver esta carta<br>
    <span style="margin-top:4px;display:block;">RestaurantePro © <?= date('Y') ?></span>
</div>

<script>
function irCategoria(id, btn) {
    document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior:'smooth', block:'start' });
}

// Activar tab según scroll
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const id  = entry.target.id.replace('cat-','');
            const btn = document.querySelector(`.cat-tab[onclick*="cat-${id}"]`);
            if (btn) {
                document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                btn.scrollIntoView({ behavior:'smooth', inline:'center', block:'nearest' });
            }
        }
    }), { threshold: 0.3 };
}, { threshold: 0.3 });

document.querySelectorAll('.cat-section').forEach(s => observer.observe(s));
</script>
</body>
</html>