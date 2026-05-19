<?php
/** @var array  $reservas */
/** @var array  $proximas */
/** @var array  $resumen  */
/** @var array  $mesas    */
/** @var string $fecha    */

$estados = [
    'pendiente'  => ['label'=>'Pendiente',  'bg'=>'#e3f2fd','color'=>'#1565c0'],
    'confirmada' => ['label'=>'Confirmada', 'bg'=>'#e8f5e9','color'=>'#2e7d32'],
    'sentada'    => ['label'=>'Sentada',    'bg'=>'#fff3e0','color'=>'#e65100'],
    'cancelada'  => ['label'=>'Cancelada',  'bg'=>'#ffebee','color'=>'#c62828'],
    'no_show'    => ['label'=>'No show',    'bg'=>'#f3e5f5','color'=>'#6a1b9a'],
];
?>
<style>
.res-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.res-card .r-hdr { padding:.9rem 1.2rem; border-bottom:1px solid #e8e5df; font-size:14px; font-weight:600; display:flex; align-items:center; justify-content:space-between; }
.res-item { padding:.9rem 1.2rem; border-bottom:1px solid #f7f5f0; display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }
.res-item:last-child { border-bottom:none; }
.res-hora { font-size:1.1rem; font-weight:700; color:#2c1f3e; min-width:50px; }
.res-nombre { font-size:14px; font-weight:500; flex:1; }
.res-meta { font-size:12px; color:#888; }
.estado-pill { font-size:11px; padding:2px 10px; border-radius:20px; font-weight:500; }
.btn-accion { border:none; background:none; padding:4px 8px; border-radius:6px; cursor:pointer; font-size:14px; }
.btn-accion:hover { background:#f0ede7; }
.stat-mini { background:#fff; border:1px solid #e8e5df; border-radius:12px; padding:.9rem 1rem; text-align:center; }
.fecha-nav { display:flex; align-items:center; gap:.5rem; }
.fecha-nav input { border:1.5px solid #e0ddd6; border-radius:8px; padding:.35rem .75rem; font-size:13px; }
.sin-reservas { text-align:center; padding:2.5rem; color:#ccc; }
</style>

<!-- Stats hoy -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.5rem;font-weight:700;color:#2c1f3e;"><?= $resumen['total'] ?? 0 ?></div>
            <div style="font-size:12px;color:#888;">Reservas hoy</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.5rem;font-weight:700;color:#1565c0;"><?= $resumen['confirmadas'] ?? 0 ?></div>
            <div style="font-size:12px;color:#888;">Confirmadas</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.5rem;font-weight:700;color:#e65100;"><?= $resumen['sentadas'] ?? 0 ?></div>
            <div style="font-size:12px;color:#888;">En mesa</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.5rem;font-weight:700;color:#c62828;"><?= $resumen['canceladas'] ?? 0 ?></div>
            <div style="font-size:12px;color:#888;">Canceladas</div>
        </div>
    </div>
</div>

<!-- Navegación de fecha + botón nueva -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="fecha-nav">
        <a href="<?= APP_URL ?>/reservas?fecha=<?= date('Y-m-d', strtotime($fecha . ' -1 day')) ?>"
           class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
        <form method="GET" action="<?= APP_URL ?>/reservas" class="d-flex gap-2">
            <input type="date" name="fecha" value="<?= $fecha ?>" onchange="this.form.submit()">
        </form>
        <a href="<?= APP_URL ?>/reservas?fecha=<?= date('Y-m-d', strtotime($fecha . ' +1 day')) ?>"
           class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
        <a href="<?= APP_URL ?>/reservas?fecha=<?= date('Y-m-d') ?>"
           class="btn btn-sm btn-outline-secondary">Hoy</a>
    </div>
    <a href="<?= APP_URL ?>/reservas/nueva"
       class="btn btn-sm" style="background:#8e44ad;color:#fff;border-radius:8px;padding:.4rem 1rem;text-decoration:none;">
        <i class="bi bi-plus-circle me-1"></i>Nueva reserva
    </a>
</div>

<!-- Reservas del día seleccionado -->
<div class="res-card mb-4">
    <div class="r-hdr">
        <span><i class="bi bi-calendar-check me-2"></i>
            Reservas del <?= date('d/m/Y', strtotime($fecha)) ?>
            <?= $fecha === date('Y-m-d') ? '— Hoy' : '' ?>
        </span>
        <span style="font-size:13px;color:#888;"><?= count($reservas) ?> reservas</span>
    </div>

    <?php if (empty($reservas)): ?>
    <div class="sin-reservas">
        <i class="bi bi-calendar-x" style="font-size:2.5rem;"></i>
        <p class="mt-2 mb-0">Sin reservas para este día</p>
    </div>
    <?php else: ?>
    <?php foreach ($reservas as $r):
        $est = $estados[$r['estado']] ?? $estados['pendiente'];
    ?>
    <div class="res-item">
        <div class="res-hora"><?= substr($r['hora'], 0, 5) ?></div>
        <div style="flex:1;">
            <div class="res-nombre"><?= htmlspecialchars($r['nombre_cliente']) ?></div>
            <div class="res-meta">
                <i class="bi bi-people me-1"></i><?= $r['personas'] ?> personas
                <?php if ($r['mesa_nombre']): ?>
                &nbsp;·&nbsp; <i class="bi bi-table me-1"></i><?= htmlspecialchars($r['mesa_nombre']) ?>
                <?php endif; ?>
                <?php if ($r['telefono']): ?>
                &nbsp;·&nbsp; <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($r['telefono']) ?>
                <?php endif; ?>
                <?php if ($r['notas']): ?>
                <br><i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($r['notas']) ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Cambio de estado rápido -->
            <form method="POST" action="<?= APP_URL ?>/reservas/estado/<?= $r['id'] ?>" class="d-flex gap-1">
                <select name="estado" class="form-select form-select-sm" style="border-radius:8px;font-size:12px;border:1.5px solid #e0ddd6;"
                        onchange="this.form.submit()">
                    <?php foreach ($estados as $val => $info): ?>
                    <option value="<?= $val ?>" <?= $r['estado'] === $val ? 'selected' : '' ?>>
                        <?= $info['label'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <a href="<?= APP_URL ?>/reservas/editar/<?= $r['id'] ?>" class="btn-accion" title="Editar">
                <i class="bi bi-pencil text-primary"></i>
            </a>
            <button class="btn-accion" title="Eliminar"
                    onclick="confirmarEliminar(<?= $r['id'] ?>, '<?= addslashes($r['nombre_cliente']) ?>')">
                <i class="bi bi-trash text-danger"></i>
            </button>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Próximas reservas (7 días) -->
<?php if (!empty($proximas)): ?>
<div class="res-card">
    <div class="r-hdr">
        <span><i class="bi bi-calendar-week me-2"></i>Próximos 7 días</span>
        <span style="font-size:13px;color:#888;"><?= count($proximas) ?> reservas</span>
    </div>
    <?php
    $porFecha = [];
    foreach ($proximas as $r) $porFecha[$r['fecha']][] = $r;
    foreach ($porFecha as $f => $lista):
    ?>
    <div style="padding:.5rem 1.2rem;background:#faf9f6;border-bottom:1px solid #f0ede7;font-size:12px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:.05em;">
        <?= date('l d/m', strtotime($f)) ?>
        <?= $f === date('Y-m-d') ? '— Hoy' : '' ?>
    </div>
    <?php foreach ($lista as $r):
        $est = $estados[$r['estado']] ?? $estados['pendiente'];
    ?>
    <div class="res-item">
        <div class="res-hora"><?= substr($r['hora'], 0, 5) ?></div>
        <div style="flex:1;">
            <div class="res-nombre"><?= htmlspecialchars($r['nombre_cliente']) ?></div>
            <div class="res-meta">
                <i class="bi bi-people me-1"></i><?= $r['personas'] ?> pers.
                <?php if ($r['mesa_nombre']): ?>
                &nbsp;·&nbsp; <?= htmlspecialchars($r['mesa_nombre']) ?>
                <?php endif; ?>
            </div>
        </div>
        <span class="estado-pill" style="background:<?= $est['bg'] ?>;color:<?= $est['color'] ?>;">
            <?= $est['label'] ?>
        </span>
    </div>
    <?php endforeach; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                <h6 class="mt-3 mb-1">¿Eliminar reserva?</h6>
                <p class="text-muted mb-3" style="font-size:13px;" id="modalNombre"></p>
                <form method="POST" id="formEliminar">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-danger">Sí, eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function confirmarEliminar(id, nombre) {
    document.getElementById('modalNombre').textContent = nombre;
    document.getElementById('formEliminar').action = '<?= APP_URL ?>/reservas/eliminar/' + id;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
</script>