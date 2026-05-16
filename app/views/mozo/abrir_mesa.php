<?php
// Solo para autocompletado del editor — no afecta el sistema
/** @var array $mesa */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abrir Mesa — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f1eb; min-height:100vh; display:flex; flex-direction:column; }
        .topbar { background:#2c1f3e; color:#fff; padding:.9rem 1.2rem; display:flex; align-items:center; justify-content:space-between; }
        .card-abrir { background:#fff; border:1px solid #e0ddd6; border-radius:16px; padding:2rem; max-width:400px; width:100%; }
        .mesa-big { font-size:4rem; text-align:center; margin-bottom:.5rem; }
        .personas-btn { width:44px; height:44px; border-radius:50%; border:2px solid #8e44ad; background:#fff; color:#8e44ad; font-size:1.2rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .2s; }
        .personas-btn:hover { background:#8e44ad; color:#fff; }
        .personas-num { font-size:2rem; font-weight:600; min-width:50px; text-align:center; }
        .btn-abrir { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.75rem; font-size:1rem; font-weight:500; width:100%; }
        .btn-abrir:hover { background:#7d3c98; }
    </style>
</head>
<body>

<div class="topbar">
    <a href="<?= APP_URL ?>/mesas" class="btn btn-sm btn-outline-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span style="font-weight:500;">Abrir mesa</span>
    <div></div>
</div>

<div class="flex-grow-1 d-flex align-items-center justify-content-center p-3">
    <div class="card-abrir">

        <div class="mesa-big">🍽</div>
        <h4 class="text-center fw-600 mb-0"><?= htmlspecialchars($mesa['nombre']) ?></h4>
        <p class="text-center text-muted mb-3" style="font-size:13px;">
            <i class="bi bi-people me-1"></i>Capacidad: <?= $mesa['capacidad'] ?> personas
        </p>

        <form method="POST" action="<?= APP_URL ?>/mesas/abrir/<?= $mesa['id'] ?>">

            <label class="form-label fw-500 mb-2">¿Cuántas personas?</label>
            <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                <button type="button" class="personas-btn" onclick="cambiar(-1)">−</button>
                <span class="personas-num" id="num">1</span>
                <button type="button" class="personas-btn" onclick="cambiar(1)">+</button>
                <input type="hidden" name="personas" id="personas_input" value="1">
            </div>

            <button type="submit" class="btn-abrir btn">
                <i class="bi bi-check-circle me-2"></i>Abrir mesa y tomar pedido
            </button>
        </form>

    </div>
</div>

<script>
let personas = 1;
const max = <?= $mesa['capacidad'] ?>;
function cambiar(n) {
    personas = Math.min(Math.max(1, personas + n), max);
    document.getElementById('num').textContent = personas;
    document.getElementById('personas_input').value = personas;
}
</script>
</body>
</html>