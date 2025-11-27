<?php
require_once __DIR__ . '/../conexion.php';
session_start();
$conn = obtenerConexion();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave = $_POST['clave'] ?? '';
    if ($correo && $clave) {
        $stmt = $conn->prepare('SELECT idUsuario,nombre,apellido,correo,clave,rol_id FROM usuarios WHERE correo = ?');
        $stmt->bind_param('s',$correo);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 1) {
            $u = $res->fetch_assoc();
            if (password_verify($clave, $u['clave'])) {
                $_SESSION['usuario'] = ['id' => $u['idUsuario'], 'nombre' => $u['nombre'], 'rol_id' => $u['rol_id']];
                header('Location: ../usuarios/listar.php');
                exit;
            } else $error = 'Credenciales inválidas.';
        } else $error = 'Credenciales inválidas.';
    } else $error = 'Correo y clave son obligatorios.';
}
?>
<?php require_once __DIR__ . '/../plantillas/encabezado.php'; ?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card"><div class="card-body">
<h5 class="card-title">Iniciar sesión</h5>
<?php if ($error) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
<form method="post"><div class="mb-3"><label class="form-label">Correo</label><input class="form-control" name="correo"></div>
<div class="mb-3"><label class="form-label">Clave</label><input class="form-control" type="password" name="clave"></div>
<button class="btn btn-primary">Entrar</button></form>
</div></div></div></div>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
