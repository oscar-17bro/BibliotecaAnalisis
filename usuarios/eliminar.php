<?php
require_once __DIR__ . '/../seguridad.php';

require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: listar.php?err=ID inválido'); exit; }
$stmt = $conn->prepare('DELETE FROM usuarios WHERE idUsuario = ?');
$stmt->bind_param('i',$id);
if ($stmt->execute()) header('Location: listar.php?msg=Usuario eliminado'); else header('Location: listar.php?err=No se pudo eliminar');
exit;
