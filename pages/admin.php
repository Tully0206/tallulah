<?php
$titulo     = 'Panel Admin';
$css_pagina = 'admin.css';
$es_admin   = true;

// Solo el admin puede acceder a esta página
if (!isset($_SESSION['usu_id']) || $_SESSION['usu_rol'] !== 'admin') {
    header('Location: /tallulah/pages/login.php');
    exit();
}

include('../includes/header.php');
require_once('../database/database.php');

$msg = '';

// ── Cambio de estado de reserva ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    if ($_POST['accion'] === 'cambiar_estado') {
        $res_id   = (int)$_POST['res_id'];
        $estado   = htmlspecialchars(trim($_POST['estado']));
        $stmt = $db->prepare('UPDATE reservas SET res_estado = :estado WHERE res_id = :id');
        $stmt->bindValue(':estado', $estado, SQLITE3_TEXT);
        $stmt->bindValue(':id',     $res_id, SQLITE3_INTEGER);
        $stmt->execute();
        $msg = 'Estado actualizado correctamente.';
    }

    // ── Edición de cliente ──
    if ($_POST['accion'] === 'editar_cliente') {
        $cli_id    = (int)$_POST['cli_id'];
        $nombre    = htmlspecialchars(trim($_POST['cli_nombre']));
        $apellidos = htmlspecialchars(trim($_POST['cli_apellidos']));
        $telefono  = htmlspecialchars(trim($_POST['cli_telefono']));
        $direccion = htmlspecialchars(trim($_POST['cli_direccion']));
        $stmt = $db->prepare('UPDATE clientes SET cli_nombre=:n, cli_apellidos=:a, cli_telefono=:t, cli_direccion=:d WHERE cli_id=:id');
        $stmt->bindValue(':n',  $nombre,    SQLITE3_TEXT);
        $stmt->bindValue(':a',  $apellidos, SQLITE3_TEXT);
        $stmt->bindValue(':t',  $telefono,  SQLITE3_TEXT);
        $stmt->bindValue(':d',  $direccion, SQLITE3_TEXT);
        $stmt->bindValue(':id', $cli_id,    SQLITE3_INTEGER);
        $stmt->execute();
        $msg = 'Cliente actualizado correctamente.';
    }

    // ── Edición de mascota desde el admin ──
    if ($_POST['accion'] === 'editar_mascota') {
        $mas_id   = (int)$_POST['mas_id'];
        $nombre   = htmlspecialchars(trim($_POST['mas_nombre']));
        $especie  = htmlspecialchars(trim($_POST['mas_especie']));
        $raza     = htmlspecialchars(trim($_POST['mas_raza']));
        $edad     = (int)$_POST['mas_edad'];
        $notas    = htmlspecialchars(trim($_POST['mas_notas']));
        $stmt = $db->prepare('UPDATE mascotas SET mas_nombre=:n, mas_especie=:e, mas_raza=:r, mas_edad=:a, mas_notas=:nt WHERE mas_id=:id');
        $stmt->bindValue(':n',  $nombre,  SQLITE3_TEXT);
        $stmt->bindValue(':e',  $especie, SQLITE3_TEXT);
        $stmt->bindValue(':r',  $raza,    SQLITE3_TEXT);
        $stmt->bindValue(':a',  $edad,    SQLITE3_INTEGER);
        $stmt->bindValue(':nt', $notas,   SQLITE3_TEXT);
        $stmt->bindValue(':id', $mas_id,  SQLITE3_INTEGER);
        $stmt->execute();
        $msg = 'Mascota actualizada correctamente.';
    }

    // ── Marcar mensaje como leído ──
    if ($_POST['accion'] === 'marcar_leido') {
        $con_id = (int)$_POST['con_id'];
        $stmt   = $db->prepare('UPDATE contactos SET con_leido = 1 WHERE con_id = :id');
        $stmt->bindValue(':id', $con_id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    // ── Guardar configuración de horario ──
    if ($_POST['accion'] === 'guardar_horario') {
        $inicio = htmlspecialchars(trim($_POST['horario_inicio']));
        $fin    = htmlspecialchars(trim($_POST['horario_fin']));
        foreach (['horario_inicio' => $inicio, 'horario_fin' => $fin] as $clave => $valor) {
            $stmt = $db->prepare('UPDATE configuracion SET cfg_valor = :v WHERE cfg_clave = :k');
            $stmt->bindValue(':v', $valor,  SQLITE3_TEXT);
            $stmt->bindValue(':k', $clave,  SQLITE3_TEXT);
            $stmt->execute();
        }
        // Días activos
        $dias = isset($_POST['dias']) ? array_map('intval', $_POST['dias']) : [];
        $stmt = $db->prepare('UPDATE configuracion SET cfg_valor = :v WHERE cfg_clave = :k');
        $stmt->bindValue(':v', implode(',', $dias), SQLITE3_TEXT);
        $stmt->bindValue(':k', 'dias_activos',      SQLITE3_TEXT);
        $stmt->execute();
        $msg = 'Horario actualizado correctamente.';
    }

    // ── Añadir fecha bloqueada ──
    if ($_POST['accion'] === 'bloquear_fecha') {
        $fecha = htmlspecialchars(trim($_POST['fecha_bloq']));
        $stmt  = $db->prepare('INSERT OR IGNORE INTO fechas_bloqueadas (fec_fecha) VALUES (:f)');
        $stmt->bindValue(':f', $fecha, SQLITE3_TEXT);
        $stmt->execute();
        $msg = 'Fecha bloqueada correctamente.';
    }

    // ── Eliminar fecha bloqueada ──
    if ($_POST['accion'] === 'desbloquear_fecha') {
        $fecha = htmlspecialchars(trim($_POST['fecha_del']));
        $stmt  = $db->prepare('DELETE FROM fechas_bloqueadas WHERE fec_fecha = :f');
        $stmt->bindValue(':f', $fecha, SQLITE3_TEXT);
        $stmt->execute();
        $msg = 'Fecha desbloqueada correctamente.';
    }
}

// ── Obtengo todos los datos necesarios para el panel ──

// Reservas con datos del cliente y servicio
$reservas = [];
$res = $db->query('SELECT r.*, c.cli_nombre, c.cli_apellidos, c.cli_telefono,
                          m.mas_nombre, m.mas_especie, s.ser_nombre
                   FROM reservas r
                   JOIN clientes  c ON r.cli_id = c.cli_id
                   JOIN mascotas  m ON r.mas_id = m.mas_id
                   JOIN servicios s ON r.ser_id = s.ser_id
                   ORDER BY r.res_fecha DESC, r.res_hora DESC');
while ($row = $res->fetchArray(SQLITE3_ASSOC)) { $reservas[] = $row; }

// KPIs — conteo por estado
$kpis = ['pendiente' => 0, 'confirmada' => 0, 'completada' => 0, 'cancelada' => 0];
foreach ($reservas as $r) {
    if (isset($kpis[$r['res_estado']])) $kpis[$r['res_estado']]++;
}

// Clientes con sus mascotas
$clientes = [];
$res = $db->query('SELECT c.*, u.usu_email FROM clientes c JOIN usuarios u ON c.usu_id = u.usu_id ORDER BY c.cli_nombre');
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $stmtM = $db->prepare('SELECT * FROM mascotas WHERE cli_id = :cid');
    $stmtM->bindValue(':cid', $row['cli_id'], SQLITE3_INTEGER);
    $resM = $stmtM->execute();
    $row['mascotas'] = [];
    while ($m = $resM->fetchArray(SQLITE3_ASSOC)) { $row['mascotas'][] = $m; }
    $clientes[] = $row;
}

// Mensajes de contacto
$mensajes = [];
$res = $db->query('SELECT * FROM contactos ORDER BY con_fecha DESC');
while ($row = $res->fetchArray(SQLITE3_ASSOC)) { $mensajes[] = $row; }
$mensajes_nuevos = count(array_filter($mensajes, fn($m) => !$m['con_leido']));

// Configuración de horario
$cfg = [];
$res = $db->query('SELECT cfg_clave, cfg_valor FROM configuracion');
while ($row = $res->fetchArray(SQLITE3_ASSOC)) { $cfg[$row['cfg_clave']] = $row['cfg_valor']; }
$dias_activos = array_map('intval', explode(',', $cfg['dias_activos'] ?? '1,2,3,4,5,6'));

// Fechas bloqueadas
$fechas_bloq = [];
$res = $db->query('SELECT * FROM fechas_bloqueadas ORDER BY fec_fecha');
while ($row = $res->fetchArray(SQLITE3_ASSOC)) { $fechas_bloq[] = $row; }

$db->close();

$dias_nombres = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
?>

<main class="admin-main">

    <?php if ($msg): ?>
        <div class="adm-flash"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="adm-topbar">
        <div class="adm-greeting">
            <h1>Hola, <em>Tallulah</em></h1>
        </div>
        <a href="/tallulah/pages/logout.php" class="adm-topbar-logout">Cerrar sesión</a>
    </div>

    <!-- KPIs -->
    <div class="adm-kpis">
        <?php foreach ($kpis as $estado => $num): ?>
            <div class="kpi-card kpi-<?= $estado ?>">
                <div class="kpi-dot"></div>
                <span class="kpi-num"><?= $num ?></span>
                <span class="kpi-label"><?= ucfirst($estado) ?>s</span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabs -->
    <div class="adm-tabs">
        <button class="tab-btn active" onclick="switchTab('reservas')">
            Reservas
            <?php if ($kpis['pendiente'] > 0): ?>
                <span class="tab-badge"><?= $kpis['pendiente'] ?></span>
            <?php endif; ?>
        </button>
        <button class="tab-btn" onclick="switchTab('clientes')">Clientes</button>
        <button class="tab-btn" onclick="switchTab('mensajes')">
            Mensajes
            <?php if ($mensajes_nuevos > 0): ?>
                <span class="tab-badge"><?= $mensajes_nuevos ?></span>
            <?php endif; ?>
        </button>
        <button class="tab-btn" onclick="switchTab('horario')">Horario</button>
    </div>

    <div class="adm-body">

        <!-- Tab reservas -->
        <div class="tab-content active" id="tab-reservas">
            <?php if (empty($reservas)): ?>
                <p class="admin-empty">No hay reservas todavía.</p>
            <?php else: ?>
                <div class="tabla-wrap">
                    <table class="admin-tabla">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Mascota</th>
                                <th>Especie</th>
                                <th>Servicio</th>
                                <th>Estado</th>
                                <th>Cambiar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['res_fecha']) ?></td>
                                    <td><strong><?= htmlspecialchars($r['res_hora']) ?></strong></td>
                                    <td><strong><?= htmlspecialchars($r['cli_nombre'] . ' ' . $r['cli_apellidos']) ?></strong></td>
                                    <td><a href="tel:<?= htmlspecialchars($r['cli_telefono']) ?>"><?= htmlspecialchars($r['cli_telefono']) ?></a></td>
                                    <td><?= htmlspecialchars($r['mas_nombre']) ?></td>
                                    <td><?= htmlspecialchars($r['mas_especie']) ?></td>
                                    <td><?= htmlspecialchars($r['ser_nombre']) ?></td>
                                    <td>
                                        <span class="estado-badge estado-<?= $r['res_estado'] ?>">
                                            <?= htmlspecialchars($r['res_estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="form-accion">
                                            <input type="hidden" name="accion" value="cambiar_estado">
                                            <input type="hidden" name="res_id" value="<?= $r['res_id'] ?>">
                                            <select name="estado" onchange="this.form.submit()">
                                                <option value="">Cambiar...</option>
                                                <option value="pendiente">Pendiente</option>
                                                <option value="confirmada">Confirmada</option>
                                                <option value="completada">Completada</option>
                                                <option value="cancelada">Cancelada</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab clientes -->
        <div class="tab-content" id="tab-clientes">
            <?php if (empty($clientes)): ?>
                <p class="admin-empty">No hay clientes registrados todavía.</p>
            <?php else: ?>
                <?php foreach ($clientes as $c): ?>
                    <details class="cliente-card-exp">
                        <summary>
                            <strong><?= htmlspecialchars($c['cli_nombre'] . ' ' . $c['cli_apellidos']) ?></strong>
                            <span class="tabla-sub"><?= htmlspecialchars($c['usu_email']) ?></span>
                        </summary>

                        <!-- Editar datos del cliente -->
                        <div class="exp-section">
                            <div class="exp-title">Datos del cliente</div>
                            <form method="POST" class="exp-form">
                                <input type="hidden" name="accion"  value="editar_cliente">
                                <input type="hidden" name="cli_id"  value="<?= $c['cli_id'] ?>">
                                <div class="exp-row">
                                    <div class="exp-group">
                                        <label>Nombre</label>
                                        <input type="text" name="cli_nombre" value="<?= htmlspecialchars($c['cli_nombre']) ?>">
                                    </div>
                                    <div class="exp-group">
                                        <label>Apellidos</label>
                                        <input type="text" name="cli_apellidos" value="<?= htmlspecialchars($c['cli_apellidos']) ?>">
                                    </div>
                                    <div class="exp-group">
                                        <label>Teléfono</label>
                                        <input type="text" name="cli_telefono" value="<?= htmlspecialchars($c['cli_telefono'] ?? '') ?>">
                                    </div>
                                    <div class="exp-group">
                                        <label>Dirección</label>
                                        <input type="text" name="cli_direccion" value="<?= htmlspecialchars($c['cli_direccion'] ?? '') ?>">
                                    </div>
                                </div>
                                <button type="submit" class="btn-exp-save">Guardar</button>
                            </form>
                        </div>

                        <!-- Mascotas del cliente -->
                        <?php foreach ($c['mascotas'] as $m): ?>
                            <div class="exp-section exp-mascota">
                                <div class="exp-title">🐾 <?= htmlspecialchars($m['mas_nombre']) ?></div>
                                <form method="POST" class="exp-form">
                                    <input type="hidden" name="accion"  value="editar_mascota">
                                    <input type="hidden" name="mas_id"  value="<?= $m['mas_id'] ?>">
                                    <div class="exp-row">
                                        <div class="exp-group">
                                            <label>Nombre</label>
                                            <input type="text" name="mas_nombre" value="<?= htmlspecialchars($m['mas_nombre']) ?>">
                                        </div>
                                        <div class="exp-group">
                                            <label>Especie</label>
                                            <select name="mas_especie">
                                                <option value="perro" <?= $m['mas_especie']==='perro'?'selected':'' ?>>Perro</option>
                                                <option value="gato"  <?= $m['mas_especie']==='gato' ?'selected':'' ?>>Gato</option>
                                                <option value="otro"  <?= $m['mas_especie']==='otro' ?'selected':'' ?>>Otro</option>
                                            </select>
                                        </div>
                                        <div class="exp-group">
                                            <label>Raza</label>
                                            <input type="text" name="mas_raza" value="<?= htmlspecialchars($m['mas_raza'] ?? '') ?>">
                                        </div>
                                        <div class="exp-group">
                                            <label>Edad</label>
                                            <input type="number" name="mas_edad" min="0" max="30" value="<?= (int)($m['mas_edad'] ?? 0) ?>">
                                        </div>
                                    </div>
                                    <div class="exp-group">
                                        <label>Notas</label>
                                        <textarea name="mas_notas" rows="2"><?= htmlspecialchars($m['mas_notas'] ?? '') ?></textarea>
                                    </div>
                                    <button type="submit" class="btn-exp-save">Guardar</button>
                                </form>
                            </div>
                        <?php endforeach; ?>

                    </details>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Tab mensajes -->
        <div class="tab-content" id="tab-mensajes">
            <?php if (empty($mensajes)): ?>
                <p class="admin-empty">No hay mensajes todavía.</p>
            <?php else: ?>
                <div class="mensajes-lista">
                    <?php foreach ($mensajes as $m): ?>
                        <div class="mensaje-card <?= !$m['con_leido'] ? 'msg-nuevo' : '' ?>">
                            <div class="msg-top">
                                <div class="msg-quien">
                                    <strong><?= htmlspecialchars($m['con_nombre']) ?></strong>
                                    <a href="mailto:<?= htmlspecialchars($m['con_email']) ?>"><?= htmlspecialchars($m['con_email']) ?></a>
                                </div>
                                <div class="msg-meta">
                                    <small><?= htmlspecialchars($m['con_fecha'] ?? '') ?></small>
                                    <?php if (!$m['con_leido']): ?>
                                        <form method="POST" style="display:inline">
                                            <input type="hidden" name="accion" value="marcar_leido">
                                            <input type="hidden" name="con_id" value="<?= $m['con_id'] ?>">
                                            <button type="submit" class="btn-msg-leido">Marcar como leído</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="msg-texto"><?= htmlspecialchars($m['con_mensaje']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab horario -->
        <div class="tab-content" id="tab-horario">
            <div class="horario-grid">

                <!-- Configuración de horario -->
                <div class="horario-bloque">
                    <h3>Horario y días activos</h3>
                    <form method="POST" style="display:flex;flex-direction:column;gap:16px;">
                        <input type="hidden" name="accion" value="guardar_horario">
                        <div class="exp-group">
                            <label>Hora de inicio</label>
                            <input type="time" name="horario_inicio" value="<?= htmlspecialchars($cfg['horario_inicio'] ?? '09:00') ?>">
                        </div>
                        <div class="exp-group">
                            <label>Hora de fin</label>
                            <input type="time" name="horario_fin" value="<?= htmlspecialchars($cfg['horario_fin'] ?? '19:00') ?>">
                        </div>
                        <div class="exp-group">
                            <label>Días activos</label>
                            <div class="dias-wrap">
                                <?php foreach ($dias_nombres as $i => $nombre): ?>
                                    <label class="dia-chip">
                                        <input type="checkbox" name="dias[]" value="<?= $i ?>"
                                               <?= in_array($i, $dias_activos) ? 'checked' : '' ?>>
                                        <?= $nombre ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn-exp-save">Guardar horario</button>
                    </form>
                </div>

                <!-- Fechas bloqueadas -->
                <div class="horario-bloque">
                    <h3>Fechas bloqueadas</h3>
                    <form method="POST" style="display:flex;gap:10px;align-items:flex-end;">
                        <input type="hidden" name="accion" value="bloquear_fecha">
                        <div class="exp-group" style="flex:1">
                            <label>Bloquear fecha</label>
                            <input type="date" name="fecha_bloq" min="<?= date('Y-m-d') ?>">
                        </div>
                        <button type="submit" class="btn-exp-save">Bloquear</button>
                    </form>
                    <div class="fechas-lista">
                        <?php if (empty($fechas_bloq)): ?>
                            <p class="admin-empty">No hay fechas bloqueadas.</p>
                        <?php else: ?>
                            <?php foreach ($fechas_bloq as $fb): ?>
                                <div class="fecha-fila">
                                    <span><?= htmlspecialchars($fb['fec_fecha']) ?></span>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="accion"    value="desbloquear_fecha">
                                        <input type="hidden" name="fecha_del" value="<?= htmlspecialchars($fb['fec_fecha']) ?>">
                                        <button type="submit" class="btn-fecha-del">✕</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </div>

</main>