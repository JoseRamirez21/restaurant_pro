<?php /** @var array $categorias */ ?>
<style>
.cat-table { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.cat-table table { width:100%; border-collapse:collapse; }
.cat-table th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:1px solid #f0ede7; background:#faf9f6; text-align:left; }
.cat-table td { font-size:13px; padding:.75rem 1rem; border-bottom:1px solid #f7f5f0; vertical-align:middle; }
.cat-table tr:last-child td { border-bottom:none; }
.cat-table tr:hover td { background:#fdfcfa; }
.cat-dot { width:12px; height:12px; border-radius:50%; display:inline-block; flex-shrink:0; }
.btn-accion { border:none; background:none; padding:4px 8px; border-radius:6px; cursor:pointer; font-size:14px; }
.btn-accion:hover { background:#f0ede7; }
.activo-pill   { background:#e8f5e9; color:#2e7d32; font-size:11px; padding:2px 9px; border-radius:20px; }
.inactivo-pill { background:#ffebee; color:#c62828; font-size:11px; padding:2px 9px; border-radius:20px; }
</style>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <span style="font-size:13px;color:#888;"><?= count($categorias) ?> categorías activas</span>
    <a href="<?= APP_URL ?>/categorias/nueva"
       class="btn btn-sm" style="background:#8e44ad;color:#fff;border-radius:8px;padding:.4rem 1rem;text-decoration:none;">
        <i class="bi bi-plus-circle me-1"></i>Nueva categoría
    </a>
</div>

<div class="cat-table">
    <table>
        <thead>
            <tr>
                <th>Orden</th>
                <th>Categoría</th>
                <th>Ícono</th>
                <th>Productos</th>
                <th>Activos</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($categorias as $c): ?>
        <tr>
            <td style="color:#aaa;"><?= $c['orden'] ?></td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <span class="cat-dot" style="background:<?= htmlspecialchars($c['color']) ?>"></span>
                    <strong><?= htmlspecialchars($c['nombre']) ?></strong>
                </div>
                <?php if ($c['descripcion']): ?>
                <div style="font-size:11px;color:#aaa;"><?= htmlspecialchars($c['descripcion']) ?></div>
                <?php endif; ?>
            </td>
            <td>
                <i class="bi <?= htmlspecialchars($c['icono']) ?>" style="font-size:18px;color:<?= htmlspecialchars($c['color']) ?>"></i>
                <span style="font-size:11px;color:#aaa;margin-left:4px;"><?= htmlspecialchars($c['icono']) ?></span>
            </td>
            <td><?= $c['total_productos'] ?> platos</td>
            <td><?= $c['productos_activos'] ?> activos</td>
            <td>
                <span class="<?= $c['activo'] ? 'activo-pill' : 'inactivo-pill' ?>">
                    <?= $c['activo'] ? '● Activa' : '● Inactiva' ?>
                </span>
            </td>
            <td>
                <div class="d-flex gap-1">
                    <a href="<?= APP_URL ?>/categorias/editar/<?= $c['id'] ?>"
                       class="btn-accion" title="Editar">
                        <i class="bi bi-pencil text-primary"></i>
                    </a>
                    <a href="<?= APP_URL ?>/categorias/toggle/<?= $c['id'] ?>"
                       class="btn-accion" title="<?= $c['activo'] ? 'Desactivar' : 'Activar' ?>">
                        <i class="bi bi-<?= $c['activo'] ? 'eye-slash text-warning' : 'eye text-success' ?>"></i>
                    </a>
                    <?php if ($c['total_productos'] == 0): ?>
                    <button class="btn-accion" title="Eliminar"
                            onclick="confirmarEliminar(<?= $c['id'] ?>, '<?= addslashes($c['nombre']) ?>')">
                        <i class="bi bi-trash text-danger"></i>
                    </button>
                    <?php else: ?>
                    <span style="font-size:11px;color:#ccc;padding:4px 8px;" title="Tiene productos">
                        <i class="bi bi-trash text-muted"></i>
                    </span>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                <h6 class="mt-3 mb-1">¿Eliminar categoría?</h6>
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
    document.getElementById('formEliminar').action = '<?= APP_URL ?>/categorias/eliminar/' + id;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
</script>