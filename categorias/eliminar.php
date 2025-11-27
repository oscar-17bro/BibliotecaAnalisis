<?php
require_once __DIR__ . '/../permiso_admin.php';
require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("DELETE FROM categorias WHERE idCategoria = ?");
$stmt->bind_param("i", $id); $stmt->execute();
header("Location: listar.php"); exit;
