<?php
/** @var array|null $cliente */
/** @var string     $error   */
$es_editar = !is_null($cliente);
$action    = $es_editar
    ? APP_URL . '/clientes/editar/' . $cliente['id']
    : APP_URL . '/clientes/crear';
?>
<style>
.form-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:540px; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; }
.btn-guardar:hover { background:#7d3c98; color:#fff; }
.seccion-titulo { font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .6rem; border-bottom:1px solid #f0ede7; padding-bottom:.4rem; }
</style>

<div class="form-card">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/clientes" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-600">
            <?= $es_editar ? 'Editar — ' . htmlspecialchars($cliente['nombre']) : 'Nuevo cliente' ?>
        </h5>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 mb-3" style="font-size:13px;border-radius:10px;">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $action ?>">

        <div class="seccion-titulo">Datos personales</div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>"
                       placeholder="Ej: María" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control"
                       value="<?= htmlspecialchars($cliente['apellido'] ?? '') ?>"
                       placeholder="Ej: Quispe">
            </div>
        </div>

        <div class="seccion-titulo">Contacto</div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="telefono" class="form-control"
                       value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>"
                       placeholder="999 123 456">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($cliente['email'] ?? '') ?>"
                       placeholder="cliente@email.com">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha de nacimiento <span style="color:#aaa;font-weight:400;">— para saludarle en su día</span></label>
            <input type="date" name="fecha_nac" class="form-control"
                   value="<?= $cliente['fecha_nac'] ?? '' ?>">
        </div>

        <div class="seccion-titulo">Notas internas</div>

        <div class="mb-4">
            <textarea name="notas" class="form-control" rows="3"
                      placeholder="Preferencias, alergias, mesa favorita..."><?= htmlspecialchars($cliente['notas'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-guardar btn">
                <i class="bi bi-check-circle me-2"></i><?= $es_editar ? 'Guardar cambios' : 'Registrar cliente' ?>
            </button>
            <a href="<?= APP_URL ?>/clientes" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>