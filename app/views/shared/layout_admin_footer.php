</div><!-- /content -->
</div><!-- /main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (!empty($page_scripts)) echo $page_scripts; ?>
<script>
// Reloj en topbar
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

// Sidebar móvil
function abrirSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('show');
}
function cerrarSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
}
</script>
</body>
</html>