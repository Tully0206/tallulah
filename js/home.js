// Slideshow de fotos — cambia la imagen activa cada 3 segundos
const slides = document.querySelectorAll('.slide');
const dots   = document.querySelectorAll('.dot');
let current  = 0;

function goToSlide(index) {
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = index;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
}

// Avanza automáticamente cada 3 segundos
setInterval(() => {
    goToSlide((current + 1) % slides.length);
}, 3000);

// Permite navegar haciendo clic en los puntos
dots.forEach((dot, i) => {
    dot.addEventListener('click', () => goToSlide(i));
});


// Testimonios — los genera dinámicamente para no tenerlos hardcodeados en el HTML
const testimonios = [
    { texto: '¡Tully es increíble! Mi perro volvió feliz y cansado. Repetiré sin duda.', autor: 'María G.' },
    { texto: 'Ana es muy cariñosa y profesional. Mi gata estuvo en buenas manos.', autor: 'Carlos M.' },
    { texto: 'Tranquilidad total mientras estaba de viaje. 100% recomendable.', autor: 'Laura P.' },
    { texto: 'Mi perro es muy nervioso y con Ana estuvo super tranquilo. ¡Gracias!', autor: 'Sergio R.' },
    { texto: 'Nos mandó fotos durante el paseo. Un detalle que se agradece mucho.', autor: 'Elena T.' },
];

const colores = ['postit-1', 'postit-2', 'postit-3', 'postit-4', 'postit-5'];
const stage   = document.getElementById('cardsStage');

if (stage) {
    testimonios.forEach((t, i) => {
        const card = document.createElement('div');
        card.className = `postit ${colores[i % colores.length]}`;

        // Posición aleatoria pero controlada para que no se salgan de la pantalla
        const left    = 5 + (i * 18) + Math.random() * 4;
        const topBase = 15 + Math.random() * 40;
        const rotate  = (Math.random() - 0.5) * 8;

        card.style.cssText = `left:${left}%;top:120%;width:220px;transform:rotate(${rotate}deg);`;
        card.innerHTML     = `<p>"${t.texto}"</p><span>— ${t.autor}</span>`;
        stage.appendChild(card);

        // Animación de caída al hacer scroll hasta la sección
        setTimeout(() => { card.style.top = topBase + '%'; }, 300 + i * 150);
    });
}