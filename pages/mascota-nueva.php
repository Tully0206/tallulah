<?php
$titulo         = 'Añadir mascota';
$css_pagina     = 'usuario.css';
$requiere_login = true;
$es_dashboard   = true;

include('../includes/header.php');
require_once('../database/database.php');

$error = '';

// Obtengo el cliente logueado para vincular la mascota
$stmt    = $db->prepare('SELECT * FROM clientes WHERE usu_id = :usu_id');
$stmt->bindValue(':usu_id', $_SESSION['usu_id'], SQLITE3_INTEGER);
$cliente = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = htmlspecialchars(trim($_POST['nombre']));
    $especie = htmlspecialchars(trim($_POST['especie']));
    $raza    = htmlspecialchars(trim($_POST['raza']));
    $edad    = (int)$_POST['edad'];
    $notas   = htmlspecialchars(trim($_POST['notas']));

    if (empty($nombre) || empty($especie)) {
        $error = 'El nombre y la especie son obligatorios.';
    } else {
        $stmt = $db->prepare('INSERT INTO mascotas (cli_id, mas_nombre, mas_especie, mas_raza, mas_edad, mas_notas)
                              VALUES (:cli_id, :nombre, :especie, :raza, :edad, :notas)');
        $stmt->bindValue(':cli_id',  $cliente['cli_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':nombre',  $nombre,            SQLITE3_TEXT);
        $stmt->bindValue(':especie', $especie,           SQLITE3_TEXT);
        $stmt->bindValue(':raza',    $raza,              SQLITE3_TEXT);
        $stmt->bindValue(':edad',    $edad,              SQLITE3_INTEGER);
        $stmt->bindValue(':notas',   $notas,             SQLITE3_TEXT);
        $stmt->execute();
        $db->close();
        header('Location: /tallulah/pages/dashboard.php');
        exit();
    }
}

$db->close();
?>

<main class="mascota-main">
    <div class="usuario-wrap">

        <h1 class="page-title">Añadir <em>mascota</em></h1>
        <p class="page-sub">Cuéntame sobre tu peludo para poder cuidarlo mejor.</p>

        <?php if (!empty($error)): ?>
            <div class="form-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="usuario-card">
            <form method="POST" action="mascota-nueva.php" class="mascota-form" style="display:flex;flex-direction:column;gap:20px;">

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre"
                               placeholder="Coco"
                               value="<?= isset($nombre) ? htmlspecialchars($nombre) : '' ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="especie">Especie *</label>
                        <select id="especie" name="especie" required>
                            <option value="">Selecciona</option>
                            <option value="perro" <?= (isset($_POST['especie']) && $_POST['especie']==='perro') ? 'selected' : '' ?>>🐶 Perro</option>
                            <option value="gato"  <?= (isset($_POST['especie']) && $_POST['especie']==='gato')  ? 'selected' : '' ?>>🐱 Gato</option>
                            <option value="otro"  <?= (isset($_POST['especie']) && $_POST['especie']==='otro')  ? 'selected' : '' ?>>🐾 Otro</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="raza">Raza</label>
                        <input type="text" id="raza" name="raza"
                               placeholder="Golden Retriever"
                               value="<?= isset($raza) ? htmlspecialchars($raza) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="edad">Edad (años)</label>
                        <input type="number" id="edad" name="edad"
                               min="0" max="30" placeholder="3"
                               value="<?= isset($edad) ? htmlspecialchars($edad) : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="notas">Notas importantes</label>
                    <textarea id="notas" name="notas" rows="3"
                              placeholder="Alergias, miedos, medicación, comportamiento..."><?= isset($notas) ? htmlspecialchars($notas) : '' ?></textarea>
                </div>

                <div class="usuario-btns">
                    <a href="/tallulah/pages/dashboard.php" class="btn-hg">Cancelar</a>
                    <button type="submit" class="btn-hg-pink">Guardar mascota →</button>
                </div>

            </form>
        </div>

    </div>
</main>