<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../plantillas/encabezado.php';

$conn = obtenerConexion();
$errors = [];

// ROL DEL USUARIO LOGUEADO
$rolSesion = $_SESSION['usuario']['rol_id'] ?? 0;

// ID DEL USUARIO A EDITAR
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { 
    echo '<div class="alert alert-danger">ID inválido</div>';
    require_once __DIR__.'/../plantillas/pie.php'; 
    exit; 
}

// OBTENER DATOS DEL USUARIO
$stmt = $conn->prepare("SELECT idUsuario,nombre,apellido,identificacion,correo,rol_id,telefono,direccion,estado 
                        FROM usuarios WHERE idUsuario = ?");
$stmt->bind_param('i',$id); 
$stmt->execute(); 
$res = $stmt->get_result();

if ($res->num_rows === 0) { 
    echo '<div class="alert alert-danger">Usuario no encontrado</div>'; 
    require_once __DIR__.'/../plantillas/pie.php'; 
    exit; 
}

$user = $res->fetch_assoc(); 
$stmt->close();

// PROCESAR POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $identificacion = trim($_POST['identificacion'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $rol_id = intval($_POST['rol_id'] ?? $user['rol_id']);
    $estado = isset($_POST['estado']) ? 1 : 0;

    // VALIDACIONES
    if ($nombre === '' || $apellido === '' || $identificacion === '' || $correo === '') 
        $errors[] = 'Los campos Nombre, Apellido, Identificación y Correo son obligatorios.';

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) 
        $errors[] = 'Correo inválido.';

    //  BACKEND: Usuario rol 2 NO puede asignar rol 1
    if ($rolSesion == 2 && $rol_id == 1) {
        $errors[] = "No tiene permiso para asignar el rol Administrador.";
    }

    // Validar duplicados
    $stmt = $conn->prepare("SELECT idUsuario FROM usuarios 
                            WHERE (identificacion = ? OR correo = ?) AND idUsuario != ?");
    $stmt->bind_param('ssi', $identificacion, $correo, $id); 
    $stmt->execute(); 
    $stmt->store_result();
    if ($stmt->num_rows > 0) 
        $errors[] = 'Identificación o correo ya registrados por otro usuario.';
    $stmt->close();

    // SI NO HAY ERRORES → ACTUALIZAR
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE usuarios 
            SET nombre=?, apellido=?, identificacion=?, correo=?, rol_id=?, telefono=?, direccion=?, estado=? 
            WHERE idUsuario=?");
        $stmt->bind_param('ssssiisii', 
            $nombre, $apellido, $identificacion, $correo, $rol_id, 
            $telefono, $direccion, $estado, $id
        );

        if ($stmt->execute()) { 
            header('Location: listar.php?msg=Usuario actualizado'); 
            exit; 
        } else { 
            $errors[] = 'Error actualizando: '.$stmt->error; 
        }
    }

} else {
    // CARGAR VALORES DEL USUARIO
    $nombre = $user['nombre']; 
    $apellido = $user['apellido']; 
    $identificacion = $user['identificacion']; 
    $correo = $user['correo'];
    $telefono = $user['telefono']; 
    $direccion = $user['direccion']; 
    $rol_id = $user['rol_id']; 
    $estado = $user['estado'];
}

// OBTENER ROLES
$roles = $conn->query('SELECT idRol,nombre FROM roles ORDER BY nombre');
?>

<div class="card">
<div class="card-body">
<h5>Editar Usuario</h5>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $e) echo "<div>$e</div>"; ?>
</div>
<?php endif; ?>

<form method="post">

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nombre</label>
        <input class="form-control" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Apellido</label>
        <input class="form-control" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>">
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Identificación</label>
        <input class="form-control" name="identificacion" value="<?php echo htmlspecialchars($identificacion); ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Correo</label>
        <input class="form-control" name="correo" value="<?php echo htmlspecialchars($correo); ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Rol</label>
        <select class="form-select" name="rol_id">
            <?php while($r = $roles->fetch_assoc()): ?>

                <?php
                // SI EL USUARIO LOGUEADO ES ROL 2 → OCULTAR ROL 1
                if ($rolSesion == 2 && $r['idRol'] == 1) continue;
                ?>

                <option value="<?php echo $r['idRol']; ?>"
                    <?php echo ($r['idRol']==$rol_id)?'selected':''; ?>>
                    <?php echo htmlspecialchars($r['nombre']); ?>
                </option>

            <?php endwhile; ?>
        </select>
    </div>
</div>

<div class="row">
    <div the="col-md-6 mb-3">
        <label class="form-label">Teléfono</label>
        <input class="form-control" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Dirección</label>
        <input class="form-control" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>">
    </div>
</div>

<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="estado" id="estado" 
        <?php echo $estado ? 'checked' : ''; ?>>
    <label class="form-check-label" for="estado">Activo</label>
</div>

<button class="btn btn-primary">Actualizar</button>
<a class="btn btn-secondary" href="listar.php">Cancelar</a>

</form>
</div>
</div>

<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
