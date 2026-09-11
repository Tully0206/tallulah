// Muestra u oculta el formulario de edición de una mascota
function toggleEditMascota(id) {
    const form = document.getElementById('edit-mascota-' + id);
    if (!form) return;

    const isVisible = form.style.display === 'block';
    
    // Cierro todos los formularios abiertos antes de abrir uno nuevo
    document.querySelectorAll('.mascota-edit-form').forEach(f => {
        f.style.display = 'none';
    });

    // Si estaba cerrado lo abro, si estaba abierto queda cerrado
    if (!isVisible) {
        form.style.display = 'block';
    }
}