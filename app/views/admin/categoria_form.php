<?php
/** @var array|null $categoria */
/** @var string $error */
$es_editar = !is_null($categoria);
$action    = $es_editar
    ? APP_URL . '/categorias/editar/' . $categoria['id']
    : APP_URL . '/categorias/crear';

$iconos = [
    'bi-egg-fried'      => 'Entradas',
    'bi-cup-hot'        => 'Sopas',
    'bi-bag'            => 'Fondos',
    'bi-fire'           => 'Parrillas',
    'bi-cake2'          => 'Postres',
    'bi-cup-straw'      => 'Bebidas',
    'bi-stars'          => 'Cócteles',
    'bi-calendar-check' => 'Menú del día',
    'bi-grid'           => 'General',
    'bi-basket'         => 'Canasta',
    'bi-fish'           => 'Mariscos',
    'bi-droplet'        => 'Jugos',
    'bi-flower1'        => 'Especiales',
    'bi-heart'          => 'Favoritos',
];
?>
<style>
.form-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:560px; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control, .form-select { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus, .form-select:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; }
.btn-guardar:hover { background:#7d3c98; color:#fff; }
.seccion-titulo { font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .6rem; border-bottom:1px solid #f0ede7; padding-bottom:.4rem; }
.icono-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(70px,1fr)); gap:8px; }
.icono-card { border:2px solid #e8e5df; border-radius:10px; padding:.6rem .4rem; text-align:center; cursor:pointer; transition:all .15s; }
.icono-card:hover { border-color:#8e44ad; }
.icono-card.selected { border-color:#8e44ad; background:#f9f5fd; }
.icono-card input { display:none; }
.icono-card i { font-size:1.4rem; display:block; margin-bottom:3px; }
.icono-card span { font-size:10px; color:#888; }
.preview-box { background:#f4f1eb; border-radius:12px; padding:1rem; display:flex; align-items:center; gap:.75rem; }
.preview-dot { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; transition:all .2s; }
</style>

<div class="form-card">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/categorias" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-600">
            <?= $es_editar ? 'Editar — ' . htmlspecialchars($categoria['nombre']) : 'Nueva categoría' ?>
        </h5>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 mb-3" style="font-size:13px;border-radius:10px;">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $action ?>">

        <div class="seccion-titulo">Información</div>

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label">Nombre de la categoría *</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($categoria['nombre'] ?? '') ?>"
                       placeholder="Ej: Entradas" required
                       oninput="actualizarPreview()">
            </div>
            <div class="col-md-4">
                <label class="form-label">Orden en carta</label>
                <input type="number" name="orden" class="form-control"
                       min="0" value="<?= $categoria['orden'] ?? 0 ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <input type="text" name="descripcion" class="form-control"
                   value="<?= htmlspecialchars($categoria['descripcion'] ?? '') ?>"
                   placeholder="Descripción breve (opcional)">
        </div>

        <div class="seccion-titulo">Color e ícono</div>

        <!-- Preview en tiempo real -->
        <div class="preview-box mb-3">
            <div class="preview-dot" id="previewDot"
                 style="background:<?= $categoria['color'] ?? '#e8e5df' ?>20;color:<?= $categoria['color'] ?? '#6c757d' ?>">
                <i class="bi <?= $categoria['icono'] ?? 'bi-grid' ?>" id="previewIcono"></i>
            </div>
            <div>
                <div style="font-size:14px;font-weight:500;" id="previewNombre">
                    <?= htmlspecialchars($categoria['nombre'] ?? 'Nueva categoría') ?>
                </div>
                <div style="font-size:11px;color:#aaa;">Vista previa en el menú</div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Color</label>
            <div class="d-flex align-items-center gap-3">
                <input type="color" name="color" id="colorPicker"
                       class="form-control form-control-color"
                       value="<?= $categoria['color'] ?? '#8e44ad' ?>"
                       oninput="actualizarPreview()">
                <div class="d-flex gap-2 flex-wrap">
                    <?php foreach (['#e74c3c','#e67e22','#f39c12','#27ae60','#2980b9','#8e44ad','#16a085','#c0392b'] as $color): ?>
                    <div style="width:24px;height:24px;border-radius:50%;background:<?= $color ?>;cursor:pointer;border:2px solid #fff;box-shadow:0 0 0 1px #ddd;"
                         onclick="selColor('<?= $color ?>')"></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Ícono</label>
            <input type="hidden" name="icono" id="iconoInput" value="<?= $categoria['icono'] ?? 'bi-grid' ?>">
            <div class="icono-grid">
                <?php foreach ($iconos as $ico => $lbl): ?>
                <label class="icono-card <?= ($categoria['icono'] ?? 'bi-grid') === $ico ? 'selected' : '' ?>"
                       onclick="selIcono('<?= $ico ?>', this)">
                    <input type="radio" name="_icono_radio" value="<?= $ico ?>"
                           <?= ($categoria['icono'] ?? 'bi-grid') === $ico ? 'checked' : '' ?>>
                    <i class="bi <?= $ico ?>"></i>
                    <span><?= $lbl ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-guardar btn">
                <i class="bi bi-check-circle me-2"></i><?= $es_editar ? 'Guardar cambios' : 'Crear categoría' ?>
            </button>
            <a href="<?= APP_URL ?>/categorias" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>

<script>
function selIcono(ico, card) {
    document.querySelectorAll('.icono-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('iconoInput').value = ico;
    document.getElementById('previewIcono').className = 'bi ' + ico;
}
function selColor(color) {
    document.getElementById('colorPicker').value = color;
    actualizarPreview();
}
function actualizarPreview() {
    const color  = document.getElementById('colorPicker').value;
    const nombre = document.querySelector('[name=nombre]').value || 'Nueva categoría';
    const dot    = document.getElementById('previewDot');
    dot.style.background = color + '20';
    dot.style.color      = color;
    document.getElementById('previewNombre').textContent = nombre;
}
</script>
