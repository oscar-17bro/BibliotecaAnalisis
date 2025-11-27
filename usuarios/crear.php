<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../plantillas/encabezado.php';

$conn = obtenerConexion();
$errors = [];

// ROL DEL USUARIO LOGUEADO
$rolSesion = $_SESSION['usuario']['rol_id'] ?? 0;

// Valores iniciales
$nombre = $apellido = $identificacion = $correo = $telefono = $direccion = '';
$rol_id = 3; // por defecto rol lector

// PROCESAR POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $identificacion = trim($_POST['identificacion'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $rol_id = intval($_POST['rol_id'] ?? 3);
    $clave = $_POST['clave'] ?? '';

    // Validaciones obligatorias
    if ($nombre === '' || $apellido === '' || $identificacion === '' || $correo === '' || $clave === '') {
        $errors[] = "Los campos Nombre, Apellido, Identificación, Correo y Clave son obligatorios.";
    }

    // Validación email
    if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Correo con formato inválido.";
    }

    // Restricción: si el usuario es rol 2, NO puede crear rol 1
    if ($rolSesion == 2 && $rol_id == 1) {
        $errors[] = "No tiene permiso para asignar el rol Administrador.";
    }

    // Validar duplicados
    $stmt = $conn->prepare("SELECT idUsuario FROM usuarios WHERE identificacion = ? OR correo = ?");
    $stmt->bind_param('ss', $identificacion, $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Identificación o correo ya registrados.";
    }

    $stmt->close();

    // SI NO HAY ERRORES → INSERTAR
    if (empty($errors)) {

        $hash = password_hash($clave, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO usuarios 
            (nombre, apellido, identificacion, correo, clave, rol_id, telefono, direccion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            'sssssiis',
            $nombre, $apellido, $identificacion, $correo,
            $hash, $rol_id, $telefono, $direccion
        );

        if ($stmt->execute()) {
            header("Location: listar.php?msg=Usuario creado");
            exit;
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
    }
}

// Cargar roles
$roles = $conn->query("SELECT idRol,nombre FROM roles ORDER BY nombre");
?>

<div class="card">
<div class="card-body">

<h5>Crear Usuario</h5>

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
        <label class="form-label">Clave</label>
        <input class="form-control" type="password" name="clave">
    </div>
  </div>

  <div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Teléfono</label>
        <input class="form-control" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Dirección</label>
        <input class="form-control" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Rol</label>
        <select class="form-select" name="rol_id">

            <?php while($r = $roles->fetch_assoc()): ?>

                <?php
                //  Ocultar rol 1 para usuarios rol 2
                if ($rolSesion == 2 && $r['idRol'] == 1) continue;
                ?>

                <option value="<?php echo $r['idRol']; ?>"
                    <?php echo ($r['idRol'] == $rol_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($r['nombre']); ?>
                </option>

            <?php endwhile; ?>

        </select>
    </div>
  </div>

  <button class="btn btn-primary">Guardar</button> 
  <a class="btn btn-secondary" href="listar.php">Cancelar</a>

</form>

</div>
</div>

<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
