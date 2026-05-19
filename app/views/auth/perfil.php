<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f1eb; min-height:100vh; }
        .topbar { background:#2c1f3e; color:#fff; padding:.9rem 1.2rem; display:flex; align-items:center; justify-content:space-between; }
        .perfil-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:520px; margin:1.5rem auto; }
        .form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
        .form-control { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
        .form-control:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
        .btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; width:100%; }
        .btn-guardar:hover { background:#7d3c98; }
        .seccion-titulo { font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .6rem; border-bottom:1px solid #f0ede7; padding-bottom:.4rem; }
        .avatar-grande { width:64px; height:64px; border-radius:50%; background:#8e44ad; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:600; color:#fff; margin:0 auto 1rem; }
    </style>
</head>
<body>
<div class="topbar">
    <a href="javascript:history.back()" class="btn btn-sm btn-outline-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span style="font-weight:500;">Mi perfil</span>
    <a href="<?= APP_URL ?>/logout" class="btn btn-sm btn-outline-light">
        <i class="bi bi-box-arrow-left"></i>
    </a>
</div>

<div class="container-fluid p-3">
    <div class="perfil-card">
        <div class="avatar-grande"><?= strtoupper(substr($usuario['nombre'],0,1)) ?></div>
        <h5 class="text-center fw-600 mb-1"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h5>
        <p class="text-center text-muted mb-4" style="font-size:13px;"><?= ucfirst($usuario['rol']) ?></p>

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
                <div class="col-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
            <div class="seccion-titulo">Cambiar contraseña <span style="font-weight:400;color:#aaa;">— opcional</span></div>
            <div class="mb-3">
                <label class="form-label">Contraseña actual</label>
                <input type="password" name="password_actual" class="form-control" placeholder="••••••">
            </div>
            <div class="mb-4">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" name="password_nuevo" class="form-control" placeholder="••••••">
            </div>
            <button type="submit" class="btn-guardar btn">
                <i class="bi bi-check-circle me-2"></i>Guardar cambios
            </button>
        </form>
    </div>
</div>
</body>
</html>
