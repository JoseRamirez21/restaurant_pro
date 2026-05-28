<?php /** @var string $page_title */ ?>
<style>
.conf-card { background:#fff; border:1px solid #e8e5df; border-radius:14px; padding:1.5rem; max-width:580px; }
.form-label { font-size:13px; font-weight:500; color:#444; margin-bottom:.3rem; }
.form-control, .form-select { font-size:13px; border-radius:8px; border:1.5px solid #e0ddd6; }
.form-control:focus, .form-select:focus { border-color:#8e44ad; box-shadow:0 0 0 3px rgba(142,68,173,.1); }
.btn-guardar { background:#8e44ad; color:#fff; border:none; border-radius:10px; padding:.65rem 1.5rem; font-size:14px; font-weight:500; }
.seccion-titulo { font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.06em; margin:1.2rem 0 .6rem; border-bottom:1px solid #f0ede7; padding-bottom:.4rem; }
.test-result { border-radius:10px; padding:.75rem 1rem; font-size:13px; display:none; margin-top:.75rem; }
.test-ok   { background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; }
.test-fail { background:#ffebee; color:#c62828; border:1px solid #ef9a9a; }
.tipo-btn { border:2px solid #e8e5df; border-radius:10px; padding:.75rem 1rem; cursor:pointer; transition:all .15s; }
.tipo-btn.selected { border-color:#8e44ad; background:#f9f5fd; }
.tipo-btn input { display:none; }
.info-box { background:#e3f2fd; border-radius:10px; padding:.75rem 1rem; font-size:13px; color:#1565c0; margin-bottom:1rem; }
</style>

<div class="conf-card">
    <h5 class="fw-600 mb-1">Configuración de impresora térmica</h5>
    <p style="font-size:13px;color:#888;margin-bottom:1.5rem;">
        Compatible con Epson TM-T20, TM-T88, Star TSP100 y genéricas ESC/POS.
    </p>

    <div class="info-box">
        <i class="bi bi-info-circle me-2"></i>
        Para cambiar la IP o tipo de conexión, edita directamente el método
        <code>config()</code> en <code>app/models/Impresora.php</code>
    </div>

    <div class="seccion-titulo">Tipo de conexión actual</div>

    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="tipo-btn selected">
                <i class="bi bi-wifi" style="font-size:1.5rem;color:#8e44ad;display:block;margin-bottom:.3rem;"></i>
                <div style="font-size:13px;font-weight:600;">Red (LAN/WiFi)</div>
                <div style="font-size:11px;color:#888;">IP: 192.168.1.100:9100</div>
                <div style="font-size:11px;color:#aaa;margin-top:2px;">Recomendado para caja fija</div>
            </div>
        </div>
        <div class="col-6">
            <div class="tipo-btn">
                <i class="bi bi-usb-symbol" style="font-size:1.5rem;color:#888;display:block;margin-bottom:.3rem;"></i>
                <div style="font-size:13px;font-weight:600;">USB</div>
                <div style="font-size:11px;color:#888;">Impresora compartida Windows</div>
                <div style="font-size:11px;color:#aaa;margin-top:2px;">Nombre: POS-80</div>
            </div>
        </div>
    </div>

    <div class="seccion-titulo">Probar conexión</div>

    <p style="font-size:13px;color:#888;margin-bottom:1rem;">
        Imprime un ticket de prueba para verificar que la impresora está conectada correctamente.
    </p>

    <button class="btn-guardar btn" onclick="testImpresora()" id="btnTest">
        <i class="bi bi-printer me-2"></i>Imprimir ticket de prueba
    </button>

    <div class="test-result test-ok"  id="testOk">
        <i class="bi bi-check-circle me-2"></i>
        ✅ Impresora conectada y funcionando correctamente.
    </div>
    <div class="test-result test-fail" id="testFail">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Error:</strong> <span id="testError"></span>
        <div style="margin-top:.5rem;font-size:12px;">
            Verifica que: la impresora esté encendida, conectada a la misma red y la IP sea correcta.
        </div>
    </div>

    <div class="seccion-titulo mt-4">Cómo conectar tu impresora</div>

    <div style="font-size:13px;color:#555;line-height:1.8;">
        <strong>Impresora en red (recomendado):</strong><br>
        1. Conecta la impresora al router por cable o WiFi<br>
        2. Imprime un auto-test (mantén presionado el botón al encender)<br>
        3. Anota la IP que aparece en el ticket<br>
        4. Edita <code>Impresora.php</code> → método <code>config()</code> → cambia la IP<br><br>

        <strong>Impresora USB:</strong><br>
        1. Conecta por USB al PC del cajero<br>
        2. En Windows: Panel de control → Dispositivos → anota el nombre<br>
        3. Edita <code>Impresora.php</code> → cambia <code>tipo</code> a <code>'usb'</code> y el nombre
    </div>
</div>

<script>
function testImpresora() {
    const btn = document.getElementById('btnTest');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Probando...';

    fetch('<?= APP_URL ?>/impresora/test', { method: 'POST' })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-printer me-2"></i>Imprimir ticket de prueba';
        if (data.ok) {
            document.getElementById('testOk').style.display   = '';
            document.getElementById('testFail').style.display = 'none';
        } else {
            document.getElementById('testFail').style.display = '';
            document.getElementById('testOk').style.display   = 'none';
            document.getElementById('testError').textContent  = data.error || 'Error desconocido';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-printer me-2"></i>Imprimir ticket de prueba';
        document.getElementById('testFail').style.display = '';
        document.getElementById('testError').textContent  = 'No se pudo conectar al servidor';
    });
}
</script>