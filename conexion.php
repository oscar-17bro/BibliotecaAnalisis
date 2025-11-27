<?php
require_once __DIR__ . '/configuracion.php';
function obtenerConexion() {
    static $conn;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NOMBRE);
        if ($conn->connect_errno) die("Error de conexión: ".$conn->connect_error);
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}
