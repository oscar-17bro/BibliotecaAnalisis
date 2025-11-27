<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../configuracion.php';

// Color del menú según el rol
$colorMenu = "bg-primary"; // Por defecto: azul Bootstrap

if (isset($_SESSION['usuario'])) {
    switch ($_SESSION['usuario']['rol_id']) {
        case 1:  // Administrador
            $colorMenu = "bg-dark";   // Negro
            break;
        case 2:  // Bibliotecario
            $colorMenu = "bg-primary"; // Azul
            break;
        case 3:  // Usuario
            $colorMenu = "bg-success"; // Verde
            break;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bibliogestor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark <?php echo $colorMenu; ?> mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo URL_BASE; ?>">Bibliogestor</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navegacion">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navegacion">
      <ul class="navbar-nav me-auto">

        <?php if (isset($_SESSION['usuario'])): ?>
            <!-- Menú visible solo si hay sesión -->
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URL_BASE; ?>/usuarios/listar.php">Usuarios</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="<?php echo URL_BASE; ?>/libros/listar.php">Libros</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="<?php echo URL_BASE; ?>/categorias/listar.php">Categorías</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URL_BASE; ?>/prestamos/listar.php">Préstamos</a>
             </li>

        <?php endif; ?>

      </ul>

      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['usuario'])): ?>
          <li class="nav-item">
            <span class="nav-link">Hola, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="<?php echo URL_BASE; ?>/usuarios/cerrar.php">Cerrar sesión</a>
          </li>

        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URL_BASE; ?>/usuarios/login.php">Iniciar sesión</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
