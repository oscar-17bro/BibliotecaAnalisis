<?php
require_once __DIR__ . '/seguridad.php';
$rol = $_SESSION['usuario']['rol_id'] ?? 0;
if ($rol != 1) {
    header("Location: " . URL_BASE . "/no_autorizado.php");
    exit;
}
