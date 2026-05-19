/**
 * RestaurantePro — Sistema de notificaciones en tiempo real
 * Polling cada 8 segundos hacia /notificaciones/estado
 */

const Notificaciones = (() => {

    const INTERVALO   = 8000; // ms
    const APP_URL     = window.APP_URL || '';
    let   timer       = null;
    let   audioCtx    = null;
    let   ultimasVistas = new Set();

    // Crear sonido de notificación con Web Audio API
    function beep(tipo = 'info') {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc  = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.value = tipo === 'listo' ? 880 : 660;
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.4);
        } catch(e) {}
    }

    // Crear contenedor de toasts si no existe
    function getContenedor() {
        let c = document.getElementById('notif-contenedor');
        if (!c) {
            c = document.createElement('div');
            c.id = 'notif-contenedor';
            c.style.cssText = `
                position: fixed; bottom: 1rem; right: 1rem;
                z-index: 9999; display: flex; flex-direction: column;
                gap: .5rem; max-width: 320px; width: 100%;
            `;
            document.body.appendChild(c);
        }
        return c;
    }

    // Mostrar toast
    function mostrarToast(msg, tipo = 'info', accion = null) {
        const colores = {
            listo   : { bg: '#e8f5e9', borde: '#28a745', texto: '#2e7d32', icono: 'bi-check-circle-fill' },
            warning : { bg: '#fff3e0', borde: '#fd7e14', texto: '#e65100', icono: 'bi-exclamation-triangle-fill' },
            info    : { bg: '#e3f2fd', borde: '#2196f3', texto: '#1565c0', icono: 'bi-info-circle-fill' },
        };
        const c   = colores[tipo] || colores.info;
        const div = document.createElement('div');
        div.style.cssText = `
            background:${c.bg}; border-left:4px solid ${c.borde};
            border-radius:10px; padding:.75rem 1rem;
            box-shadow:0 4px 16px rgba(0,0,0,.12);
            animation: slideIn .3s ease;
            display:flex; align-items:flex-start; gap:.6rem;
        `;
        div.innerHTML = `
            <i class="bi ${c.icono}" style="color:${c.borde};font-size:18px;flex-shrink:0;margin-top:1px;"></i>
            <div style="flex:1;">
                <div style="font-size:13px;font-weight:500;color:${c.texto};">${msg}</div>
                ${accion ? `<button onclick="${accion.fn}(${accion.id})" style="margin-top:4px;font-size:11px;background:${c.borde};color:#fff;border:none;border-radius:6px;padding:2px 10px;cursor:pointer;">${accion.label}</button>` : ''}
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;color:${c.texto};opacity:.5;cursor:pointer;font-size:16px;padding:0;line-height:1;">×</button>
        `;

        // Auto-remover en 6 segundos
        setTimeout(() => { if (div.parentElement) div.remove(); }, 6000);
        getContenedor().appendChild(div);
    }

    // Actualizar badge en topbar
    function actualizarBadge(total) {
        let badge = document.getElementById('notif-badge');
        if (total > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.id = 'notif-badge';
                badge.style.cssText = `
                    background:#e94560; color:#fff; font-size:10px;
                    padding:1px 7px; border-radius:20px; font-weight:600;
                `;
                const topbar = document.querySelector('.topbar .d-flex');
                if (topbar) topbar.appendChild(badge);
            }
            badge.textContent = total + ' en cocina';
            badge.style.display = '';
        } else if (badge) {
            badge.style.display = 'none';
        }
    }

    // Consultar al servidor
    async function consultar() {
        try {
            const res  = await fetch(APP_URL + '/notificaciones/estado', { cache: 'no-store' });
            const data = await res.json();
            if (!data.ok) return;

            actualizarBadge(data.pendientes || 0);

            // Notificaciones para mozo: items listos
            if (data.notifs && data.notifs.length > 0) {
                data.notifs.forEach(n => {
                    const key = 'item-' + n.detalle_id;
                    if (!ultimasVistas.has(key)) {
                        ultimasVistas.add(key);
                        beep('listo');
                        mostrarToast(
                            `✅ <strong>${n.producto_nombre}</strong> listo para ${n.mesa_nombre}`,
                            'listo',
                            { fn: 'Notificaciones.marcarEntregado', id: n.detalle_id, label: 'Marcar entregado' }
                        );
                    }
                });
            }

            // Para cocina: avisar si hay nuevas comandas
            if (data.rol === 'cocinero' && data.pendientes > 0) {
                const key = 'cocina-' + data.pendientes + '-' + data.hora?.slice(0,5);
                if (!ultimasVistas.has(key)) {
                    ultimasVistas.add(key);
                    if (ultimasVistas.size > 1) { // no notificar en la primera carga
                        beep('info');
                        mostrarToast(`🍳 ${data.pendientes} comanda(s) pendiente(s)`, 'warning');
                    }
                }
            }

        } catch(e) {
            // Silencioso — no molestar si no hay conexión
        }
    }

    function marcarEntregado(detalleId) {
        fetch(APP_URL + '/notificaciones/entregar/' + detalleId, { method: 'POST' });
        // Remover el toast correspondiente
        const toasts = document.querySelectorAll('#notif-contenedor > div');
        toasts.forEach(t => {
            if (t.textContent.includes('Marcar entregado')) {
                const btn = t.querySelector('button[onclick*="' + detalleId + '"]');
                if (btn) t.remove();
            }
        });
    }

    function iniciar() {
        // Inyectar animación CSS
        if (!document.getElementById('notif-style')) {
            const s = document.createElement('style');
            s.id = 'notif-style';
            s.textContent = `
                @keyframes slideIn {
                    from { opacity:0; transform:translateX(20px); }
                    to   { opacity:1; transform:translateX(0); }
                }
            `;
            document.head.appendChild(s);
        }
        consultar();
        timer = setInterval(consultar, INTERVALO);
    }

    function detener() {
        if (timer) clearInterval(timer);
    }

    return { iniciar, detener, marcarEntregado, mostrarToast };
})();

// Arrancar automáticamente cuando cargue la página
document.addEventListener('DOMContentLoaded', () => Notificaciones.iniciar());