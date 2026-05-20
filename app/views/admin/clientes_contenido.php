<?php
/** @var array $clientes */
/** @var array $stats    */
?>
<style>
.cli-table { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.cli-table table { width:100%; border-collapse:collapse; }
.cli-table th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:1px solid #f0ede7; background:#faf9f6; text-align:left; }
.cli-table td { font-size:13px; padding:.75rem 1rem; border-bottom:1px solid #f7f5f0; vertical-align:middle; }
.cli-table tr:last-child td { border-bottom:none; }
.cli-table tr:hover td { background:#fdfcfa; }
.avatar-sm { width:34px; height:34px; border-radius:50%; background:#8e44ad; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; color:#fff; flex-shrink:0; }
.btn-accion { border:none; background:none; padding:4px 8px; border-radius:6px; cursor:pointer; font-size:14px; }
.btn-accion:hover { background:#f0ede7; }
.stat-mini { background:#fff; border:1px solid #e8e5df; border-radius:12px; padding:.9rem; text-align:center; }
.puntos-pill { font-size:11px; padding:2px 9px; border-radius:20px; background:#fff3e0; color:#e65100; font-weight:600; }
.frecuente-pill { font-size:10px; padding:1px 7px; border-radius:20px; background:#e8f5e9; color:#2e7d32; font-weight:600; }
.search-box { border:1.5px solid #e0ddd6; border-radius:10px; padding:.4rem .9rem; font-size:13px; width:100%; max-width:280px; }
.search-box:focus { border-color:#8e44ad; outline:none; }
</style>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.6rem;font-weight:700;color:#8e44ad;"><?= $stats['total'] ?></div>
            <div style="font-size:12px;color:#888;">Clientes registrados</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.6rem;font-weight:700;color:#2e7d32;"><?= $stats['frecuentes'] ?></div>
            <div style="font-size:12px;color:#888;">Clientes frecuentes</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.6rem;font-weight:700;color:#1565c0;">
                S/ <?= number_format($stats['total_gastado'],2) ?>
            </div>
            <div style="font-size:12px;color:#888;">Total generado</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div style="font-size:1.6rem;font-weight:700;color:#e65100;">
                S/ <?= number_format($stats['promedio_gasto'],2) ?>
            </div>
            <div style="font-size:12px;color:#888;">Gasto promedio</div>
        </div>
    </div>
</div>

<!-- Cabecera -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <input type="text" id="buscarCliente" class="search-box"
           placeholder="🔍 Buscar por nombre, teléfono..."
           oninput="buscarLocal(this.value)">
    <a href="<?= APP_URL ?>/clientes/nuevo"
       class="btn btn-sm" style="background:#8e44ad;color:#fff;border-radius:8px;padding:.4rem 1rem;text-decoration:none;">
        <i class="bi bi-person-plus me-1"></i>Nuevo cliente
    </a>
</div>

<!-- Tabla -->
<div class="cli-table">
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Contacto</th>
                <th>Visitas</th>
                <th>Total gastado</th>
                <th>Puntos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaClientes">
        <?php foreach ($clientes as $c): ?>
        <tr data-buscar="<?= strtolower(htmlspecialchars($c['nombre'].' '.$c['apellido'].' '.$c['telefono'])) ?>">
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-sm"><?= strtoupper(substr($c['nombre'],0,1)) ?></div>
                    <div>
                        <div style="font-weight:500;">
                            <?= htmlspecialchars($c['nombre'] . ' ' . ($c['apellido'] ?? '')) ?>
                            <?php if ($c['visitas'] >= 5): ?>
                            <span class="frecuente-pill ms-1">VIP</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($c['fecha_nac']): ?>
                        <div style="font-size:11px;color:#aaa;">
                            <i class="bi bi-cake me-1"></i><?= date('d/m', strtotime($c['fecha_nac'])) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td style="color:#888;">
                <?php if ($c['telefono']): ?>
                <div><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($c['telefono']) ?></div>
                <?php endif; ?>
                <?php if ($c['email']): ?>
                <div style="font-size:11px;"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($c['email']) ?></div>
                <?php endif; ?>
            </td>
            <td>
                <strong><?= $c['visitas'] ?></strong>
                <span style="font-size:11px;color:#aaa;"> visitas</span>
            </td>
            <td><strong>S/ <?= number_format($c['total_gastado'],2) ?></strong></td>
            <td><span class="puntos-pill">⭐ <?= $c['puntos'] ?> pts</span></td>
            <td>
                <div class="d-flex gap-1">
                    <a href="<?= APP_URL ?>/clientes/ver/<?= $c['id'] ?>"
                       class="btn-accion" title="Ver historial">
                        <i class="bi bi-eye text-info"></i>
                    </a>
                    <a href="<?= APP_URL ?>/clientes/editar/<?= $c['id'] ?>"
                       class="btn-accion" title="Editar">
                        <i class="bi bi-pencil text-primary"></i>
                    </a>
                    <button class="btn-accion" title="Eliminar"
                            onclick="confirmarEliminar(<?= $c['id'] ?>,'<?= addslashes($c['nombre']) ?>')">
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
                <h6 class="mt-3 mb-1">¿Eliminar cliente?</h6>
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
function buscarLocal(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#tablaClientes tr').forEach(tr => {
        tr.style.display = tr.dataset.buscar?.includes(q) ? '' : 'none';
    });
}
function confirmarEliminar(id, nombre) {
    document.getElementById('modalNombre').textContent = nombre;
    document.getElementById('formEliminar').action = '<?= APP_URL ?>/clientes/eliminar/' + id;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
</script>