<?php
$titulo         = 'Reservar';
$css_pagina     = 'usuario.css';
$requiere_login = true;
$js_pagina      = 'reservar.js';
$es_dashboard   = true;

include('../includes/header.php');
require_once('../database/database.php');

$error = '';
$exito = '';

// Obtengo los datos del cliente logueado
$stmt    = $db->prepare('SELECT * FROM clientes WHERE usu_id = :usu_id');
$stmt->bindValue(':usu_id', $_SESSION['usu_id'], SQLITE3_INTEGER);
$cliente = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

// Obtengo las mascotas del cliente
$stmt    = $db->prepare('SELECT * FROM mascotas WHERE cli_id = :cli_id');
$stmt->bindValue(':cli_id', $cliente['cli_id'], SQLITE3_INTEGER);
$res_mas  = $stmt->execute();
$mascotas = [];
while ($m = $res_mas->fetchArray(SQLITE3_ASSOC)) {
    $mascotas[] = $m;
}

// Obtengo los servicios disponibles
$res_svc   = $db->query('SELECT * FROM servicios WHERE ser_activo = 1');
$servicios = [];
while ($s = $res_svc->fetchArray(SQLITE3_ASSOC)) {
    $servicios[] = $s;
}

// Obtengo la configuración de horario desde la base de datos
$cfg = [];
$res_cfg = $db->query('SELECT cfg_clave, cfg_valor FROM configuracion');
while ($row = $res_cfg->fetchArray(SQLITE3_ASSOC)) {
    $cfg[$row['cfg_clave']] = $row['cfg_valor'];
}
$horario_inicio = $cfg['horario_inicio'] ?? '09:00';
$horario_fin    = $cfg['horario_fin']    ?? '19:00';
$dias_activos   = array_map('intval', explode(',', $cfg['dias_activos'] ?? '1,2,3,4,5,6'));

// Obtengo las fechas bloqueadas por la admin
$fechas_bloq = [];
$res_fb = $db->query('SELECT fec_fecha FROM fechas_bloqueadas');
while ($row = $res_fb->fetchArray(SQLITE3_ASSOC)) {
    $fechas_bloq[] = $row['fec_fecha'];
}

// Obtengo las horas ya ocupadas por fecha
$ocupadas = [];
$res_ocu  = $db->query("SELECT res_fecha, res_hora FROM reservas WHERE res_estado NOT IN ('cancelada')");
while ($row = $res_ocu->fetchArray(SQLITE3_ASSOC)) {
    $ocupadas[$row['res_fecha']][] = $row['res_hora'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mas_id = (int)$_POST['mas_id'];
    $ser_id = (int)$_POST['ser_id'];
    $fecha  = htmlspecialchars(trim($_POST['fecha']));
    $hora   = htmlspecialchars(trim($_POST['hora']));
    $notas  = htmlspecialchars(trim($_POST['notas']));

    if (empty($mas_id) || empty($ser_id) || empty($fecha) || empty($hora)) {
        $error = 'Por favor rellena todos los campos obligatorios.';
    } elseif (in_array($hora, $ocupadas[$fecha] ?? [])) {
        $error = 'Ese horario ya no está disponible. Por favor elige otro.';
    } else {
        $stmt = $db->prepare('INSERT INTO reservas (cli_id, mas_id, ser_id, res_fecha, res_hora, res_notas)
                              VALUES (:cli_id, :mas_id, :ser_id, :fecha, :hora, :notas)');
        $stmt->bindValue(':cli_id', $cliente['cli_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':mas_id', $mas_id,            SQLITE3_INTEGER);
        $stmt->bindValue(':ser_id', $ser_id,            SQLITE3_INTEGER);
        $stmt->bindValue(':fecha',  $fecha,             SQLITE3_TEXT);
        $stmt->bindValue(':hora',   $hora,              SQLITE3_TEXT);
        $stmt->bindValue(':notas',  $notas,             SQLITE3_TEXT);
        $stmt->execute();

        $exito = 'Reserva enviada correctamente. Tallulah te confirmará por WhatsApp en breve. 🐾';
    }
}

// Genero los slots de hora de 30 en 30 minutos
$slots = [];
$t = strtotime($horario_inicio);
$f = strtotime($horario_fin);
while ($t < $f) {
    $slots[] = date('H:i', $t);
    $t += 1800;
}

// Paso los datos al JS para que bloquee fechas y horas no disponibles
$dias_activos_js = json_encode($dias_activos);
$fechas_bloq_js  = json_encode($fechas_bloq);
$ocupadas_js     = json_encode($ocupadas);

$db->close();
?>

<main class="reservar-main">
    <div class="usuario-wrap">

        <h1 class="page-title">Nueva <em>reserva</em></h1>
        <p class="page-sub">Rellena el formulario y Tallulah te confirmará la reserva por WhatsApp.</p>

        <?php if (!empty($exito)): ?>
            <div class="form-exito"><?= $exito ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="form-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (empty($mascotas)): ?>
            <div class="reservar-sin-mascotas">
                <p>Antes de reservar necesitas añadir al menos una mascota.</p>
                <a href="/tallulah/pages/mascota-nueva.php" class="btn-hg-pink">Añadir mascota →</a>
            </div>
        <?php else: ?>

        <div class="usuario-card">
            <form method="POST" action="reservar.php" style="display:flex;flex-direction:column;gap:20px;">

                <div class="form-row">
                    <div class="form-group">
                        <label for="mas_id">¿Para quién reservas? *</label>
                        <select id="mas_id" name="mas_id" required>
                            <option value="">Selecciona una mascota</option>
                            <?php foreach ($mascotas as $m): ?>
                                <option value="<?= $m['mas_id'] ?>"
                                    <?= (isset($_POST['mas_id']) && $_POST['mas_id'] == $m['mas_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['mas_nombre']) ?> (<?= htmlspecialchars($m['mas_especie']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ser_id">Servicio *</label>
                        <select id="ser_id" name="ser_id" required>
                            <option value="">Selecciona un servicio</option>
                            <?php foreach ($servicios as $s): ?>
                                <option value="<?= $s['ser_id'] ?>"
                                    <?= (isset($_POST['ser_id']) && $_POST['ser_id'] == $s['ser_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['ser_nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fecha">Fecha *</label>
                    <input type="date" id="fecha" name="fecha"
                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                           value="<?= isset($_POST['fecha']) ? htmlspecialchars($_POST['fecha']) : '' ?>"
                           required>
                    <p class="fecha-aviso" id="fechaAviso"></p>
                </div>

                <!-- Los slots de hora aparecen al seleccionar una fecha válida -->
                <div class="form-group" id="bloqueHora" style="display:none">
                    <label>Hora *</label>
                    <div class="slots-grid">
                        <?php foreach ($slots as $slot): ?>
                            <label class="slot-opt" id="slot-<?= str_replace(':', '', $slot) ?>">
                                <input type="radio" name="hora" value="<?= $slot ?>" required>
                                <span><?= $slot ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notas">Notas para Tallulah</label>
                    <textarea id="notas" name="notas" rows="3"
                              placeholder="Instrucciones especiales, alergias, comportamiento..."><?= isset($_POST['notas']) ? htmlspecialchars($_POST['notas']) : '' ?></textarea>
                </div>

                <div class="usuario-btns">
                    <a href="/tallulah/pages/dashboard.php" class="btn-hg">Cancelar</a>
                    <button type="submit" class="btn-hg-pink">Enviar reserva →</button>
                </div>

            </form>
        </div>

        <?php endif; ?>

    </div>
</main>

<script>
    const DIAS_ACTIVOS = <?= $dias_activos_js ?>;
    const FECHAS_BLOQ  = <?= $fechas_bloq_js ?>;
    const HORAS_OCUP   = <?= $ocupadas_js ?>;
</script>