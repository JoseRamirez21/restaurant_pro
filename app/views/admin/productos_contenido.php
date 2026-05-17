<?php
/** @var array $productos */
/** @var array $categorias */
?>
<style>
.filtro-btn { border:1.5px solid #e8e5df; background:#fff; border-radius:20px; padding:.3rem .9rem; font-size:13px; cursor:pointer; transition:all .2s; }
.filtro-btn.active, .filtro-btn:hover { background:#8e44ad; color:#fff; border-color:#8e44ad; }
.prod-table { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.prod-table table { width:100%; border-collapse:collapse; }
.prod-table th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:1px solid #f0ede7; text-align:left; background:#faf9f6; }
.prod-table td { font-size:13px; padding:.7rem 1rem; border-bottom:1px solid #f7f5f0; vertical-align:middle; }
.prod-table tr:last-child td { border-bottom:none; }
.prod-table tr:hover td { background:#fdfcfa; }
.cat-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:5px; }
.badge-disp { font-size:11px; padding:2px 9px; border-radius:20px; font-weight:500; }
.badge-si  { background:#e8f5e9; color:#2e7d32; }
.badge-no  { background:#ffebee; color:#c62828; }
.badge-dest{ background:#fff3e0; color:#e65100; }
.btn-accion { border:none; background:none; padding:4px 8px; border-radius:6px; cursor:pointer; font-size:14px; transition:all .15s; }
.btn-accion:hover { background:#f0ede7; }
.rentabilidad { font-size:11px; color:#888; }
.rentabilidad.alta { color:#2e7d32; }
.rentabilidad.baja { color:#c62828; }
</style>

<!-- Cabecera -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap" id="filtros">
        <button class="filtro-btn active" onclick="filtrar(0, this)">Todos</button>
        <?php foreach ($categorias as $cat): ?>
        <button class="filtro-btn" onclick="filtrar(<?= $cat['id'] ?>, this)"
                style="--cat-color:<?= $cat['color'] ?>">
            <?= htmlspecialchars($cat['nombre']) ?>
        </button>
        <?php endforeach; ?>
    </div>
    <a href="<?= APP_URL ?>/productos/nuevo" class="btn btn-sm" style="background:#8e44ad;color:#fff;border-radius:8px;padding:.4rem 1rem;text-decoration:none;">
        <i class="bi bi-plus-circle me-1"></i>Nuevo plato
    </a>
</div>

<!-- Tabla de productos -->
<div class="prod-table">
    <table>
        <thead>
            <tr>
                <th>Plato</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Costo</th>
                <th>Margen</th>
                <th>Prep.</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaBody">
        <?php foreach ($productos as $p):
            $margen = $p['precio'] > 0 ? (($p['precio'] - $p['costo']) / $p['precio']) * 100 : 0;
            $claseMargen = $margen >= 60 ? 'alta' : ($margen < 30 ? 'baja' : '');
        ?>
        <tr data-cat="<?= $p['categoria_id'] ?>">
            <td>
                <div style="font-weight:500;"><?= htmlspecialchars($p['nombre']) ?></div>
                <?php if ($p['descripcion']): ?>
                <div style="font-size:11px;color:#aaa;"><?= htmlspecialchars(mb_substr($p['descripcion'],0,50)) ?>...</div>
                <?php endif; ?>
                <?php if ($p['destacado']): ?>
                <span class="badge-disp badge-dest">⭐ Destacado</span>
                <?php endif; ?>
            </td>
            <td>
                <span class="cat-dot" style="background:<?= $p['categoria_color'] ?>"></span>
                <?= htmlspecialchars($p['categoria_nombre']) ?>
            </td>
            <td><strong>S/ <?= number_format($p['precio'], 2) ?></strong></td>
            <td style="color:#888;">S/ <?= number_format($p['costo'], 2) ?></td>
            <td>
                <span class="rentabilidad <?= $claseMargen ?>">
                    <?= number_format($margen, 0) ?>%
                </span>
            </td>
            <td style="color:#888;"><?= $p['tiempo_prep_min'] ?> min</td>
            <td>
                <span class="badge-disp <?= $p['disponible'] ? 'badge-si' : 'badge-no' ?>">
                    <?= $p['disponible'] ? 'Disponible' : 'No disp.' ?>
                </span>
            </td>
            <td>
                <div class="d-flex gap-1">
                    <!-- Editar -->
                    <a href="<?= APP_URL ?>/productos/editar/<?= $p['id'] ?>"
                       class="btn-accion" title="Editar">
                        <i class="bi bi-pencil text-primary"></i>
                    </a>
                    <!-- Toggle disponible -->
                    <a href="<?= APP_URL ?>/productos/toggle/<?= $p['id'] ?>"
                       class="btn-accion" title="<?= $p['disponible'] ? 'Desactivar' : 'Activar' ?>">
                        <i class="bi bi-<?= $p['disponible'] ? 'eye-slash text-warning' : 'eye text-success' ?>"></i>
                    </a>
                    <!-- Eliminar -->
                    <button class="btn-accion" title="Eliminar"
                            onclick="confirmarEliminar(<?= $p['id'] ?>, '<?= addslashes($p['nombre']) ?>')">
                        <i class="bi bi-trash text-danger"></i>
                    </button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal confirmación eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                <h6 class="mt-3 mb-1">¿Eliminar plato?</h6>
                <p class="text-muted mb-3" style="font-size:13px;" id="modalNombreProducto"></p>
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
function filtrar(catId, btn) {
    document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('#tablaBody tr').forEach(tr => {
        tr.style.display = (catId === 0 || parseInt(tr.dataset.cat) === catId) ? '' : 'none';
    });
}

function confirmarEliminar(id, nombre) {
    document.getElementById('modalNombreProducto').textContent = nombre;
    document.getElementById('formEliminar').action = '<?= APP_URL ?>/productos/eliminar/' + id;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
</script>