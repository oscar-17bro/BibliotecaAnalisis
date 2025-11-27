<?php
require_once __DIR__ . '/../seguridad.php';

require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: listar.php?err=ID inválido'); exit; }
$stmt = $conn->prepare('SELECT portada FROM libros WHERE idLibro = ?'); $stmt->bind_param('i',$id); $stmt->execute(); $res = $stmt->get_result(); $portada = null;
if ($r = $res->fetch_assoc()) $portada = $r['portada']; $stmt->close();
$stmt = $conn->prepare('DELETE FROM libros WHERE idLibro = ?'); $stmt->bind_param('i',$id);
if ($stmt->execute()) { if ($portada && file_exists(__DIR__ . '/../recursos/imagenes/'.$portada)) @unlink(__DIR__ . '/../recursos/imagenes/'.$portada); header('Location: listar.php?msg=Libro eliminado'); } else header('Location: listar.php?err=No se pudo eliminar');
exit;
