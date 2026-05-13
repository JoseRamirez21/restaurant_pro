<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin acceso — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
<div class="text-center p-4">
    <i class="bi bi-shield-lock" style="font-size:3rem; color:#8e44ad;"></i>
    <h2 class="mt-3 fw-500">Sin acceso</h2>
    <p class="text-muted">No tienes permisos para ver esta página.</p>
    <a href="<?= APP_URL ?>/logout" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al inicio
    </a>
</div>
</body>
</html>