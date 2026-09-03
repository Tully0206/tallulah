<?php
$titulo         = 'Mi cuenta';
$css_pagina     = 'dashboard.css';
$requiere_login = true;
$js_pagina      = 'dashboard.js';
$es_dashboard   = true;

include('../includes/header.php');
require_once('../database/database.php');

$msg = '';

// Proceso la edición de mascota si viene por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar_mascota') {
    $mas_id      = (int)$_POST['mas_id'];
    $mas_nombre  = htmlspecialchars(trim($_POST['mas_nombre']));
    $mas_especie = htmlspecialchars(trim($_POST['mas_especie']));
    $mas_raza    = htmlspecialchars(trim($_POST['mas_raza']));
    $mas_edad    = (int)$_POST['mas_edad'];
    $mas_notas   = htmlspecialchars(trim($_POST['mas_notas']));

    // Verifico que la mascota pertenece al cliente logueado — seguridad
    $stmtCli = $db->prepare('SELECT cli_id FROM clientes WHERE usu_id = :uid');
    $stmtCli->bindValue(':uid', $_SESSION['usu_id'], SQLITE3_INTEGER);
    $clienteTemp = $stmtCli->execute()->fetchArray(SQLITE3_ASSOC);

    $check = $db->prepare('SELECT mas_id FROM mascotas WHERE mas_id = :mid AND cli_id = :cid');
    $check->bindValue(':mid', $mas_id,                SQLITE3_INTEGER);
    $check->bindValue(':cid', $clienteTemp['cli_id'], SQLITE3_INTEGER);

    if ($check->execute()->fetchArray()) {
        $stmt = $db->prepare('UPDATE mascotas SET mas_nombre=:n, mas_especie=:e, mas_raza=:r, mas_edad=:a, mas_notas=:nt WHERE mas_id=:id');
        $stmt->bindValue(':n',  $mas_nombre,  SQLITE3_TEXT);
        $stmt->bindValue(':e',  $mas_especie, SQLITE3_TEXT);
        $stmt->bindValue(':r',  $mas_raza,    SQLITE3_TEXT);
        $stmt->bindValue(':a',  $mas_edad,    SQLITE3_INTEGER);
        $stmt->bindValue(':nt', $mas_notas,   SQLITE3_TEXT);
        $stmt->bindValue(':id', $mas_id,      SQLITE3_INTEGER);
        $stmt->execute();
        $msg = 'Mascota actualizada correctamente.';
    }
}

// Obtengo los datos del cliente logueado
$stmt = $db->prepare('SELECT c.*, u.usu_email
                      FROM clientes c
                      JOIN usuarios u ON c.usu_id = u.usu_id
                      WHERE c.usu_id = :usu_id');
$stmt->bindValue(':usu_id', $_SESSION['usu_id'], SQLITE3_INTEGER);
$cliente = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

// Obtengo las mascotas del cliente
$stmt = $db->prepare('SELECT * FROM mascotas WHERE cli_id = :cli_id ORDER BY mas_nombre');
$stmt->bindValue(':cli_id', $cliente['cli_id'], SQLITE3_INTEGER);
$res_mas  = $stmt->execute();
$mascotas = [];
while ($m = $res_mas->fetchArray(SQLITE3_ASSOC)) {
    $mascotas[] = $m;
}

// Obtengo las reservas del cliente
$stmt = $db->prepare('SELECT r.*, s.ser_nombre
                      FROM reservas r
                      JOIN servicios s ON r.ser_id = s.ser_id
                      WHERE r.cli_id = :cli_id
                      ORDER BY r.res_fecha DESC');
$stmt->bindValue(':cli_id', $cliente['cli_id'], SQLITE3_INTEGER);
$res_rsv  = $stmt->execute();
$reservas = [];
while ($r = $res_rsv->fetchArray(SQLITE3_ASSOC)) {
    $reservas[] = $r;
}

$db->close();
?>

<main class="dashboard-main">

    <div class="dashboard-hero">
        <div class="dashboard-hero-inner">
            <h1>Hola, <em><?= htmlspecialchars($cliente['cli_nombre']) ?></em></h1>
            <p>Bienvenida a tu espacio. Aquí puedes gestionar tus mascotas y ver tus reservas.</p>
        </div>
        <a href="/tallulah/pages/logout.php" class="dash-logout-btn">Cerrar sesión</a>
    </div>

    <?php if ($msg): ?>
        <div class="admin-msg"><?= $msg ?></div>
    <?php endif; ?>

    <div class="dashboard-grid">

        <!-- Mis mascotas -->
        <section class="dash-card">
            <div class="dash-card-header">
                <h2>Mis mascotas</h2>
                <a href="/tallulah/pages/mascota-nueva.php" class="btn-hg">+ Añadir</a>
            </div>

            <?php if (empty($mascotas)): ?>
                <p class="dash-empty">Todavía no tienes mascotas registradas.</p>
            <?php else: ?>
                <div class="mascotas-list">
                    <?php foreach ($mascotas as $m): ?>
                        <div class="mascota-item">
                            <div class="mascota-info">
                                <strong><?= htmlspecialchars($m['mas_nombre']) ?></strong>
                                <span><?= htmlspecialchars($m['mas_especie']) ?>
                                    <?= $m['mas_raza'] ? '· ' . htmlspecialchars($m['mas_raza']) : '' ?>
                                </span>
                            </div>
                            <button class="btn-hg btn-editar-mascota"
                                    onclick="toggleEditMascota(<?= $m['mas_id'] ?>)">
                                Editar
                            </button>
                        </div>

                        <!-- Formulario de edición inline — oculto por defecto -->
                        <div class="mascota-edit-form" id="edit-mascota-<?= $m['mas_id'] ?>">
                            <form method="POST" action="dashboard.php" class="exp-form">
                                <input type="hidden" name="accion"  value="editar_mascota">
                                <input type="hidden" name="mas_id"  value="<?= $m['mas_id'] ?>">
                                <div class="exp-row">
                                    <div class="exp-group">
                                        <label>Nombre</label>
                                        <input type="text" name="mas_nombre"
                                               value="<?= htmlspecialchars($m['mas_nombre']) ?>" required>
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
                                        <input type="text" name="mas_raza"
                                               value="<?= htmlspecialchars($m['mas_raza'] ?? '') ?>">
                                    </div>
                                    <div class="exp-group">
                                        <label>Edad (años)</label>
                                        <input type="number" name="mas_edad" min="0" max="30"
                                               value="<?= (int)($m['mas_edad'] ?? 0) ?>">
                                    </div>
                                </div>
                                <div class="exp-group">
                                    <label>Notas</label>
                                    <textarea name="mas_notas" rows="2"><?= htmlspecialchars($m['mas_notas'] ?? '') ?></textarea>
                                </div>
                                <div class="edit-mascota-actions">
                                    <button type="submit" class="btn-exp-save">Guardar</button>
                                    <button type="button" class="btn-hg"
                                            onclick="toggleEditMascota(<?= $m['mas_id'] ?>)">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Mis reservas -->
        <section class="dash-card">
            <div class="dash-card-header">
                <h2>Mis reservas</h2>
                <a href="/tallulah/pages/reservar.php" class="btn-hg">+ Nueva</a>
            </div>

            <?php if (empty($reservas)): ?>
                <p class="dash-empty">Todavía no tienes reservas.</p>
            <?php else: ?>
                <div class="reservas-list">
                    <?php foreach ($reservas as $r): ?>
                        <div class="reserva-item">
                            <div class="reserva-estado estado-<?= $r['res_estado'] ?>">
                                <?= htmlspecialchars($r['res_estado']) ?>
                            </div>
                            <div class="reserva-info">
                                <strong><?= htmlspecialchars($r['ser_nombre']) ?></strong>
                                <span><?= htmlspecialchars($r['res_fecha']) ?> · <?= htmlspecialchars($r['res_hora']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Mis datos -->
        <section class="dash-card">
            <div class="dash-card-header">
                <h2>Mis datos</h2>
                <a href="/tallulah/pages/perfil.php" class="btn-hg">Editar</a>
            </div>
            <div class="datos-list">
                <div class="dato-item">
                    <span class="dato-label">Email</span>
                    <span><?= htmlspecialchars($cliente['usu_email']) ?></span>
                </div>
                <div class="dato-item">
                    <span class="dato-label">Teléfono</span>
                    <span><?= $cliente['cli_telefono'] ? htmlspecialchars($cliente['cli_telefono']) : '—' ?></span>
                </div>
                <div class="dato-item">
                    <span class="dato-label">Dirección</span>
                    <span><?= $cliente['cli_direccion'] ? htmlspecialchars($cliente['cli_direccion']) : '—' ?></span>
                </div>
            </div>
        </section>

    </div>

</main>