<?php
session_start();

// Protección de rutas — redirige al login si la página requiere autenticación
if (isset($requiere_login) && $requiere_login === true) {
    if (!isset($_SESSION['usu_id'])) {
        header('Location: /tallulah/pages/login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titulo) ? htmlspecialchars($titulo) . ' — Tallulah' : 'Tallulah — Cuidado con amor' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Caveat:wght@700&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/tallulah/css/style.css">

    <!-- Cada página puede definir $css_pagina para cargar su CSS específico -->
    <?php if (isset($css_pagina)): ?>
        <link rel="stylesheet" href="/tallulah/css/<?= htmlspecialchars($css_pagina) ?>">
    <?php endif; ?>
</head>
<body>

<?php
// El cursor, la barra superior y el botón de contacto
// solo tienen sentido en páginas públicas, no en el área privada
if ((!isset($es_admin) || !$es_admin) && (!isset($es_dashboard) || !$es_dashboard)):
?>
    <div class="cursor" id="cursor"></div>
    <div class="cursor-ring" id="cursorRing"></div>

    <a href="https://wa.me/34600000000" class="side-contact">Contáctame</a>

    <div class="top-bar">
        <div></div>
        <div class="top-bar-center">✨ Reservando para abril 2026 ✨</div>
        <button class="hamburger" onclick="toggleMenu()" aria-label="Abrir menú">
            <span></span><span></span><span></span>
        </button>
    </div>

<?php endif; ?>

<nav>
    <ul class="nav-links">
        <li><a href="/tallulah/index.php#sobre-mi">Sobre mí</a></li>
        <li>
            <a href="/tallulah/index.php#servicios">Servicios</a>
            <div class="nav-dropdown">
                <a href="/tallulah/index.php?svc=0#servicios">Paseo de perros</a>
                <a href="/tallulah/index.php?svc=1#servicios">Visita a domicilio</a>
                <a href="/tallulah/index.php?svc=2#servicios">Cuidado a domicilio</a>
                <a href="/tallulah/index.php?svc=3#servicios">Extras a medida</a>
            </div>
        </li>
        <li><a href="/tallulah/index.php#testimonios">Testimonios</a></li>
        <li><a href="/tallulah/pages/contacto.php">Contacto</a></li>
    </ul>

    <a href="/tallulah/index.php" class="nav-logo">Tallulah</a>

    <div class="nav-right">

        <div class="lang-selector">
            <button class="lang-btn" title="Idioma">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                    <circle cx="9" cy="10" r=".8" fill="currentColor" stroke="none"/>
                    <circle cx="15" cy="10" r=".8" fill="currentColor" stroke="none"/>
                </svg>
            </button>
            <div class="lang-dropdown">
                <div class="lang-header">Idioma / Language</div>
                <a href="#" class="lang-option active" data-lang="es">Español</a>
                <a href="#" class="lang-option" data-lang="en">English</a>
                <a href="#" class="lang-option" data-lang="ca">Català</a>
            </div>
        </div>

        <div class="nav-icon-wrap">
            <div class="icon-label">Follow me</div>
            <a href="https://instagram.com" target="_blank" title="Instagram">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5"/>
                    <circle cx="12" cy="12" r="4"/>
                    <circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none"/>
                </svg>
            </a>
        </div>

        <?php
        // Muestra botones diferentes según el estado de sesión y el rol del usuario
        if (isset($_SESSION['usu_id'])): ?>
            <?php if ($_SESSION['usu_rol'] === 'admin'): ?>
                <a href="/tallulah/pages/admin.php" class="btn-hg">Admin</a>
            <?php elseif (!isset($es_dashboard) || !$es_dashboard): ?>
                <a href="/tallulah/pages/dashboard.php" class="btn-hg">Mi cuenta</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="/tallulah/pages/login.php" class="btn-hg">Login</a>
        <?php endif; ?>

    </div>
</nav>

<div class="menu-overlay" id="menuOverlay">
    <button class="menu-close" onclick="toggleMenu()">✕</button>
    <div class="menu-left">
        <a href="/tallulah/index.php#sobre-mi"    class="menu-link" onclick="toggleMenu()">Sobre mí</a>
        <a href="/tallulah/index.php#servicios"   class="menu-link" onclick="toggleMenu()">Servicios</a>
        <a href="/tallulah/index.php#testimonios" class="menu-link" onclick="toggleMenu()">Testimonios</a>
        <a href="/tallulah/pages/contacto.php"    class="menu-link" onclick="toggleMenu()">Contacto</a>
        <a href="/tallulah/pages/reservar.php" class="btn-hg-pink" onclick="toggleMenu()" style="margin-top:28px;width:fit-content;">Reservar →</a>
        <div class="menu-brand">Tallulah</div>
    </div>
    <div class="menu-right">
        <img src="/tallulah/img/tres.jpeg" alt="Tallulah" class="menu-foto">
    </div>
</div>