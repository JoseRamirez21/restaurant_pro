<?php
/** @var array $ingredientes */
/** @var array $alertas     */
$unidades = ['kg'=>'kg','g'=>'g','l'=>'L','ml'=>'ml','unidad'=>'Unid.','docena'=>'Doc.','caja'=>'Caja','bolsa'=>'Bolsa'];
?>
<style>
.inv-table { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; }
.inv-table table { width:100%; border-collapse:collapse; }
.inv-table th { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:1px solid #f0ede7; background:#faf9f6; text-align:left; }
.inv-table td { font-size:13px; padding:.75rem 1rem; border-bottom:1px solid #f7f5f0; vertical-align:middle; }
.inv-table tr:last-child td { border-bottom:none; }
.inv-table tr:hover td { background:#fdfcfa; }
.stock-bar { height:6px; border-radius:3px; background:#e8e5df; overflow:hidden; margin-top:4px; }
.stock-bar-fill { height:100%; border-radius:3px; transition:width .3s; }
.badge-ok      { background:#e8f5e9; color:#2e7d32; font-size:11px; padding:2px 9px; border-radius:20px; }
.badge-bajo    { background:#fff3e0; color:#e65100; font-size:11px; padding:2px 9px; border-radius:20px; }
.badge-critico { background:#ffebee; color:#c62828; font-size:11px; padding:2px 9px; border-radius:20px; }
.btn-accion { border:none; background:none; padding:4px 8px; border-radius:6px; cursor:pointer; font-size:14px; }
.btn-accion:hover { background:#f0ede7; }
.alerta-card { background:#fff8f0; border:1px solid #fd7e14; border-radius:12px; padding:.9rem 1.2rem; }
</style>

<!-- Alertas de stock -->
<?php if (!empty($alertas)): ?>
<div class="alerta-card mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:18px;"></i>
        <strong style="font-size:14px;">Stock bajo — <?= count($alertas) ?> ingrediente(s) requieren atención</strong>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($alertas as $a): ?>
        <span style="background:#fff;border:1px solid #fd7e14;border-radius:8px;padding:3px 10px;font-size:12px;">
            <?= htmlspecialchars($a['nombre']) ?>
            <strong style="color:#e65100;"><?= number_format($a['stock_actual'],1) ?> <?= $unidades[$a['unidad']] ?? $a['unidad'] ?></strong>
        </span>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Cabecera -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <span style="font-size:13px;color:#888;"><?= count($ingredientes) ?> ingredientes registrados</span>
    <a href="<?= APP_URL ?>/inventario/nuevo"
       class="btn btn-sm" style="background:#8e44ad;color:#fff;border-radius:8px;padding:.4rem 1rem;text-decoration:none;">
        <i class="bi bi-plus-circle me-1"></i>Nuevo ingrediente
    </a>
</div>

<!-- Tabla -->
<div class="inv-table">
    <table>
        <thead>
            <tr>
                <th>Ingrediente</th>
                <th>Unidad</th>
                <th>Stock actual</th>
                <th>Mín / Máx</th>
                <th>Costo unit.</th>
                <th>Proveedor</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ingredientes as $ing):
            $pct = $ing['stock_maximo'] > 0
                ? min(100, ($ing['stock_actual'] / $ing['stock_maximo']) * 100)
                : 0;
            $barColor = $ing['estado_stock'] === 'critico' ? '#dc3545'
                      : ($ing['estado_stock'] === 'bajo' ? '#fd7e14' : '#28a745');
        ?>
        <tr>
            <td><strong><?= htmlspecialchars($ing['nombre']) ?></strong></td>
            <td><?= $unidades[$ing['unidad']] ?? $ing['unidad'] ?></td>
            <td>
                <div style="font-weight:600;"><?= number_format($ing['stock_actual'],2) ?></div>
                <div class="stock-bar">
                    <div class="stock-bar-fill" style="width:<?= $pct ?>%;background:<?= $barColor ?>;"></div>
                </div>
            </td>
            <td style="color:#888;font-size:12px;">
                <?= number_format($ing['stock_minimo'],2) ?> / <?= number_format($ing['stock_maximo'],2) ?>
            </td>
            <td>S/ <?= number_format($ing['costo_unitario'],2) ?></td>
            <td style="color:#888;font-size:12px;"><?= htmlspecialchars($ing['proveedor'] ?? '—') ?></td>
            <td>
                <?php if ($ing['estado_stock'] === 'critico'): ?>
                <span class="badge-critico">⚠ Crítico</span>
                <?php elseif ($ing['estado_stock'] === 'bajo'): ?>
                <span class="badge-bajo">↓ Bajo</span>
                <?php else: ?>
                <span class="badge-ok">✓ OK</span>
                <?php endif; ?>
            </td>
            <td>
                <div class="d-flex gap-1">
                    <a href="<?= APP_URL ?>/inventario/ajustar/<?= $ing['id'] ?>"
                       class="btn-accion" title="Ajustar stock">
                        <i class="bi bi-arrow-left-right text-success"></i>
                    </a>
                    <a href="<?= APP_URL ?>/inventario/editar/<?= $ing['id'] ?>"
                       class="btn-accion" title="Editar">
                        <i class="bi bi-pencil text-primary"></i>
                    </a>
                    <button class="btn-accion" title="Eliminar"
                            onclick="confirmarEliminar(<?= $ing['id'] ?>, '<?= addslashes($ing['nombre']) ?>')">
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
                <h6 class="mt-3 mb-1">¿Eliminar ingrediente?</h6>
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
    document.getElementById('formEliminar').action = '<?= APP_URL ?>/inventario/eliminar/' + id;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
</script>