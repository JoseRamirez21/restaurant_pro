<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin acceso — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f1eb; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .card-error { background:#fff; border:1px solid #e8e5df; border-radius:16px; padding:2.5rem; text-align:center; max-width:380px; }
    </style>
</head>
<body>
<div class="card-error">
    <i class="bi bi-shield-lock" style="font-size:3rem;color:#8e44ad;"></i>
    <h4 class="mt-3 fw-600">Sin acceso</h4>
    <p class="text-muted" style="font-size:14px;">No tienes permisos para ver esta página.</p>
    <div class="d-flex gap-2 justify-content-center">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
        <a href="<?= APP_URL ?>/logout" class="btn btn-sm" style="background:#8e44ad;color:#fff;border-radius:8px;">
            <i class="bi bi-box-arrow-left me-1"></i>Cerrar sesión
        </a>
    </div>
</div>
</body>
</html>