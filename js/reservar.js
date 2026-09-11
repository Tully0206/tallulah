// Gestiona el selector de fecha y los slots de hora disponibles

const inputFecha   = document.getElementById('fecha');
const bloqueHora   = document.getElementById('bloqueHora');
const fechaAviso   = document.getElementById('fechaAviso');

inputFecha.addEventListener('change', () => {
    const fecha    = inputFecha.value;
    const diasSemana = new Date(fecha + 'T00:00:00').getDay(); // 0=domingo, 1=lunes...

    // Compruebo si el día de la semana está activo
    if (!DIAS_ACTIVOS.includes(diasSemana)) {
        fechaAviso.textContent = 'Ese día no hay servicio. Por favor elige otro.';
        fechaAviso.className   = 'fecha-aviso fecha-aviso--error';
        bloqueHora.style.display = 'none';
        return;
    }

    // Compruebo si la fecha está bloqueada por la admin
    if (FECHAS_BLOQ.includes(fecha)) {
        fechaAviso.textContent = 'Esa fecha no está disponible. Por favor elige otra.';
        fechaAviso.className   = 'fecha-aviso fecha-aviso--error';
        bloqueHora.style.display = 'none';
        return;
    }

    // La fecha es válida — muestro los slots de hora
    fechaAviso.textContent   = '';
    fechaAviso.className     = 'fecha-aviso';
    bloqueHora.style.display = 'block';

    // Marco los slots ya ocupados para esa fecha
    const horasOcupadas = HORAS_OCUP[fecha] || [];

    document.querySelectorAll('.slot-opt').forEach(slot => {
        const input = slot.querySelector('input');
        const hora  = input.value;

        if (horasOcupadas.includes(hora)) {
            slot.classList.add('slot-ocupado');
            input.disabled = true;
        } else {
            slot.classList.remove('slot-ocupado');
            input.disabled = false;
        }
    });
});