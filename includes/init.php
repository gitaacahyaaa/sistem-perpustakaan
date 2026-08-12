<?php
// Titik masuk bersama: session, koneksi DB, helper functions.
// Semua halaman (kecuali login.php) meng-include includes/auth.php,
// yang otomatis meng-include file ini.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PHP built-in server uses this folder as the web root.
define('BASE_URL', '/');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
