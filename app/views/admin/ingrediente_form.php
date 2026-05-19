<?php
/** @var array|null $ingrediente */
$es_editar = !is_null($ingrediente);
$action    = $es_editar
    ? APP_URL . '/inventario/editar/' . $ingrediente['id']
    : APP_URL . '/inventario/crear';
$unidades  = ['kg','g','l','ml','unidad','docena','caja','bolsa'];
?>
<style>
.form-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:580px; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control, .form-select { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus, .form-select:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; }
.btn-guardar:hover { background:#7d3c98; color:#fff; }
.seccion-titulo { font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .6rem; border-bottom:1px solid #f0ede7; padding-bottom:.4rem; }
.valor-total { background:#f4f1eb; border-radius:10px; padding:.75rem 1rem; font-size:13px; }
</style>

<div class="form-card">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/inventario" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-600">
            <?= $es_editar ? 'Editar — ' . htmlspecialchars($ingrediente['nombre']) : 'Nuevo ingrediente' ?>
        </h5>
    </div>

    <form method="POST" action="<?= $action ?>">

        <div class="seccion-titulo">Información básica</div>

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label">Nombre del ingrediente *</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($ingrediente['nombre'] ?? '') ?>"
                       placeholder="Ej: Lomo de res" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Unidad de medida *</label>
                <select name="unidad" class="form-select" required>
                    <?php foreach ($unidades as $u): ?>
                    <option value="<?= $u ?>"
                        <?= ($ingrediente['unidad'] ?? 'kg') === $u ? 'selected' : '' ?>>
                        <?= strtoupper($u) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Proveedor</label>
            <input type="text" name="proveedor" class="form-control"
                   value="<?= htmlspecialchars($ingrediente['proveedor'] ?? '') ?>"
                   placeholder="Ej: Mercado Central">
        </div>

        <div class="seccion-titulo">Stock</div>

        <div class="row g-3 mb-3">
            <?php if (!$es_editar): ?>
            <div class="col-md-4">
                <label class="form-label">Stock inicial</label>
                <input type="number" name="stock_actual" class="form-control"
                       step="0.01" min="0" value="0" oninput="calcTotal()">
            </div>
            <?php endif; ?>
            <div class="col-md-4">
                <label class="form-label">Stock mínimo (alerta)</label>
                <input type="number" name="stock_minimo" class="form-control"
                       step="0.01" min="0"
                       value="<?= $ingrediente['stock_minimo'] ?? 0 ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Stock máximo</label>
                <input type="number" name="stock_maximo" class="form-control"
                       step="0.01" min="0"
                       value="<?= $ingrediente['stock_maximo'] ?? 0 ?>">
            </div>
        </div>

        <div class="seccion-titulo">Costo</div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Costo por unidad (S/)</label>
                <input type="number" name="costo_unitario" class="form-control"
                       step="0.01" min="0"
                       value="<?= $ingrediente['costo_unitario'] ?? 0 ?>"
                       oninput="calcTotal()">
            </div>
            <?php if (!$es_editar): ?>
            <div class="col-md-6">
                <label class="form-label">Valor total del stock</label>
                <div class="valor-total">
                    S/ <span id="valorTotal">0.00</span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-guardar btn">
                <i class="bi bi-check-circle me-2"></i><?= $es_editar ? 'Guardar cambios' : 'Crear ingrediente' ?>
            </button>
            <a href="<?= APP_URL ?>/inventario" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>

<script>
function calcTotal() {
    const stock = parseFloat(document.querySelector('[name=stock_actual]')?.value) || 0;
    const costo = parseFloat(document.querySelector('[name=costo_unitario]')?.value) || 0;
    const el    = document.getElementById('valorTotal');
    if (el) el.textContent = (stock * costo).toFixed(2);
}
</script>