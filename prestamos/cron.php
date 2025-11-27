<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
require_once __DIR__ . '/../conexion.php';

$conn = obtenerConexion();

// Contadores
$notificaciones_generadas = 0;
$multas_actualizadas = 0;
$usuarios_suspendidos = 0;

// Configuración
$cfg = $conn->query("SELECT diasAvisoVencimiento, valorMultaDia, multaMaximaSuspendida 
                     FROM configuracion LIMIT 1")->fetch_assoc();
$diasAviso = intval($cfg['diasAvisoVencimiento'] ?? 3);
$valorDia  = intval($cfg['valorMultaDia'] ?? 0);
$multaMax  = intval($cfg['multaMaximaSuspendida'] ?? 50000);
$hoy = date('Y-m-d');

// Préstamos activos
$stmt = $conn->prepare("
    SELECT p.idPrestamo, p.usuario_id, p.libro_id, p.fechaDevolucion, l.titulo 
    FROM prestamos p 
    JOIN libros l ON l.idLibro = p.libro_id 
    WHERE p.estado = 'Activo'
");
$stmt->execute();
$res = $stmt->get_result();

while ($p = $res->fetch_assoc()) {
    $diasRestantes = (int)((strtotime($p['fechaDevolucion']) - strtotime($hoy)) / 86400);

    // Aviso previo
    if ($diasRestantes <= $diasAviso && $diasRestantes >= 0) {
        $msg = "Tu préstamo del libro '".addslashes($p['titulo'])
              ."' vence en $diasRestantes día(s).";
        $ins = $conn->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)");
        $ins->bind_param('is', $p['usuario_id'], $msg);
        $ins->execute();
        $notificaciones_generadas++;
    }

    // Multa atrasada
    if (strtotime($hoy) > strtotime($p['fechaDevolucion'])) {
        $diasTarde = intval((strtotime($hoy) - strtotime($p['fechaDevolucion'])) / 86400);
        $multa = $diasTarde * $valorDia;

        $up = $conn->prepare("UPDATE prestamos SET multa = ? WHERE idPrestamo = ?");
        $up->bind_param('ii', $multa, $p['idPrestamo']);
        $up->execute();
        $multas_actualizadas++;

        $msg = "Tu préstamo del libro '".addslashes($p['titulo'])
              ."' tiene multa de $multa por $diasTarde día(s) de retraso.";
        $ins = $conn->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)");
        $ins->bind_param('is', $p['usuario_id'], $msg);
        $ins->execute();
        $notificaciones_generadas++;
    }
}

// Suspensiones
$usrRes = $conn->query("
    SELECT u.idUsuario, u.nombre, SUM(p.multa) AS totalMulta
    FROM usuarios u 
    LEFT JOIN prestamos p ON p.usuario_id = u.idUsuario 
       AND p.multa > 0 AND p.multaPagada = 0
    GROUP BY u.idUsuario
");

while ($row = $usrRes->fetch_assoc()) {
    $total = intval($row['totalMulta'] ?? 0);

    if ($total >= $multaMax) {
        $susp = $conn->prepare("UPDATE usuarios SET estado = 0 WHERE idUsuario = ?");
        $susp->bind_param('i', $row['idUsuario']);
        $susp->execute();
        $usuarios_suspendidos++;

        $msg = "Tu cuenta fue suspendida por multas acumuladas ($total).";
        $ins = $conn->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)");
        $ins->bind_param('is', $row['idUsuario'], $msg);
        $ins->execute();
        $notificaciones_generadas++;
    }
}
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">

            <h3 class="text-center mb-3"> Notificaciones generadas correctamente</h3>
            <p class="text-center text-muted">Ejecutado: <strong><?php echo date('Y-m-d H:i:s'); ?></strong></p>

            <hr>

            <ul class="list-group mb-4">
                <li class="list-group-item">
                     Notificaciones creadas: <strong><?php echo $notificaciones_generadas; ?></strong>
                </li>
                <li class="list-group-item">
                     Multas actualizadas: <strong><?php echo $multas_actualizadas; ?></strong>
                </li>
                <li class="list-group-item">
                     Usuarios suspendidos: <strong><?php echo $usuarios_suspendidos; ?></strong>
                </li>
            </ul>

            <div class="text-center">
                <a class="btn btn-primary" href="listar.php"> Volver a Préstamos</a>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
