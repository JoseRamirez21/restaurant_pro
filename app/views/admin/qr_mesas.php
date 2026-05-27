<?php
/** @var array $mesas */
?>
<style>
.qr-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1rem; }
.qr-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.2rem; text-align:center; }
.qr-card h6 { font-weight:600; margin-bottom:.25rem; }
.qr-card p  { font-size:12px; color:#888; margin-bottom:.75rem; }
.qr-img { width:160px; height:160px; margin:0 auto .75rem; border:1px solid #e8e5df; border-radius:10px; padding:6px; background:#fff; }
.btn-dl { background:#8e44ad; color:#fff; border:none; border-radius:8px; padding:.4rem .9rem; font-size:12px; font-weight:500; cursor:pointer; text-decoration:none; display:inline-block; }
.btn-dl:hover { background:#7d3c98; color:#fff; }
.url-box { background:#f4f1eb; border-radius:8px; padding:.4rem .6rem; font-size:11px; color:#666; word-break:break-all; margin-bottom:.5rem; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <p style="font-size:13px;color:#888;margin:0;">
            Imprime o muestra estos QR en cada mesa. El cliente escanea y ve la carta desde su celular.
        </p>
    </div>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-printer me-1"></i>Imprimir todos
    </button>
</div>

<div class="qr-grid">
    <?php foreach ($mesas as $m): ?>
    <?php
    $url_carta = APP_URL . '/carta/' . $m['id'];
    $qr_api    = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' . urlencode($url_carta);
    ?>
    <div class="qr-card">
        <h6><?= htmlspecialchars($m['nombre']) ?></h6>
        <p><i class="bi bi-people me-1"></i><?= $m['capacidad'] ?> personas</p>
        <img src="<?= $qr_api ?>" class="qr-img" alt="QR Mesa <?= $m['numero'] ?>">
        <div class="url-box"><?= $url_carta ?></div>
        <a href="<?= $qr_api ?>&download=1" download="qr_mesa_<?= $m['numero'] ?>.png" class="btn-dl">
            <i class="bi bi-download me-1"></i>Descargar QR
        </a>
    </div>
    <?php endforeach; ?>
</div>

<style>
@media print {
    .sidebar,.sidebar-overlay,.topbar,.banner-solo-lec { display:none!important; }
    .main { margin-left:0!important; }
    body { background:#fff; }
    .qr-grid { grid-template-columns:repeat(3,1fr); }
    .btn-dl { display:none; }
}
</style>