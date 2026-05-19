<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f1eb; }
        .topbar { background:#2c1f3e; color:#fff; padding:.9rem 1.2rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .res-item { background:#fff; border:1px solid #e8e5df; border-radius:12px; padding:1rem 1.2rem; margin-bottom:.6rem; }
        .res-hora { font-size:1.3rem; font-weight:700; color:#2c1f3e; }
        .estado-pill { font-size:11px; padding:2px 10px; border-radius:20px; font-weight:500; }
        .sin-res { text-align:center; padding:3rem; color:#aaa; }
    </style>
</head>
<body>
<div class="topbar">
    <a href="<?= APP_URL ?>/mesas" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left"></i></a>
    <span style="font-weight:500;"><i class="bi bi-calendar-check me-2"></i>Reservas de hoy</span>
    <a href="<?= APP_URL ?>/reservas/nueva" class="btn btn-sm btn-outline-light">
        <i class="bi bi-plus"></i>
    </a>
</div>

<div class="container-fluid p-3">
    <?php
    $estados = [
        'pendiente'  => ['label'=>'Pendiente',  'bg'=>'#e3f2fd','color'=>'#1565c0'],
        'confirmada' => ['label'=>'Confirmada', 'bg'=>'#e8f5e9','color'=>'#2e7d32'],
        'sentada'    => ['label'=>'Sentada',    'bg'=>'#fff3e0','color'=>'#e65100'],
        'cancelada'  => ['label'=>'Cancelada',  'bg'=>'#ffebee','color'=>'#c62828'],
        'no_show'    => ['label'=>'No show',    'bg'=>'#f3e5f5','color'=>'#6a1b9a'],
    ];
    if (empty($reservas)):
    ?>
    <div class="sin-res">
        <i class="bi bi-calendar-x" style="font-size:2.5rem;"></i>
        <p class="mt-2">Sin reservas para hoy</p>
    </div>
    <?php else: ?>
    <?php foreach ($reservas as $r):
        $est = $estados[$r['estado']] ?? $estados['pendiente'];
    ?>
    <div class="res-item">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="res-hora"><?= substr($r['hora'],0,5) ?></div>
            <span class="estado-pill" style="background:<?= $est['bg'] ?>;color:<?= $est['color'] ?>;">
                <?= $est['label'] ?>
            </span>
        </div>
        <div style="font-size:14px;font-weight:500;"><?= htmlspecialchars($r['nombre_cliente']) ?></div>
        <div style="font-size:12px;color:#888;">
            <i class="bi bi-people me-1"></i><?= $r['personas'] ?> personas
            <?php if ($r['mesa_nombre']): ?>
            &nbsp;·&nbsp; <?= htmlspecialchars($r['mesa_nombre']) ?>
            <?php endif; ?>
        </div>
        <?php if ($r['notas']): ?>
        <div style="font-size:11px;color:#aaa;margin-top:3px;">
            <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($r['notas']) ?>
        </div>
        <?php endif; ?>
        <div class="d-flex gap-2 mt-2">
            <form method="POST" action="<?= APP_URL ?>/reservas/estado/<?= $r['id'] ?>">
                <input type="hidden" name="estado" value="confirmada">
                <button type="submit" class="btn btn-sm btn-success"
                        style="font-size:12px;border-radius:8px;"
                        <?= $r['estado']==='confirmada'?'disabled':'' ?>>
                    <i class="bi bi-check2"></i> Confirmar
                </button>
            </form>
            <form method="POST" action="<?= APP_URL ?>/reservas/estado/<?= $r['id'] ?>">
                <input type="hidden" name="estado" value="sentada">
                <button type="submit" class="btn btn-sm btn-warning"
                        style="font-size:12px;border-radius:8px;">
                    <i class="bi bi-person-check"></i> Sentar
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
<script>window.APP_URL = "<?= APP_URL ?>";</script>
<script src="<?= APP_URL ?>/public/js/notificaciones.js"></script>
</body>
</html>