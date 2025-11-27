<?php
require_once __DIR__ . '/../permiso_bibliotecario.php';
require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: listar.php?err=ID inválido"); exit; }
$stmt = $conn->prepare("SELECT * FROM prestamos WHERE idPrestamo = ?");
$stmt->bind_param('i',$id); $stmt->execute(); $res = $stmt->get_result();
if ($res->num_rows === 0) { header("Location: listar.php?err=Préstamo no encontrado"); exit; }
$p = $res->fetch_assoc(); $stmt->close();
if ($p['estado'] !== 'Activo') { header("Location: listar.php?err=El préstamo no está activo"); exit; }
$hoy = date('Y-m-d'); $fechaDevolucion = $p['fechaDevolucion']; $fechaDevuelto = $hoy; $mult = 0; $diaTarde = 0;
if (strtotime($fechaDevuelto) > strtotime($fechaDevolucion)) {
    $diff = (strtotime($fechaDevuelto) - strtotime($fechaDevolucion));
    $diaTarde = intval($diff / (60*60*24));
    $cfg = $conn->query("SELECT valorMultaDia FROM configuracion LIMIT 1")->fetch_assoc();
    $valor = intval($cfg['valorMultaDia'] ?? 0);
    $mult = $diaTarde * $valor;
}
$u = $conn->prepare("UPDATE prestamos SET fechaDevuelto = ?, multa = ?, estado = 'Finalizado', multaPagada = CASE WHEN ? > 0 THEN 0 ELSE 1 END WHERE idPrestamo = ?");
$u->bind_param('siii', $fechaDevuelto, $mult, $mult, $id); $u->execute(); $u->close();
$u2 = $conn->prepare("UPDATE libros SET estado = 'Disponible' WHERE idLibro = ?"); $u2->bind_param('i', $p['libro_id']); $u2->execute(); $u2->close();
if ($mult > 0) {
    $msg = "Tu préstamo del libro (ID: ".$p['libro_id'].") tiene una multa de $mult por $diaTarde día(s) de retraso.";
    $ins = $conn->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)"); $ins->bind_param('is', $p['usuario_id'], $msg); $ins->execute();
}
header("Location: listar.php?msg=Devolución registrada. Multa: $mult (dias tarde: $diaTarde)");
exit;
