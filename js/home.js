// Acordeón de servicios
function toggleSvc(i) {
    const filas   = document.querySelectorAll('.svc-row');
    const abierta = filas[i].classList.contains('open');
    filas.forEach(f => f.classList.remove('open'));
    if (!abierta) filas[i].classList.add('open');
}

function openSvc(i) {
    const filas = document.querySelectorAll('.svc-row');
    filas.forEach(f => f.classList.remove('open'));
    filas[i].classList.add('open');
}

// Testimonios — caen una a una al hacer scroll
(function () {
    const CARDS = [
        { text: '"Tallulah es increíble. Mi Coco llegó agotado y feliz."',              autor: '— Laura M., Barcelona', cls: 'postit-1', rot: '-4deg', x: '3%',  y: '15%', w: '28%' },
        { text: '"Recibí fotos del paseo en tiempo real. Mi perrita muy contenta."',    autor: '— Marc T.',             cls: 'postit-2', rot: '3deg',  x: '34%', y: '5%',  w: '30%' },
        { text: '"Me fui de viaje tranquila sabiendo que Bruno estaba bien cuidado."',  autor: '— Ana R., Clot',        cls: 'postit-3', rot: '-2deg', x: '67%', y: '14%', w: '29%' },
        { text: '"Puntual, cariñosa y responsable. El reporte con fotitos es precioso."', autor: '— Sofía P.',          cls: 'postit-4', rot: '5deg',  x: '48%', y: '38%', w: '30%' },
        { text: '"Mi gato es muy especial y Tallulah lo trató con toda la paciencia."', autor: '— Jordi V.',            cls: 'postit-5', rot: '-3deg', x: '8%',  y: '42%', w: '34%' },
    ];

    const stage  = document.getElementById('cardsStage');
    const anchor = document.getElementById('testiAnchor');
    if (!stage || !anchor) return;

    // Creo cada tarjeta fuera de la pantalla (top: 120%)
    const els = CARDS.map(c => {
        const el = document.createElement('div');
        el.className = 'postit ' + c.cls;

        const q = document.createElement('p');
        q.textContent = c.text;
        el.appendChild(q);

        const a = document.createElement('span');
        a.textContent = c.autor;
        el.appendChild(a);

        el.style.cssText = `left:${c.x};width:${c.w};top:120%;transform:rotate(${c.rot});transition:top .6s cubic-bezier(.22,1,.36,1);`;
        stage.appendChild(el);
        return el;
    });

    // Al hacer scroll calculo el progreso y bajo cada tarjeta a su posición
    function update() {
        const progreso = Math.max(0, Math.min(1, -anchor.getBoundingClientRect().top / (window.innerHeight * 4)));
        els.forEach((el, i) => {
            el.style.top = progreso >= i * 0.16 ? CARDS[i].y : '120%';
        });
    }

    window.addEventListener('scroll', update, { passive: true });
    update();
})();

// Slideshow de fotos
(function () {
    const slides = document.querySelectorAll('.slide');
    const dots   = document.querySelectorAll('.dot');
    if (!slides.length) return;

    let current = 0;

    function goTo(n) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }

    setInterval(() => goTo(current + 1), 3000);
    dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));
})();

// Abre el acordeón según ?svc= en la URL
(function () {
    const svc = new URLSearchParams(window.location.search).get('svc');
    if (svc !== null) setTimeout(() => openSvc(parseInt(svc)), 100);
})();