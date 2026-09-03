<?php
$titulo    = 'Inicio';
$js_pagina = 'home.js';

// index.php está en la raíz del proyecto — los includes van sin pages/
include('includes/header.php');
?>

<!-- Panel 1 — Hero -->
<div class="panel p1">
    <div class="panel-fill">
        <div class="hero-bg" style="background-image: url('/tallulah/img/tully.jpg');"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Tallulah</h1>
            <div class="hero-btns">
                <a href="/tallulah/pages/reservar.php" class="btn-hg-pink">Reservar</a>
                <a href="https://wa.me/34600000000"    class="btn-hg">Chat</a>
            </div>
        </div>
    </div>
</div>
<div class="spacer"></div>

<!-- Panel 2 — Sobre mí -->
<div class="panel p2" id="sobre-mi">
    <div class="wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,120 L0,65 C100,5 260,0 440,50 C620,100 700,122 900,64 C1060,18 1180,0 1340,42 C1390,56 1425,72 1440,62 L1440,120 Z" fill="#fffcf2"/>
        </svg>
    </div>
    <div class="panel-fill" style="background:#fffcf2;">
        <div class="sobre-mi-inner">
            <div class="sobre-mi-photo">
                <div class="slideshow">
                    <div class="slide active" style="background-image: url('/tallulah/img/uno.jpeg');"></div>
                    <div class="slide"        style="background-image: url('/tallulah/img/dos.jpeg');"></div>
                    <div class="slide"        style="background-image: url('/tallulah/img/tres.jpeg');"></div>
                    <div class="slide"        style="background-image: url('/tallulah/img/cuatro.jpeg');"></div>
                    <div class="slide-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>

            <div class="sobre-mi-text">
                <span class="section-label">Sobre mí</span>
                <h2>Soy <em>Ana</em>, tu cuidadora de confianza.</h2>
                <p>Acabo de terminar mi carrera de Diseño de Aplicaciones Web y esta página la he creado yo misma, con mucho cariño. 😄
                La idea surgió gracias a Tully, mi teckel. Mi novio me la regaló en un momento muy duro para mí, y desde entonces no me imagino la vida sin ella.</p>
                <p>Sé lo que es dejar a tu peludito con alguien y necesitar saber que está bien. Eso es exactamente lo que quiero ser para ti — alguien de confianza, cercana, y que lo va a tratar como si fuera el suyo.</p>
                <a href="/tallulah/pages/reservar.php" class="btn-hg-pink" style="width:fit-content;margin-top:8px;">Reservar</a>
            </div>
        </div>
    </div>
</div>
<div class="spacer"></div>

<!-- Panel 3 — Servicios -->
<div class="panel p3" id="servicios">
    <div class="wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,120 L0,68 C120,8 280,0 460,48 C640,96 720,120 920,66 C1080,24 1200,2 1360,40 C1400,50 1428,64 1440,58 L1440,120 Z" fill="#F9B4ED"/>
        </svg>
    </div>
    <div class="svc-panel-fill">

        <div class="svc-header-section">
            <h2>Los <em>Servicios</em> de Tallulah</h2>
            <p>Cuidado personalizado para tu mascota en el barrio del Guinardó y alrededores. ¿No encuentras lo que buscas? Escríbeme por WhatsApp.</p>
        </div>

        <div class="svc-grid">
            <div class="svc-rows">

                <div class="svc-row" id="svc-0" onclick="toggleSvc(0)">
                    <div class="svc-row-header">
                        <span class="svc-row-title">Paseo de perros</span>
                        <div class="svc-row-icon">+</div>
                    </div>
                    <div class="svc-row-body">
                        <div class="svc-row-body-inner">
                            1 hora de paseo o juego en tu barrio. Voy a tu casa, recojo a tu perrito y lo traigo de vuelta feliz y cansado.
                            <br><a href="/tallulah/pages/reservar.php">Reservar este servicio →</a>
                        </div>
                    </div>
                </div>

                <div class="svc-row" id="svc-1" onclick="toggleSvc(1)">
                    <div class="svc-row-header">
                        <span class="svc-row-title">Visita a domicilio</span>
                        <div class="svc-row-icon">+</div>
                    </div>
                    <div class="svc-row-body">
                        <div class="svc-row-body-inner">
                            Voy a tu casa a cuidar, jugar y acompañar a tu mascota. Ideal para días de trabajo largo.
                            <br><a href="/tallulah/pages/reservar.php">Reservar este servicio →</a>
                        </div>
                    </div>
                </div>

                <div class="svc-row" id="svc-2" onclick="toggleSvc(2)">
                    <div class="svc-row-header">
                        <span class="svc-row-title">Cuidado a domicilio</span>
                        <div class="svc-row-icon">+</div>
                    </div>
                    <div class="svc-row-body">
                        <div class="svc-row-body-inner">
                            Me quedo en tu casa para que tu mascota mantenga su rutina sin estrés.
                            <br><a href="/tallulah/pages/reservar.php">Reservar este servicio →</a>
                        </div>
                    </div>
                </div>

                <div class="svc-row" id="svc-3" onclick="toggleSvc(3)">
                    <div class="svc-row-header">
                        <span class="svc-row-title">Extras a medida</span>
                        <div class="svc-row-icon">+</div>
                    </div>
                    <div class="svc-row-body">
                        <div class="svc-row-body-inner">
                            Llevar a la peluquería, recoger de la guardería o algo personalizado. Hablamos por WhatsApp.
                            <br><a href="https://wa.me/34600000000">Consultar por WhatsApp →</a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="svc-img-col">
                <img src="/tallulah/img/tres.jpeg" alt="Ana cuidando mascotas" class="svc-img">
            </div>
        </div>

    </div>
</div>
<div class="spacer"></div>

<!-- Panel 4 — Testimonios -->
<div id="testiAnchor"></div>
<div class="panel p4" id="testimonios">
    <div class="wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,120 L0,70 C140,10 300,0 480,46 C660,92 740,118 940,62 C1100,18 1220,0 1380,40 C1412,50 1432,62 1440,55 L1440,120 Z" fill="#CAF0F8"/>
        </svg>
    </div>
    <div class="panel-fill testi-panel-fill">
        <div class="testi-wrap">
            <div class="testi-header">
                <span class="testi-overline">Lo que dicen mis clientes</span>
                <h2 class="testi-title">Tu perrito lo <em>pasó genial</em> con Tallulah.</h2>
                <p class="testi-subtitle">No te lo digo yo — te lo cuentan ellos.</p>
            </div>
            <div class="cards-stage" id="cardsStage"></div>
        </div>
    </div>
</div>
<div class="spacer"></div>
<div class="spacer"></div>
<div class="spacer"></div>
<div class="spacer"></div>

<?php include('includes/footer.php'); ?>