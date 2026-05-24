<?php
/** @var array $grupos */
$grupos_nombres = [
    'general'  => ['label'=>'Información del restaurante', 'icon'=>'bi-shop'],
    'horario'  => ['label'=>'Horario de atención',         'icon'=>'bi-clock'],
    'boleta'   => ['label'=>'Boleta y comprobante',        'icon'=>'bi-receipt'],
    'sistema'  => ['label'=>'Sistema y apariencia',        'icon'=>'bi-gear'],
];
?>
<style>
.conf-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; overflow:hidden; margin-bottom:1rem; }
.conf-card-header { padding:.9rem 1.2rem; border-bottom:1px solid #f0ede7; display:flex; align-items:center; gap:.6rem; font-size:14px; font-weight:600; background:#faf9f6; }
.conf-card-header i { font-size:18px; color:#8e44ad; }
.conf-body { padding:1.2rem; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control, .form-select { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus, .form-select:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.form-check-input:checked { background-color:#8e44ad; border-color:#8e44ad; }
.color-preview { width:36px; height:36px; border-radius:8px; border:1.5px solid #e0ddd6; cursor:pointer; flex-shrink:0; }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.7rem 2rem; font-size:14px; font-weight:500; }
.btn-guardar:hover { background:#7d3c98; color:#fff; }
.alert-exito { background:#e8f5e9; border:1px solid #a5d6a7; color:#2e7d32; border-radius:10px; padding:.75rem 1rem; font-size:13px; margin-bottom:1rem; }
</style>

<?php if (isset($_GET['guardado'])): ?>
<div class="alert-exito">
    <i class="bi bi-check-circle me-2"></i>Configuración guardada correctamente.
</div>
<?php endif; ?>

<form method="POST" action="<?= APP_URL ?>/configuracion/guardar">

    <?php foreach ($grupos_nombres as $grupo => $info): ?>
    <?php if (empty($grupos[$grupo])): continue; endif; ?>

    <div class="conf-card">
        <div class="conf-card-header">
            <i class="bi <?= $info['icon'] ?>"></i>
            <?= $info['label'] ?>
        </div>
        <div class="conf-body">
            <div class="row g-3">
            <?php foreach ($grupos[$grupo] as $conf): ?>
                <?php if ($conf['tipo'] === 'booleano'): ?>
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="<?= $conf['clave'] ?>"
                               id="<?= $conf['clave'] ?>"
                               value="1"
                               <?= $conf['valor'] === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="<?= $conf['clave'] ?>"
                               style="font-size:13px;font-weight:500;">
                            <?= htmlspecialchars($conf['label']) ?>
                        </label>
                    </div>
                </div>

                <?php elseif ($conf['tipo'] === 'color'): ?>
                <div class="col-md-6">
                    <label class="form-label"><?= htmlspecialchars($conf['label']) ?></label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" name="<?= $conf['clave'] ?>"
                               value="<?= htmlspecialchars($conf['valor'] ?? '#8e44ad') ?>"
                               class="color-preview"
                               oninput="document.getElementById('hex_<?= $conf['clave'] ?>').value=this.value">
                        <input type="text" id="hex_<?= $conf['clave'] ?>"
                               class="form-control"
                               value="<?= htmlspecialchars($conf['valor'] ?? '#8e44ad') ?>"
                               oninput="document.querySelector('[name=<?= $conf['clave'] ?>]').value=this.value"
                               maxlength="7">
                    </div>
                </div>

                <?php elseif ($conf['tipo'] === 'numero'): ?>
                <div class="col-md-4">
                    <label class="form-label"><?= htmlspecialchars($conf['label']) ?></label>
                    <input type="number" name="<?= $conf['clave'] ?>"
                           class="form-control" min="0"
                           value="<?= htmlspecialchars($conf['valor'] ?? '') ?>">
                </div>

                <?php else: ?>
                <?php $cols = in_array($conf['clave'], ['rest_slogan','boleta_mensaje','boleta_pie','rest_direccion']) ? 'col-md-12' : 'col-md-6'; ?>
                <div class="<?= $cols ?>">
                    <label class="form-label"><?= htmlspecialchars($conf['label']) ?></label>
                    <?php if (in_array($conf['clave'], ['boleta_mensaje','boleta_pie'])): ?>
                    <textarea name="<?= $conf['clave'] ?>" class="form-control" rows="2"><?= htmlspecialchars($conf['valor'] ?? '') ?></textarea>
                    <?php else: ?>
                    <input type="text" name="<?= $conf['clave'] ?>"
                           class="form-control"
                           value="<?= htmlspecialchars($conf['valor'] ?? '') ?>">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php endforeach; ?>

    <div class="d-flex gap-2 align-items-center">
        <button type="submit" class="btn-guardar btn">
            <i class="bi bi-check-circle me-2"></i>Guardar configuración
        </button>
        <span style="font-size:12px;color:#aaa;">Los cambios se aplican de inmediato</span>
    </div>

</form>