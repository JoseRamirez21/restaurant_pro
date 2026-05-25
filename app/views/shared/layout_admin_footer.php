</div><!-- /content -->
</div><!-- /main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.APP_URL = "<?= APP_URL ?>";</script>
<script src="<?= APP_URL ?>/public/js/notificaciones.js"></script>
<?php if (!empty($page_scripts)) echo $page_scripts; ?>
<script>
function actualizarReloj() {
    const el = document.getElementById('reloj-topbar');
    if (!el) return;
    const ahora = new Date();
    el.textContent = ahora.toLocaleDateString('es-PE', {
        weekday:'long', day:'numeric', month:'long'
    }) + ' · ' + ahora.toLocaleTimeString('es-PE');
}
actualizarReloj();
setInterval(actualizarReloj, 1000);

function abrirSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('show');
}
function cerrarSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
}

<?php if (esSoloLectura()): ?>
// Supervisor: bloquear clicks en botones de acción
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('a[href*="/crear"], a[href*="/nuevo"], a[href*="/editar"], a[href*="/eliminar"], a[href*="/toggle"]').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            alert('Modo supervisor: no tienes permisos para realizar esta acción.');
        });
    });
    document.querySelectorAll('form[method="POST"]').forEach(f => {
        f.addEventListener('submit', e => {
            e.preventDefault();
            alert('Modo supervisor: no tienes permisos para guardar cambios.');
        });
    });
});
<?php endif; ?>
</script>
</body>
</html>