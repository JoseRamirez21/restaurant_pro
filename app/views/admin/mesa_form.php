<?php
/** @var array|null $mesa */
$es_editar = !is_null($mesa);
$action    = $es_editar
    ? APP_URL . '/mesas/editar/' . $mesa['id']
    : APP_URL . '/mesas/guardar';
$zonas = [
    'salon_principal' => 'Salón principal',
    'terraza'         => 'Terraza',
    'privado'         => 'Sala privada',
    'bar'             => 'Bar',
];
?>
<style>
.form-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:520px; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control, .form-select { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus, .form-select:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; }
.btn-guardar:hover { background:#7d3c98; color:#fff; }
</style>

<div class="form-card">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/mesas/gestionar" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-600"><?= $es_editar ? 'Editar — ' . htmlspecialchars($mesa['nombre']) : 'Nueva mesa' ?></h5>
    </div>

    <form method="POST" action="<?= $action ?>">

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Número de mesa *</label>
                <input type="number" name="numero" class="form-control"
                       min="1" value="<?= $mesa['numero'] ?? '' ?>"
                       placeholder="Ej: 11" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($mesa['nombre'] ?? '') ?>"
                       placeholder="Ej: Mesa 11">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Capacidad (personas) *</label>
                <input type="number" name="capacidad" class="form-control"
                       min="1" max="30"
                       value="<?= $mesa['capacidad'] ?? 4 ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Zona *</label>
                <select name="zona" class="form-select" required>
                    <?php foreach ($zonas as $val => $label): ?>
                    <option value="<?= $val ?>"
                        <?= isset($mesa['zona']) && $mesa['zona'] === $val ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-guardar btn">
                <i class="bi bi-check-circle me-2"></i><?= $es_editar ? 'Guardar cambios' : 'Crear mesa' ?>
            </button>
            <a href="<?= APP_URL ?>/mesas/gestionar" class="btn btn-outline-secondary">
                Cancelar
            </a>
        </div>

    </form>
</div>