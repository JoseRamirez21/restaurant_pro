<?php
/** @var array $mesas */
$zonas = [
    'salon_principal' => 'Salón principal',
    'terraza'         => 'Terraza',
    'privado'         => 'Sala privada',
    'bar'             => 'Bar',
];
$estados = [
    'libre'         => ['label'=>'Libre',         'class'=>'success'],
    'ocupada'       => ['label'=>'Ocupada',        'class'=>'danger'],
    'reservada'     => ['label'=>'Reservada',      'class'=>'warning'],
    'mantenimiento' => ['label'=>'Mantenimiento',  'class'=>'secondary'],
];
?>
<style>
.mesas-table { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.mesas-table table { width:100%; border-collapse:collapse; }
.mesas-table th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:1px solid #f0ede7; background:#faf9f6; text-align:left; }
.mesas-table td { font-size:13px; padding:.75rem 1rem; border-bottom:1px solid #f7f5f0; vertical-align:middle; }
.mesas-table tr:last-child td { border-bottom:none; }
.mesas-table tr:hover td { background:#fdfcfa; }
.btn-accion { border:none; background:none; padding:4px 8px; border-radius:6px; cursor:pointer; font-size:14px; }
.btn-accion:hover { background:#f0ede7; }
.zona-pill { font-size:11px; padding:2px 9px; border-radius:20px; background:#f0ede7; color:#555; font-weight:500; }
.estado-sel { font-size:12px; border:1.5px solid #e0ddd6; border-radius:8px; padding:3px 8px; cursor:pointer; }
</style>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <span style="font-size:13px;color:#888;"><?= count($mesas) ?> mesas activas</span>
    </div>
    <a href="<?= APP_URL ?>/mesas/nueva"
       class="btn btn-sm" style="background:#8e44ad;color:#fff;border-radius:8px;padding:.4rem 1rem;text-decoration:none;">
        <i class="bi bi-plus-circle me-1"></i>Nueva mesa
    </a>
</div>

<div class="mesas-table">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Zona</th>
                <th>Capacidad</th>
                <th>Estado</th>
                <th>Pedidos históricos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($mesas as $m): ?>
        <tr>
            <td><strong><?= $m['numero'] ?></strong></td>
            <td><?= htmlspecialchars($m['nombre']) ?></td>
            <td><span class="zona-pill"><?= $zonas[$m['zona']] ?? $m['zona'] ?></span></td>
            <td><i class="bi bi-people me-1 text-muted"></i><?= $m['capacidad'] ?> pers.</td>
            <td>
                <!-- Cambiar estado rápido -->
                <form method="POST" action="<?= APP_URL ?>/mesas/estado/<?= $m['id'] ?>">
                    <select name="estado" class="estado-sel" onchange="this.form.submit()">
                        <?php foreach ($estados as $val => $info): ?>
                        <option value="<?= $val ?>" <?= $m['estado'] === $val ? 'selected' : '' ?>>
                            <?= $info['label'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td style="color:#888;"><?= $m['pedidos_totales'] ?> pedidos</td>
            <td>
                <div class="d-flex gap-1">
                    <a href="<?= APP_URL ?>/mesas/editar/<?= $m['id'] ?>"
                       class="btn-accion" title="Editar">
                        <i class="bi bi-pencil text-primary"></i>
                    </a>
                    <button class="btn-accion" title="Eliminar"
                            onclick="confirmarEliminar(<?= $m['id'] ?>, '<?= addslashes($m['nombre']) ?>')">
                        <i class="bi bi-trash text-danger"></i>
                    </button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                <h6 class="mt-3 mb-1">¿Eliminar mesa?</h6>
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
    document.getElementById('formEliminar').action = '<?= APP_URL ?>/mesas/eliminar/' + id;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
</script>