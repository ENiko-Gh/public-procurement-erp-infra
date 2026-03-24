<?php
// includes/header.php
require_once __DIR__ . '/session.php';
requireLogin();
$rol = $_SESSION['user_rol'];
$nombre = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistema de Compras Públicas'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard.php">
      <i class="fa fa-building-columns me-2"></i>ComprasPúblicas
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='dashboard.php')?'active':''; ?>" href="dashboard.php">
            <i class="fa fa-gauge me-1"></i>Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='instituciones.php')?'active':''; ?>" href="instituciones.php">
            <i class="fa fa-landmark me-1"></i>Instituciones
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='proveedores.php')?'active':''; ?>" href="proveedores.php">
            <i class="fa fa-truck me-1"></i>Proveedores
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='procesos.php')?'active':''; ?>" href="procesos.php">
            <i class="fa fa-file-contract me-1"></i>Procesos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='contratos.php')?'active':''; ?>" href="contratos.php">
            <i class="fa fa-handshake me-1"></i>Contratos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='pagos.php')?'active':''; ?>" href="pagos.php">
            <i class="fa fa-money-bill me-1"></i>Pagos
          </a>
        </li>
        <?php if (isAdmin()): ?>
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='usuarios.php')?'active':''; ?>" href="usuarios.php">
            <i class="fa fa-users me-1"></i>Usuarios
          </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='microservicio.php')?'active':''; ?>" href="microservicio.php">
            <i class="fa fa-gear me-1"></i>API/Microservicio
          </a>
        </li>
      </ul>
      <div class="navbar-nav ms-auto">
        <span class="navbar-text me-3">
          <i class="fa fa-circle-user me-1"></i>
          <strong><?php echo htmlspecialchars($nombre); ?></strong>
          <span class="badge ms-1 <?php echo $rol==='administrador'?'bg-danger':($rol==='desarrollador'?'bg-warning text-dark':'bg-success'); ?>">
            <?php echo ucfirst($rol); ?>
          </span>
        </span>
        <a class="btn btn-outline-light btn-sm" href="../controllers/auth_controller.php?action=logout">
          <i class="fa fa-right-from-bracket me-1"></i>Salir
        </a>
      </div>
    </div>
  </div>
</nav>
<!-- CONTENIDO PRINCIPAL -->
<main class="container-fluid py-4 px-4">
<?php
// Flash messages
if (!empty($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="fa fa-circle-check me-2"></i><?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif;
if (!empty($_GET['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="fa fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
