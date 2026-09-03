// Cursor personalizado — mueve el punto y el anillo siguiendo al ratón
const cursor     = document.getElementById('cursor');
const cursorRing = document.getElementById('cursorRing');

document.addEventListener('mousemove', e => {
    if (cursor)     { cursor.style.left     = e.clientX + 'px'; cursor.style.top     = e.clientY + 'px'; }
    if (cursorRing) { cursorRing.style.left = e.clientX + 'px'; cursorRing.style.top = e.clientY + 'px'; }
});

// Menú hamburguesa — abre y cierra el overlay
function toggleMenu() {
    document.getElementById('menuOverlay').classList.toggle('open');
}

// Cierra el menú si el usuario hace clic fuera de él
document.addEventListener('click', e => {
    const overlay = document.getElementById('menuOverlay');
    if (overlay && overlay.classList.contains('open') && e.target === overlay) {
        overlay.classList.remove('open');
    }
});

// Cierra el menú con la tecla Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const overlay = document.getElementById('menuOverlay');
        if (overlay) overlay.classList.remove('open');
    }
});

// Acordeón de servicios en el index
function toggleSvc(index) {
    const rows = document.querySelectorAll('.svc-row');
    rows.forEach((row, i) => {
        if (i === index) {
            row.classList.toggle('open');
        } else {
            row.classList.remove('open'); // cierra los demás al abrir uno
        }
    });
}

// Abre el servicio indicado por la URL — ejemplo: index.php?svc=2
const params = new URLSearchParams(window.location.search);
const svcParam = params.get('svc');
if (svcParam !== null) {
    toggleSvc(parseInt(svcParam));
}