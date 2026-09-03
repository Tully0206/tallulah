<?php
session_start();

// Destruyo la sesión completamente y redirijo al inicio
session_destroy();
header('Location: /tallulah/index.php');
exit();