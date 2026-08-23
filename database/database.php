<?php
// Conexión a la base de datos SQLite3
// Se incluye en todas las páginas que necesitan acceder a datos

$db = new SQLite3(__DIR__ . '/tallulah.db');
$db->enableExceptions(true); // Muestra errores claros si algo falla
$db->exec('PRAGMA foreign_keys = ON'); // Activa las relaciones entre tablas