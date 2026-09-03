<?php
$titulo        = 'Iniciar sesión';
$css_pagina    = 'auth.css';
$requiere_login = false;

include('../includes/header.php');
require_once('../database/database.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Busco el usuario por email
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE usu_email = :email');
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    // Verifico la contraseña con password_verify — nunca se guarda en texto plano
    if ($row && password_verify($password, $row['usu_password'])) {
        $_SESSION['usu_id']  = $row['usu_id'];
        $_SESSION['usu_rol'] = $row['usu_rol'];

        // Redirijo según el rol del usuario
        if ($row['usu_rol'] === 'admin') {
            header('Location: /tallulah/pages/admin.php');
        } else {
            header('Location: /tallulah/pages/dashboard.php');
        }
        exit();
    } else {
        $error = 'Email o contraseña incorrectos.';
    }
}
?>

<main class="login-main">
    <div class="login-card">

        <div class="login-header">
            <a href="/tallulah/index.php" class="login-logo">Tallulah</a>
            <span class="login-sub">Accede a tu cuenta</span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="form-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email"
                       placeholder="tu@email.com"
                       required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••"
                       required>
            </div>
            <button type="submit" class="btn-hg-pink" style="width:100%;justify-content:center;">
                Entrar
            </button>
        </form>

        <div class="login-footer">
            ¿No tienes cuenta? <a href="/tallulah/pages/registro.php">Regístrate</a>
        </div>

    </div>
</main>

<?php include('../includes/footer.php'); ?>