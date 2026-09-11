<?php
$titulo         = 'Mi perfil';
$css_pagina     = 'usuario.css';
$requiere_login = true;
$es_dashboard   = true;

include('../includes/header.php');
require_once('../database/database.php');

$error = '';
$exito = '';

// Obtengo los datos actuales del cliente
$stmt = $db->prepare('SELECT c.*, u.usu_email
                      FROM clientes c
                      JOIN usuarios u ON c.usu_id = u.usu_id
                      WHERE c.usu_id = :usu_id');
$stmt->bindValue(':usu_id', $_SESSION['usu_id'], SQLITE3_INTEGER);
$cliente = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = htmlspecialchars(trim($_POST['nombre']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));
    $telefono  = htmlspecialchars(trim($_POST['telefono']));
    $direccion = htmlspecialchars(trim($_POST['direccion']));

    if (empty($nombre) || empty($apellidos)) {
        $error = 'El nombre y los apellidos son obligatorios.';
    } else {
        $stmt = $db->prepare('UPDATE clientes
                              SET cli_nombre    = :nombre,
                                  cli_apellidos = :apellidos,
                                  cli_telefono  = :telefono,
                                  cli_direccion = :direccion
                              WHERE usu_id = :usu_id');
        $stmt->bindValue(':nombre',    $nombre,             SQLITE3_TEXT);
        $stmt->bindValue(':apellidos', $apellidos,          SQLITE3_TEXT);
        $stmt->bindValue(':telefono',  $telefono,           SQLITE3_TEXT);
        $stmt->bindValue(':direccion', $direccion,          SQLITE3_TEXT);
        $stmt->bindValue(':usu_id',    $_SESSION['usu_id'], SQLITE3_INTEGER);
        $stmt->execute();

        $exito = 'Datos actualizados correctamente.';

        // Refresco los datos para mostrarlos actualizados
        $stmt = $db->prepare('SELECT c.*, u.usu_email
                              FROM clientes c
                              JOIN usuarios u ON c.usu_id = u.usu_id
                              WHERE c.usu_id = :usu_id');
        $stmt->bindValue(':usu_id', $_SESSION['usu_id'], SQLITE3_INTEGER);
        $cliente = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    }
}

$db->close();
?>

<main class="perfil-main">
    <div class="usuario-wrap">

        <h1 class="page-title">Mi <em>perfil</em></h1>
        <p class="page-sub">Actualiza tus datos personales para que Tallulah pueda contactarte.</p>

        <?php if (!empty($exito)): ?>
            <div class="form-exito"><?= htmlspecialchars($exito) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="form-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="usuario-card">
            <form method="POST" action="perfil.php" style="display:flex;flex-direction:column;gap:20px;">

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre"
                               value="<?= htmlspecialchars($cliente['cli_nombre']) ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos *</label>
                        <input type="text" id="apellidos" name="apellidos"
                               value="<?= htmlspecialchars($cliente['cli_apellidos']) ?>"
                               required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email"
                               value="<?= htmlspecialchars($cliente['usu_email']) ?>"
                               disabled>
                        <span class="form-hint">El email no se puede cambiar desde aquí.</span>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono"
                               placeholder="600 000 000"
                               value="<?= htmlspecialchars($cliente['cli_telefono'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion"
                           placeholder="Calle, número, piso..."
                           value="<?= htmlspecialchars($cliente['cli_direccion'] ?? '') ?>">
                </div>

                <div class="usuario-btns">
                    <a href="/tallulah/pages/dashboard.php" class="btn-hg">Cancelar</a>
                    <button type="submit" class="btn-hg-pink">Guardar cambios →</button>
                </div>

            </form>
        </div>

    </div>
</main>