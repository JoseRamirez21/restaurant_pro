<?php
/** @var array $mesas */
$zonas_nombres = [
    'salon_principal' => 'Salón principal',
    'terraza'         => 'Terraza',
    'privado'         => 'Sala privada',
    'bar'             => 'Bar',
];
$resumen = Mesa::resumenEstados();
?>
<style>
.mesa-card { background:#fff; border:2px solid #e8e5df; border-radius:14px; padding:1.1rem; text-align:center; cursor:pointer; transition:all .2s; text-decoration:none; color:inherit; display:block; }
.mesa-card:hover { transform:translateY(-3px); box-shadow:0 4px 20px rgba(0,0,0,.08); color:inherit; }
.mesa-card.libre     { border-color:#28a745; }
.mesa-card.ocupada   { border-color:#dc3545; background:#fff8f8; }
.mesa-card.reservada { border-color:#fd7e14; background:#fffaf5; }
.mesa-card.mantenimiento { border-color:#bbb; opacity:.6; cursor:not-allowed; }
.mesa-icon { font-size:2rem; margin-bottom:.4rem; }
.libre     .mesa-icon { color:#28a745; }
.ocupada   .mesa-icon { color:#dc3545; }
.reservada .mesa-icon { color:#fd7e14; }
.mantenimiento .mesa-icon { color:#aaa; }
.mesa-num  { font-size:1rem; font-weight:600; margin-bottom:2px; }
.mesa-info { font-size:11px; color:#888; margin-bottom:.4rem; }
.estado-badge { font-size:11px; padding:2px 10px; border-radius:20px; font-weight:500; }
.libre     .estado-badge { background:#e8f5e9; color:#2e7d32; }
.ocupada   .estado-badge { background:#ffebee; color:#c62828; }
.reservada .estado-badge { background:#fff3e0; color:#e65100; }
.mantenimiento .estado-badge { background:#f1f1f1; color:#999; }
.zona-label { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .5rem; }
.resumen-pill { display:inline-flex; align-items:center; gap:5px; font-size:13px; padding:4px 12px; border-radius:20px; font-weight:500; }
</style>

<!-- Resumen rápido -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <span class="resumen-pill" style="background:#e8f5e9;color:#2e7d32;">
        <i class="bi bi-circle-fill" style="font-size:8px;"></i> <?= $resumen['libre'] ?> libres
    </span>
    <span class="resumen-pill" style="background:#ffebee;color:#c62828;">
        <i class="bi bi-circle-fill" style="font-size:8px;"></i> <?= $resumen['ocupada'] ?> ocupadas
    </span>
    <span class="resumen-pill" style="background:#fff3e0;color:#e65100;">
        <i class="bi bi-circle-fill" style="font-size:8px;"></i> <?= $resumen['reservada'] ?> reservadas
    </span>
    <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
    </button>
</div>

<?php
$zonas = [];
foreach ($mesas as $m) $zonas[$m['zona']][] = $m;
foreach ($zonas as $zona => $lista):
?>
<div class="zona-label"><?= $zonas_nombres[$zona] ?? $zona ?></div>
<div class="row g-2 mb-3">
    <?php foreach ($lista as $m):
        $href = $m['estado'] === 'mantenimiento' ? '#' : APP_URL . '/mesas/abrir/' . $m['id'];
    ?>
    <div class="col-6 col-sm-4 col-md-3 col-xl-2">
        <a href="<?= $href ?>" class="mesa-card <?= $m['estado'] ?>">
            <div class="mesa-icon">
                <?php if ($m['estado'] === 'libre'): ?>
                    <i class="bi bi-table"></i>
                <?php elseif ($m['estado'] === 'ocupada'): ?>
                    <i class="bi bi-people-fill"></i>
                <?php elseif ($m['estado'] === 'reservada'): ?>
                    <i class="bi bi-calendar-check-fill"></i>
                <?php else: ?>
                    <i class="bi bi-tools"></i>
                <?php endif; ?>
            </div>
            <div class="mesa-num"><?= htmlspecialchars($m['nombre']) ?></div>
            <div class="mesa-info"><i class="bi bi-people"></i> <?= $m['capacidad'] ?> pers.</div>
            <?php if ($m['estado'] === 'ocupada' && $m['pedido_id']): ?>
                <div class="mesa-info" style="color:#c62828;">
                    <i class="bi bi-person"></i> <?= htmlspecialchars($m['mesero_nombre'] ?? '') ?>
                </div>
                <div class="mesa-info fw-600">S/ <?= number_format($m['total'], 2) ?></div>
            <?php endif; ?>
            <span class="estado-badge"><?= ucfirst($m['estado']) ?></span>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>