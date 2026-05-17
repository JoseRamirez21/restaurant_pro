<?php
/** @var array|null $producto */
/** @var array $categorias */
$es_editar = !is_null($producto);
$action    = $es_editar
    ? APP_URL . '/productos/editar/' . $producto['id']
    : APP_URL . '/productos/crear';
?>
<style>
.form-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:680px; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control, .form-select { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus, .form-select:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.form-check-input:checked { background-color:#8e44ad; border-color:#8e44ad; }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; }
.btn-guardar:hover { background:#7d3c98; color:#fff; }
.margen-preview { background:#f4f1eb; border-radius:10px; padding:.75rem 1rem; font-size:13px; }
.seccion-titulo { font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .6rem; border-bottom:1px solid #f0ede7; padding-bottom:.4rem; }
</style>

<div class="form-card">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/productos" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="mb-0 fw-600"><?= $es_editar ? 'Editar plato' : 'Nuevo plato' ?></h5>
            <?php if ($es_editar): ?>
            <div style="font-size:12px;color:#aaa;"><?= htmlspecialchars($producto['nombre']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST" action="<?= $action ?>">

        <div class="seccion-titulo">Información básica</div>

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label">Nombre del plato *</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>"
                       placeholder="Ej: Lomo saltado" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Categoría *</label>
                <select name="categoria_id" class="form-select" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= isset($producto['categoria_id']) && $producto['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="2"
                      placeholder="Describe el plato brevemente..."><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="seccion-titulo">Precios</div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Precio de venta (S/) *</label>
                <input type="number" name="precio" class="form-control"
                       step="0.50" min="0"
                       value="<?= $producto['precio'] ?? '' ?>"
                       placeholder="0.00"
                       oninput="calcMargen()" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Costo (S/)</label>
                <input type="number" name="costo" class="form-control"
                       step="0.50" min="0"
                       value="<?= $producto['costo'] ?? '0' ?>"
                       placeholder="0.00"
                       oninput="calcMargen()">
            </div>
            <div class="col-md-4">
                <label class="form-label">Margen estimado</label>
                <div class="margen-preview" id="margenPreview">
                    — ingresa precio y costo
                </div>
            </div>
        </div>

        <div class="seccion-titulo">Detalles operativos</div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Tiempo de preparación (min)</label>
                <input type="number" name="tiempo_prep_min" class="form-control"
                       min="1" max="120"
                       value="<?= $producto['tiempo_prep_min'] ?? '10' ?>">
            </div>
            <div class="col-md-8">
                <label class="form-label">Alérgenos</label>
                <input type="text" name="alergenos" class="form-control"
                       value="<?= htmlspecialchars($producto['alergenos'] ?? '') ?>"
                       placeholder="Ej: gluten, mariscos, nueces">
            </div>
        </div>

        <div class="seccion-titulo">Visibilidad</div>

        <div class="d-flex gap-4 mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       name="disponible" id="disponible" value="1"
                       <?= (!$es_editar || $producto['disponible']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="disponible" style="font-size:13px;">
                    <i class="bi bi-eye me-1 text-success"></i>Disponible en carta
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       name="destacado" id="destacado" value="1"
                       <?= ($es_editar && $producto['destacado']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="destacado" style="font-size:13px;">
                    <i class="bi bi-star me-1 text-warning"></i>Plato destacado
                </label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-guardar btn">
                <i class="bi bi-check-circle me-2"></i><?= $es_editar ? 'Guardar cambios' : 'Crear plato' ?>
            </button>
            <a href="<?= APP_URL ?>/productos" class="btn btn-outline-secondary">
                Cancelar
            </a>
        </div>

    </form>
</div>

<script>
function calcMargen() {
    const precio = parseFloat(document.querySelector('[name=precio]').value)  || 0;
    const costo  = parseFloat(document.querySelector('[name=costo]').value)   || 0;
    const el     = document.getElementById('margenPreview');
    if (precio <= 0) { el.textContent = '— ingresa precio y costo'; el.style.color=''; return; }
    const margen   = ((precio - costo) / precio) * 100;
    const ganancia = precio - costo;
    el.innerHTML = `<strong>S/ ${ganancia.toFixed(2)}</strong> por plato &nbsp;·&nbsp; <strong>${margen.toFixed(0)}%</strong>`;
    el.style.color = margen >= 60 ? '#2e7d32' : margen < 30 ? '#c62828' : '#e65100';
}
// Calcular al cargar si es edición
calcMargen();
</script>