// Gestiona los tabs del panel de administración
function switchTab(tab) {

    // Desactivo todos los tabs y contenidos
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    // Activo el tab seleccionado
    document.getElementById('tab-' + tab).classList.add('active');

    // Activo el botón correspondiente
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.getAttribute('onclick') === `switchTab('${tab}')`) {
            btn.classList.add('active');
        }
    });
}