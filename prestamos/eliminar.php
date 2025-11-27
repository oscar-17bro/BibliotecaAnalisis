<?php
require_once __DIR__ . '/../permiso_admin.php';
require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: listar.php?err=ID inválido"); exit; }
$stmt = $conn->prepare("SELECT libro_id, estado FROM prestamos WHERE idPrestamo = ?"); $stmt->bind_param('i',$id); $stmt->execute(); $res = $stmt->get_result();
if ($res->num_rows === 0) { header("Location: listar.php?err=No encontrado"); exit; }
$p = $res->fetch_assoc(); $stmt->close();
if ($p['estado'] == 'Activo') {
    $u2 = $conn->prepare("UPDATE libros SET estado = 'Disponible' WHERE idLibro = ?"); $u2->bind_param('i', $p['libro_id']); $u2->execute(); $u2->close();
}
$stmt = $conn->prepare("DELETE FROM prestamos WHERE idPrestamo = ?"); $stmt->bind_param('i',$id); $stmt->execute();
header("Location: listar.php?msg=Préstamo eliminado"); exit;
