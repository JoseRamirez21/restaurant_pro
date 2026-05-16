<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f1eb; }
        .topbar { background:#2c1f3e; color:#fff; padding:.9rem 1.2rem; display:flex; align-items:center; justify-content:space-between; }
        .topbar .brand { font-size:15px; font-weight:500; }
        .sin-pedidos { text-align:center; padding:3rem; color:#aaa; }
    </style>
</head>
<body>
<div class="topbar">
    <div class="brand"><i class="bi bi-receipt me-2"></i>Pedidos</div>
    <a href="<?= APP_URL ?>/logout" class="btn btn-sm btn-outline-light">
        <i class="bi bi-box-arrow-left"></i>
    </a>
</div>
<div class="container-fluid p-3">
    <div class="sin-pedidos">
        <i class="bi bi-receipt" style="font-size:2.5rem;"></i>
        <p class="mt-2">No hay pedidos activos</p>
    </div>
</div>
</body>
</html>