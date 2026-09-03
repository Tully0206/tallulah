<?php
$titulo         = 'Crear cuenta';
$css_pagina     = 'auth.css';
$requiere_login = false;

include('../includes/header.php');
require_once('../database/database.php');

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = htmlspecialchars(trim($_POST['nombre']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));
    $email     = htmlspecialchars(trim($_POST['email']));
    $password  = trim($_POST['password']);
    $password2 = trim($_POST['password2']);

    // Verifico que las contraseñas coincidan antes de guardar
    if ($password !== $password2) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // Compruebo que el email no esté ya registrado
        $check = $db->prepare('SELECT usu_id FROM usuarios WHERE usu_email = :email');
        $check->bindValue(':email', $email, SQLITE3_TEXT);
        $existe = $check->execute()->fetchArray();

        if ($existe) {
            $error = 'Ya existe una cuenta con ese email.';
        } else {
            // Cifro la contraseña — nunca se guarda en texto plano
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Creo el usuario
            $stmt = $db->prepare('INSERT INTO usuarios (usu_email, usu_password, usu_rol)
                                  VALUES (:email, :password, :rol)');
            $stmt->bindValue(':email',    $email,  SQLITE3_TEXT);
            $stmt->bindValue(':password', $hash,   SQLITE3_TEXT);
            $stmt->bindValue(':rol',      'cliente', SQLITE3_TEXT);
            $stmt->execute();

            $usu_id = $db->lastInsertRowID();

            // Creo el perfil del cliente vinculado al usuario
            $stmt = $db->prepare('INSERT INTO clientes (usu_id, cli_nombre, cli_apellidos)
                                  VALUES (:usu_id, :nombre, :apellidos)');
            $stmt->bindValue(':usu_id',    $usu_id,    SQLITE3_INTEGER);
            $stmt->bindValue(':nombre',    $nombre,    SQLITE3_TEXT);
            $stmt->bindValue(':apellidos', $apellidos, SQLITE3_TEXT);
            $stmt->execute();

            // Inicio sesión automáticamente tras el registro
            $_SESSION['usu_id']  = $usu_id;
            $_SESSION['usu_rol'] = 'cliente';

            header('Location: /tallulah/pages/dashboard.php');
            exit();
        }
    }
}
?>

<main class="registro-main">
    <div class="registro-card">

        <div class="registro-header">
            <a href="/tallulah/index.php" class="registro-logo">Tallulah</a>
            <span class="registro-sub">Crea tu cuenta</span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="form-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="registro.php" class="registro-form">

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre"
                           placeholder="Ana"
                           required>
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos *</label>
                    <input type="text" id="apellidos" name="apellidos"
                           placeholder="Martínez"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email"
                       placeholder="tu@email.com"
                       required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Contraseña *</label>
                    <input type="password" id="password" name="password"
                           placeholder="Mínimo 6 caracteres"
                           required>
                </div>
                <div class="form-group">
                    <label for="password2">Repite la contraseña *</label>
                    <input type="password" id="password2" name="password2"
                           placeholder="••••••••"
                           required>
                </div>
            </div>

            <button type="submit" class="btn-hg-pink" style="width:100%;justify-content:center;">
                Crear cuenta
            </button>

        </form>

        <div class="registro-footer">
            ¿Ya tienes cuenta? <a href="/tallulah/pages/login.php">Inicia sesión</a>
        </div>

    </div>
</main>

<?php include('../includes/footer.php'); ?>