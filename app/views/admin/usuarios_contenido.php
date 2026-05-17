<?php
/** @var array $usuarios */
$roles = [
    'administrador' => ['label'=>'Administrador', 'color'=>'#8e44ad', 'bg'=>'#f3e5f5'],
    'supervisor'    => ['label'=>'Supervisor',    'color'=>'#1565c0', 'bg'=>'#e3f2fd'],
    'mesero'        => ['label'=>'Mesero',        'color'=>'#2e7d32', 'bg'=>'#e8f5e9'],
    'cocinero'      => ['label'=>'Cocinero',      'color'=>'#e65100', 'bg'=>'#fff3e0'],
    'cajero'        => ['label'=>'Cajero',        'color'=>'#c62828', 'bg'=>'#ffebee'],
];
?>
<style>
.usr-table { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.usr-table table { width:100%; border-collapse:collapse; }
.usr-table th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:1px solid #f0ede7; background:#faf9f6; text-align:left; }
.usr-table td { font-size:13px; padding:.75rem 1rem; border-bottom:1px solid #f7f5f0; vertical-align:middle; }
.usr-table tr:last-child td { border-bottom:none; }
.usr-table tr:hover td { background:#fdfcfa; }
.avatar { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; flex-shrink:0; }
.rol-pill { font-size:11px; padding:2px 10px; border-radius:20px; font-weight:500; }
.activo-pill   { background:#e8f5e9; color:#2e7d32; font-size:11px; padding:2px 9px; border-radius:20px; }
.inactivo-pill { background:#ffebee; color:#c62828; font-size:11px; padding:2px 9px; border-radius:20px; }
.btn-accion { border:none; background:none; padding:4px 8px; border-radius:6px; cursor:pointer; font-size:14px; }
.btn-accion:hover { background:#f0ede7; }
.yo-badge { font-size:10px; background:#e3f2fd; color:#1565c0; padding:1px 7px; border-radius:20px; margin-left:5px; }
</style>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <span style="font-size:13px;color:#888;"><?= count($usuarios) ?> usuarios registrados</span>
    <a href="<?= APP_URL ?>/usuarios/nuevo"
       class="btn btn-sm" style="background:#8e44ad;color:#fff;border-radius:8px;padding:.4rem 1rem;text-decoration:none;">
        <i class="bi bi-person-plus me-1"></i>Nuevo usuario
    </a>
</div>

<div class="usr-table">
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Pedidos</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u):
            $rol   = $roles[$u['rol']] ?? ['label'=>ucfirst($u['rol']),'color'=>'#888','bg'=>'#f0f0f0'];
            $esYo  = (int)$u['id'] === (int)$_SESSION['usuario_id'];
        ?>
        <tr>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar" style="background:<?= $rol['bg'] ?>;color:<?= $rol['color'] ?>;">
                        <?= strtoupper(substr($u['nombre'],0,1)) ?>
                    </div>
                    <div>
                        <div style="font-weight:500;">
                            <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>
                            <?php if ($esYo): ?>
                            <span class="yo-badge">Tú</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </td>
            <td style="color:#888;"><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <span class="rol-pill" style="background:<?= $rol['bg'] ?>;color:<?= $rol['color'] ?>;">
                    <?= $rol['label'] ?>
                </span>
            </td>
            <td style="color:#888;"><?= $u['total_pedidos'] ?></td>
            <td>
                <span class="<?= $u['activo'] ? 'activo-pill' : 'inactivo-pill' ?>">
                    <?= $u['activo'] ? '● Activo' : '● Inactivo' ?>
                </span>
            </td>
            <td>
                <div class="d-flex gap-1">
                    <a href="<?= APP_URL ?>/usuarios/editar/<?= $u['id'] ?>"
                       class="btn-accion" title="Editar">
                        <i class="bi bi-pencil text-primary"></i>
                    </a>
                    <?php if (!$esYo): ?>
                    <a href="<?= APP_URL ?>/usuarios/toggle/<?= $u['id'] ?>"
                       class="btn-accion" title="<?= $u['activo'] ? 'Desactivar' : 'Activar' ?>">
                        <i class="bi bi-<?= $u['activo'] ? 'person-dash text-warning' : 'person-check text-success' ?>"></i>
                    </a>
                    <button class="btn-accion" title="Eliminar"
                            onclick="confirmarEliminar(<?= $u['id'] ?>, '<?= addslashes($u['nombre'] . ' ' . $u['apellido']) ?>')">
                        <i class="bi bi-trash text-danger"></i>
                    </button>
                    <?php else: ?>
                    <span style="font-size:11px;color:#ccc;padding:4px 8px;">No editable</span>
                    <?php endif; ?>
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
                <h6 class="mt-3 mb-1">¿Eliminar usuario?</h6>
                <p class="text-muted mb-1" style="font-size:13px;" id="modalNombre"></p>
                <p class="text-muted mb-3" style="font-size:11px;">Esta acción no se puede deshacer.</p>
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
    document.getElementById('formEliminar').action = '<?= APP_URL ?>/usuarios/eliminar/' + id;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
</script>