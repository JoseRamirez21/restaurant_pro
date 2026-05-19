<?php
/** @var array  $usuario */
/** @var string $error   */
/** @var string $exito   */
$roles_nombres = [
    'administrador' => 'Administrador',
    'supervisor'    => 'Supervisor',
    'mesero'        => 'Mesero',
    'cocinero'      => 'Cocinero',
    'cajero'        => 'Cajero',
];
?>
<style>
.perfil-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:560px; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; }
.btn-guardar:hover { background:#7d3c98; color:#fff; }
.seccion-titulo { font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .6rem; border-bottom:1px solid #f0ede7; padding-bottom:.4rem; }
.avatar-grande { width:64px; height:64px; border-radius:50%; background:#8e44ad; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:600; color:#fff; }
.rol-tag { font-size:12px; padding:3px 12px; border-radius:20px; background:#f3e5f5; color:#8e44ad; font-weight:500; }
</style>

<!-- Encabezado perfil -->
<div class="perfil-card mb-3">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="avatar-grande">
            <?= strtoupper(substr($usuario['nombre'],0,1)) ?>
        </div>
        <div>
            <div style="font-size:1.1rem;font-weight:600;">
                <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
            </div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="rol-tag"><?= $roles_nombres[$usuario['rol']] ?? $usuario['rol'] ?></span>
                <span style="font-size:12px;color:#aaa;"><?= htmlspecialchars($usuario['email']) ?></span>
            </div>
        </div>
    </div>

    <?php if (!empty($exito)): ?>
    <div class="alert alert-success py-2 mb-3" style="font-size:13px;border-radius:10px;">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($exito) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 mb-3" style="font-size:13px;border-radius:10px;">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/perfil/guardar">

        <div class="seccion-titulo">Datos personales</div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellido *</label>
                <input type="text" name="apellido" class="form-control"
                       value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico *</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>

        <div class="seccion-titulo">Cambiar contraseña <span style="color:#aaa;font-weight:400;font-size:11px;">— opcional</span></div>

        <div class="mb-3">
            <label class="form-label">Contraseña actual</label>
            <div class="input-group">
                <input type="password" name="password_actual" id="passActual" class="form-control"
                       placeholder="Ingresa tu contraseña actual">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('passActual','ojo1')">
                    <i class="bi bi-eye" id="ojo1"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Nueva contraseña</label>
            <div class="input-group">
                <input type="password" name="password_nuevo" id="passNuevo" class="form-control"
                       placeholder="Mínimo 3 caracteres">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('passNuevo','ojo2')">
                    <i class="bi bi-eye" id="ojo2"></i>
                </button>
            </div>
        </div>

        <div class="mb-3" style="background:#f4f1eb;border-radius:10px;padding:.75rem 1rem;font-size:12px;color:#888;">
            <i class="bi bi-shield-lock me-2"></i>
            Tu rol de <strong><?= $roles_nombres[$usuario['rol']] ?></strong> solo puede ser modificado por el administrador.
        </div>

        <button type="submit" class="btn-guardar btn">
            <i class="bi bi-check-circle me-2"></i>Guardar cambios
        </button>

    </form>
</div>

<script>
function togglePass(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(iconId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
