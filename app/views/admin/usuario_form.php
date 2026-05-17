<?php
/** @var array|null $usuario */
/** @var string $error */
$es_editar = !is_null($usuario);
$action    = $es_editar
    ? APP_URL . '/usuarios/editar/' . $usuario['id']
    : APP_URL . '/usuarios/crear';
$roles = [
    'administrador' => 'Administrador',
    'supervisor'    => 'Supervisor',
    'mesero'        => 'Mesero',
    'cocinero'      => 'Cocinero',
    'cajero'        => 'Cajero',
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
.rol-card { border:2px solid #e8e5df; border-radius:10px; padding:.75rem 1rem; cursor:pointer; transition:all .15s; }
.rol-card:hover { border-color:#8e44ad; }
.rol-card.selected { border-color:#8e44ad; background:#f9f5fd; }
.rol-card input { display:none; }
.rol-nombre { font-size:13px; font-weight:500; }
.rol-desc { font-size:11px; color:#aaa; margin-top:2px; }
</style>

<div class="form-card">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/usuarios" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-600">
            <?= $es_editar ? 'Editar — ' . htmlspecialchars($usuario['nombre']) : 'Nuevo usuario' ?>
        </h5>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex gap-2 align-items-center py-2 mb-3" style="font-size:13px;border-radius:10px;">
        <i class="bi bi-exclamation-circle"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $action ?>">

        <div class="seccion-titulo">Datos personales</div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($usuario['nombre'] ?? $_POST['nombre'] ?? '') ?>"
                       placeholder="Ej: Carlos" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellido *</label>
                <input type="text" name="apellido" class="form-control"
                       value="<?= htmlspecialchars($usuario['apellido'] ?? $_POST['apellido'] ?? '') ?>"
                       placeholder="Ej: Quispe" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico *</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($usuario['email'] ?? $_POST['email'] ?? '') ?>"
                   placeholder="usuario@restaurantepro.pe" required>
        </div>

        <div class="seccion-titulo">Acceso al sistema</div>

        <div class="mb-3">
            <label class="form-label">
                Contraseña <?= $es_editar ? '<span style="color:#aaa;font-weight:400;">— dejar vacío para no cambiar</span>' : '*' ?>
            </label>
            <div class="input-group">
                <input type="password" name="password" id="passInput" class="form-control"
                       placeholder="<?= $es_editar ? 'Nueva contraseña (opcional)' : 'Mínimo 3 caracteres' ?>"
                       <?= $es_editar ? '' : 'required' ?>>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">
                    <i class="bi bi-eye" id="ojoIcon"></i>
                </button>
            </div>
        </div>

        <div class="seccion-titulo">Rol</div>

        <?php
        $descs = [
            'administrador' => 'Acceso total al sistema',
            'supervisor'    => 'Dashboard y reportes, sin configuración',
            'mesero'        => 'Mesas y toma de pedidos',
            'cocinero'      => 'Pantalla de cocina KDS',
            'cajero'        => 'Módulo de caja y cobros',
        ];
        ?>
        <div class="row g-2 mb-4">
            <?php foreach ($roles as $val => $label): ?>
            <div class="col-6 col-md-4">
                <label class="rol-card d-block <?= (($usuario['rol'] ?? $_POST['rol'] ?? 'mesero') === $val) ? 'selected' : '' ?>"
                       onclick="selRol(this)">
                    <input type="radio" name="rol" value="<?= $val ?>"
                           <?= (($usuario['rol'] ?? $_POST['rol'] ?? 'mesero') === $val) ? 'checked' : '' ?>>
                    <div class="rol-nombre"><?= $label ?></div>
                    <div class="rol-desc"><?= $descs[$val] ?></div>
                </label>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-guardar btn">
                <i class="bi bi-check-circle me-2"></i><?= $es_editar ? 'Guardar cambios' : 'Crear usuario' ?>
            </button>
            <a href="<?= APP_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>

<script>
function selRol(card) {
    document.querySelectorAll('.rol-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    card.querySelector('input').checked = true;
}
function togglePass() {
    const inp = document.getElementById('passInput');
    const ico = document.getElementById('ojoIcon');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>