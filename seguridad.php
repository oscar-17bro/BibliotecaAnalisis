<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/configuracion.php';
// Si NO hay sesión → redirigir a login
if (!isset($_SESSION['usuario'])) {
    header("Location: " . URL_BASE . "/usuarios/login.php");
    exit;
}
