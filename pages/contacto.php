<?php
$titulo     = 'Contacto';
$css_pagina = 'contacto.css';

include('../includes/header.php');
require_once('../database/database.php');

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = htmlspecialchars(trim($_POST['nombre']));
    $email    = htmlspecialchars(trim($_POST['email']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $mensaje  = htmlspecialchars(trim($_POST['mensaje']));

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $error = 'Por favor rellena todos los campos obligatorios.';
    } else {
        // Guardo el mensaje en la base de datos para verlo desde el panel admin
        $stmt = $db->prepare('INSERT INTO contactos (con_nombre, con_email, con_telefono, con_mensaje)
                              VALUES (:nombre, :email, :telefono, :mensaje)');
        $stmt->bindValue(':nombre',   $nombre,   SQLITE3_TEXT);
        $stmt->bindValue(':email',    $email,    SQLITE3_TEXT);
        $stmt->bindValue(':telefono', $telefono, SQLITE3_TEXT);
        $stmt->bindValue(':mensaje',  $mensaje,  SQLITE3_TEXT);
        $stmt->execute();

        $exito = '¡Mensaje enviado! Tully te responderá en breve.';
    }
}

$db->close();
?>

<main class="contacto-main">
    <div class="contacto-wrap">

        <div class="contacto-info">
            <h1 class="page-title">Hablemos, <em>¿sí?</em></h1>
            <p class="page-sub">¿Tienes alguna pregunta? Escríbeme y te respondo lo antes posible.</p>

            <div class="info-item">
                <span class="info-label">Zona de cobertura</span>
                <span>Baix Guinardó, Barcelona 08025 · radio 1km</span>
            </div>
            <div class="info-item">
                <span class="info-label">WhatsApp</span>
                <a href="https://wa.me/34600000000">+34 600 000 000</a>
            </div>
            <div class="info-item">
                <span class="info-label">Instagram</span>
                <a href="https://instagram.com" target="_blank">@tallulah_bcn</a>
            </div>
        </div>

        <div class="contacto-form-wrap">

            <?php if (!empty($exito)): ?>
                <div class="form-exito"><?= $exito ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="form-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="contacto-card">
                <form method="POST" action="contacto.php" class="contacto-form">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre"
                                   placeholder="Ana"
                                   value="<?= isset($nombre) ? htmlspecialchars($nombre) : '' ?>"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono"
                                   placeholder="700 000 000"
                                   value="<?= isset($telefono) ? htmlspecialchars($telefono) : '' ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email"
                               placeholder="tu@email.com"
                               value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="mensaje">Mensaje *</label>
                        <textarea id="mensaje" name="mensaje" rows="5"
                                  placeholder="Cuéntame en qué puedo ayudarte..."
                                  required><?= isset($mensaje) ? htmlspecialchars($mensaje) : '' ?></textarea>
                    </div>