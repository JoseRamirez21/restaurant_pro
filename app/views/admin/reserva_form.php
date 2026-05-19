<?php
/** @var array|null $reserva */
/** @var array      $mesas   */
/** @var string     $error   */
$es_editar = !is_null($reserva);
$action    = $es_editar
    ? APP_URL . '/reservas/editar/' . $reserva['id']
    : APP_URL . '/reservas/crear';
$zonas = [
    'salon_principal' => 'Salón principal',
    'terraza'         => 'Terraza',
    'privado'         => 'Sala privada',
    'bar'             => 'Bar',
];
?>
<style>
.form-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:600px; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control, .form-select { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus, .form-select:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; }
.btn-guardar:hover { background:#7d3c98; color:#fff; }
.seccion-titulo { font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .6rem; border-bottom:1px solid #f0ede7; padding-bottom:.4rem; }
.mesa-opcion { border:2px solid #e8e5df; border-radius:10px; padding:.6rem .8rem; cursor:pointer; transition:all .15s; font-size:13px; }
.mesa-opcion:hover { border-color:#8e44ad; }
.mesa-opcion.selected { border-color:#8e44ad; background:#f9f5fd; }
.mesa-opcion input { display:none; }
.hora-rapida { border:1.5px solid #e8e5df; background:#fff; border-radius:8px; padding:.3rem .7rem; font-size:12px; cursor:pointer; transition:all .15s; }
.hora-rapida:hover, .hora-rapida.active { background:#8e44ad; color:#fff; border-color:#8e44ad; }
</style>

<div class="form-card">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/reservas" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-600">
            <?= $es_editar ? 'Editar reserva — ' . htmlspecialchars($reserva['nombre_cliente']) : 'Nueva reserva' ?>
        </h5>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 mb-3" style="font-size:13px;border-radius:10px;">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $action ?>">

        <div class="seccion-titulo">Datos del cliente</div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Nombre del cliente *</label>
                <input type="text" name="nombre_cliente" class="form-control"
                       value="<?= htmlspecialchars($reserva['nombre_cliente'] ?? '') ?>"
                       placeholder="Ej: Juan Pérez" required autofocus>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="telefono" class="form-control"
                       value="<?= htmlspecialchars($reserva['telefono'] ?? '') ?>"
                       placeholder="Ej: 999 123 456">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email_cliente" class="form-control"
                   value="<?= htmlspecialchars($reserva['email_cliente'] ?? '') ?>"
                   placeholder="cliente@email.com">
        </div>

        <div class="seccion-titulo">Fecha y hora</div>

        <div class="row g-3 mb-3">
            <div class="col-md-5">
                <label class="form-label">Fecha *</label>
                <input type="date" name="fecha" class="form-control"
                       value="<?= $reserva['fecha'] ?? date('Y-m-d') ?>"
                       min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Hora *</label>
                <input type="time" name="hora" id="horaInput" class="form-control"
                       value="<?= $reserva['hora'] ?? '13:00' ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Personas *</label>
                <input type="number" name="personas" class="form-control"
                       min="1" max="30"
                       value="<?= $reserva['personas'] ?? 2 ?>" required>
            </div>
        </div>

        <!-- Horas rápidas -->
        <div class="d-flex flex-wrap gap-2 mb-3">
            <?php foreach (['12:00','12:30','13:00','13:30','14:00','14:30','19:00','19:30','20:00','20:30','21:00','21:30'] as $h): ?>
            <button type="button" class="hora-rapida <?= ($reserva['hora'] ?? '13:00') === $h.':00' ? 'active' : '' ?>"
                    onclick="selHora('<?= $h ?>')">
                <?= $h ?>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="seccion-titulo">Mesa asignada <span style="font-weight:400;color:#aaa;">— opcional</span></div>

        <div class="row g-2 mb-3">
            <div class="col-4 col-md-2">
                <label class="mesa-opcion d-block text-center <?= empty($reserva['mesa_id']) ? 'selected' : '' ?>"
                       onclick="selMesa(0, this)">
                    <input type="radio" name="mesa_id" value="0"
                           <?= empty($reserva['mesa_id']) ? 'checked' : '' ?>>
                    <i class="bi bi-question-circle" style="font-size:1.2rem;color:#aaa;"></i>
                    <div style="font-size:11px;color:#aaa;margin-top:2px;">Sin asignar</div>
                </label>
            </div>
            <?php foreach ($mesas as $m): ?>
            <?php if ($m['estado'] === 'libre' || ($es_editar && $m['id'] == $reserva['mesa_id'])): ?>
            <div class="col-4 col-md-2">
                <label class="mesa-opcion d-block text-center <?= ($es_editar && $m['id'] == $reserva['mesa_id']) ? 'selected' : '' ?>"
                       onclick="selMesa(<?= $m['id'] ?>, this)">
                    <input type="radio" name="mesa_id" value="<?= $m['id'] ?>"
                           <?= ($es_editar && $m['id'] == $reserva['mesa_id']) ? 'checked' : '' ?>>
                    <div style="font-size:13px;font-weight:600;"><?= $m['numero'] ?></div>
                    <div style="font-size:10px;color:#888;"><?= $m['capacidad'] ?> pers.</div>
                </label>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="mb-4">
            <label class="form-label">Notas especiales</label>
            <textarea name="notas" class="form-control" rows="2"
                      placeholder="Ej: cumpleaños, alergias, preferencias de mesa..."><?= htmlspecialchars($reserva['notas'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-guardar btn">
                <i class="bi bi-check-circle me-2"></i><?= $es_editar ? 'Guardar cambios' : 'Crear reserva' ?>
            </button>
            <a href="<?= APP_URL ?>/reservas" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>

<script>
function selHora(h) {
    document.querySelectorAll('.hora-rapida').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('horaInput').value = h;
}
function selMesa(id, card) {
    document.querySelectorAll('.mesa-opcion').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    card.querySelector('input').checked = true;
}
</script>