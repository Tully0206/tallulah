<footer>
    <nav class="footer-nav">
        <a href="/tallulah/pages/reservar.php">Reservar</a>
        <a href="/tallulah/pages/privacidad.php">Política de privacidad</a>
        <a href="/tallulah/pages/aviso-legal.php">Aviso legal</a>
    </nav>

    <div class="footer-big">TALLULAH</div>

    <div class="footer-bottom">
        <span>© Tallulah 2026</span>
        <span class="footer-sep">|</span>
        <span>Ana Martínez</span>
        <span class="footer-sep">|</span>
        <div class="footer-icons">
            <a href="/tallulah/index.php" title="Inicio">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                    <circle cx="9" cy="10" r=".8" fill="currentColor" stroke="none"/>
                    <circle cx="15" cy="10" r=".8" fill="currentColor" stroke="none"/>
                </svg>
            </a>
            <a href="https://instagram.com" target="_blank" title="Instagram">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5"/>
                    <circle cx="12" cy="12" r="4"/>
                    <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
                </svg>
            </a>
        </div>
    </div>
</footer>

<?php
// El JS principal siempre se carga — gestiona el cursor, el menú y animaciones globales
// El JS específico de página solo se carga si la página lo necesita
?>
<script src="/tallulah/js/main.js"></script>

<?php if (isset($js_pagina)): ?>
    <script src="/tallulah/js/<?= htmlspecialchars($js_pagina) ?>"></script>
<?php endif; ?>

</body>
</html>